<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Imagick;

class LetterheadController extends Controller
{
    public function index()
    {
        $config = $this->getLetterheadConfig();

        // Get the latest order for testing
        $latestOrder = \App\Models\Order::latest()->first();
        $testOrderId = $latestOrder ? $latestOrder->id : null;

        return view('letterhead.index', compact('config', 'testOrderId'));
    }

    public function uploadLetterhead(Request $request)
    {
        $request->validate([
            'letterhead' => 'required|mimes:jpeg,png,jpg,gif,pdf|max:5120'
        ]);

        if ($request->hasFile('letterhead')) {
            // Delete old letterhead if exists
            $this->deleteOldLetterhead();

            $file = $request->file('letterhead');
            $extension = $file->getClientOriginalExtension();
            $filename = 'letterhead.' . $extension;
            $file->move(public_path('letterheads'), $filename);

            // If PDF, create a preview image for positioning
            $previewImage = null;
            if (strtolower($extension) === 'pdf') {
                $previewImage = $this->createPdfPreviewImage(public_path('letterheads/' . $filename));

                // If preview generation failed, log it but continue (positioning still works)
                if (!$previewImage) {
                    \Log::warning('PDF preview generation failed during upload - positioning canvas will work without preview');
                }
            }

            // Save letterhead info to config
            $config = [
                'letterhead_file' => $filename,
                'letterhead_type' => strtolower($extension) === 'pdf' ? 'pdf' : 'image',
                'preview_image' => $previewImage,
                'uploaded_at' => now()->toISOString()
            ];

            $this->saveLetterheadConfig($config);

            return response()->json(['success' => true, 'filename' => $filename, 'type' => $config['letterhead_type']]);
        }

        return response()->json(['success' => false, 'message' => 'No file uploaded']);
    }

    public function savePositions(Request $request)
    {
        $positions = $request->validate([
            'positions' => 'required|array',
            'positions.*.field' => 'required|string',
            'positions.*.x' => 'required|numeric',
            'positions.*.y' => 'required|numeric',
            'positions.*.font_size' => 'nullable|numeric',
            'positions.*.font_weight' => 'nullable|string',
        ]);

        $config = $this->getLetterheadConfig();
        $config['positions'] = $positions['positions'];
        $this->saveLetterheadConfig($config);

        return response()->json(['success' => true]);
    }

    public function getPositions()
    {
        $config = $this->getLetterheadConfig();
        return response()->json($config['positions'] ?? []);
    }

    public function saveToggles(Request $request)
    {
        $toggles = $request->validate([
            'toggles' => 'required|array',
            'toggles.*' => 'boolean',
        ]);

        $config = $this->getLetterheadConfig();
        $config['element_toggles'] = $toggles['toggles'];
        $this->saveLetterheadConfig($config);

        return response()->json(['success' => true]);
    }

    public function getToggles()
    {
        $config = $this->getLetterheadConfig();
        return response()->json($config['element_toggles'] ?? []);
    }

    public function saveItemsAlignment(Request $request)
    {
        $alignment = $request->validate([
            'alignment' => 'required|array',
            'alignment.start_x' => 'required|numeric|min:0|max:400',
            'alignment.end_x' => 'required|numeric|min:200|max:595',
            'alignment.width' => 'required|numeric|min:300|max:570',
        ]);

        $config = $this->getLetterheadConfig();
        $config['items_alignment'] = $alignment['alignment'];
        $this->saveLetterheadConfig($config);

        return response()->json(['success' => true, 'message' => 'Items alignment saved successfully']);
    }

    public function regeneratePreview()
    {
        $config = $this->getLetterheadConfig();

        if (!isset($config['letterhead_file']) || $config['letterhead_type'] !== 'pdf') {
            return response()->json(['success' => false, 'message' => 'No PDF letterhead found']);
        }

        $pdfPath = public_path('letterheads/' . $config['letterhead_file']);
        if (!file_exists($pdfPath)) {
            return response()->json(['success' => false, 'message' => 'PDF file not found']);
        }

        $previewImage = $this->createPdfPreviewImage($pdfPath);

        if ($previewImage) {
            $config['preview_image'] = $previewImage;
            $config['updated_at'] = now()->toISOString();
            $this->saveLetterheadConfig($config);

            return response()->json(['success' => true, 'preview_image' => $previewImage]);
        } else {
            return response()->json(['success' => false, 'message' => 'Failed to generate preview']);
        }
    }

    private function getLetterheadConfig()
    {
        $configPath = storage_path('app/letterhead_config.json');
        if (File::exists($configPath)) {
            return json_decode(File::get($configPath), true);
        }
        return $this->getDefaultConfig();
    }

    private function createPdfPreviewUsingCommandLine($pdfPath)
    {
        try {
            // Check if magick command is available (ImageMagick 7+)
            $magickPath = trim(shell_exec('which magick'));
            if (empty($magickPath)) {
                // Fallback to convert command (ImageMagick 6)
                $magickPath = trim(shell_exec('which convert'));
                if (empty($magickPath)) {
                    \Log::info('ImageMagick command not found - PDF preview unavailable');
                    return null;
                }
            }

            if (!file_exists($pdfPath)) {
                \Log::warning('PDF file not found: ' . $pdfPath);
                return null;
            }

            $previewFilename = 'letterhead_preview_' . time() . '.png';
            $previewPath = public_path('letterheads/' . $previewFilename);

            // Ensure letterheads directory exists
            $letterheadDir = public_path('letterheads');
            if (!is_dir($letterheadDir)) {
                mkdir($letterheadDir, 0755, true);
            }

            // Use ImageMagick command to create preview
            $command = sprintf(
                '%s -density 150 -quality 95 %s[0] -resize 595x842! -background white -alpha remove %s 2>&1',
                escapeshellcmd($magickPath),
                escapeshellarg($pdfPath),
                escapeshellarg($previewPath)
            );

            $output = shell_exec($command);

            if (file_exists($previewPath) && filesize($previewPath) > 0) {
                \Log::info('PDF preview created successfully using command line: ' . $previewFilename);
                return $previewFilename;
            } else {
                // More detailed error logging
                $errorMsg = 'Failed to create PDF preview using command line.';
                if (!empty($output)) {
                    $errorMsg .= ' Output: ' . $output;
                }
                if (strpos($output, 'no decode delegate') !== false) {
                    $errorMsg .= ' - This usually means Ghostscript is not properly installed or configured with ImageMagick.';
                }
                \Log::warning($errorMsg);
                return null;
            }
        } catch (\Exception $e) {
            \Log::error('Failed to create PDF preview using command line: ' . $e->getMessage());
            return null;
        }
    }

    private function saveLetterheadConfig($config)
    {
        $configPath = storage_path('app/letterhead_config.json');
        File::put($configPath, json_encode($config, JSON_PRETTY_PRINT));
    }

    private function deleteOldLetterhead()
    {
        $letterheadDir = public_path('letterheads');
        if (File::isDirectory($letterheadDir)) {
            File::cleanDirectory($letterheadDir);
        }
    }

    private function checkPdfProcessingCapability()
    {
        // Check if Imagick extension is available
        if (extension_loaded('imagick')) {
            try {
                $imagick = new Imagick();
                $formats = $imagick->queryFormats('PDF');
                $imagick->destroy();
                if (!empty($formats)) {
                    return ['status' => true, 'method' => 'php_imagick'];
                }
            } catch (\Exception $e) {
                // Fall through to command line check
            }
        }

        // Check command line ImageMagick
        $magickPath = trim(shell_exec('which magick'));
        if (empty($magickPath)) {
            $magickPath = trim(shell_exec('which convert'));
        }

        if (!empty($magickPath)) {
            return ['status' => true, 'method' => 'command_line'];
        }

        return ['status' => false, 'method' => 'none'];
    }

    private function createPdfPreviewImage($pdfPath)
    {
        try {
            if (!extension_loaded('imagick')) {
                // Try using command-line ImageMagick as fallback
                return $this->createPdfPreviewUsingCommandLine($pdfPath);
            }

            if (!file_exists($pdfPath)) {
                \Log::warning('PDF file not found: ' . $pdfPath);
                return null;
            }

            $imagick = new Imagick();

            // Set high resolution for better quality, then scale down for exact dimensions
            $imagick->setResolution(150, 150);

            // Read only the first page of the PDF
            $imagick->readImage($pdfPath . '[0]');

            // Set format to PNG for best quality
            $imagick->setImageFormat('png');
            $imagick->setImageAlphaChannel(Imagick::ALPHACHANNEL_REMOVE);
            $imagick->setImageBackgroundColor('white');

            // Get original dimensions
            $originalWidth = $imagick->getImageWidth();
            $originalHeight = $imagick->getImageHeight();

            // Always resize to exact A4 dimensions for consistent canvas
            $imagick->resizeImage(595, 842, Imagick::FILTER_LANCZOS, 1, true);

            // Enhance image quality
            $imagick->enhanceImage();
            $imagick->setImageCompressionQuality(95);

            $previewFilename = 'letterhead_preview_' . time() . '.png';
            $previewPath = public_path('letterheads/' . $previewFilename);

            // Ensure letterheads directory exists
            $letterheadDir = public_path('letterheads');
            if (!is_dir($letterheadDir)) {
                mkdir($letterheadDir, 0755, true);
            }

            // Write the preview
            $result = $imagick->writeImage($previewPath);

            // Clean up
            $imagick->clear();
            $imagick->destroy();

            if ($result && file_exists($previewPath)) {
                \Log::info('PDF preview created successfully: ' . $previewFilename);
                return $previewFilename;
            } else {
                \Log::warning('Failed to write PDF preview file');
                return null;
            }
        } catch (\Exception $e) {
            $errorMsg = 'Failed to create PDF preview using PHP Imagick: ' . $e->getMessage();
            if (strpos($e->getMessage(), 'no decode delegate') !== false) {
                $errorMsg .= ' - This usually means Ghostscript is not properly installed or configured with ImageMagick.';
            }
            \Log::error($errorMsg);
            return null;
        }
    }    private function getDefaultConfig()
    {
        return [
            'letterhead_file' => null,
            'letterhead_type' => 'image',
            'preview_image' => null,
            'positions' => [
                ['field' => 'product_name', 'x' => 50, 'y' => 130, 'font_size' => 10, 'font_weight' => 'bold'],
                ['field' => 'customer_name', 'x' => 50, 'y' => 150, 'font_size' => 10, 'font_weight' => 'bold'],
                ['field' => 'customer_phone', 'x' => 50, 'y' => 170, 'font_size' => 10, 'font_weight' => 'normal'],
                ['field' => 'customer_address', 'x' => 50, 'y' => 190, 'font_size' => 10, 'font_weight' => 'normal'],
                ['field' => 'customer_email', 'x' => 50, 'y' => 210, 'font_size' => 10, 'font_weight' => 'normal'],
                ['field' => 'items_table', 'x' => 50, 'y' => 240, 'font_size' => 10, 'font_weight' => 'normal'],
                ['field' => 'total_section', 'x' => 350, 'y' => 520, 'font_size' => 10, 'font_weight' => 'normal'],
                ['field' => 'warranty_section', 'x' => 50, 'y' => 620, 'font_size' => 9, 'font_weight' => 'normal'],
            ],
            'element_toggles' => [
                'customer_name' => true,
                'customer_phone' => true,
                'customer_address' => true,
                'customer_email' => true,
            ]
        ];
    }
}
