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
        return view('letterhead.index', compact('config'));
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

    private function getLetterheadConfig()
    {
        $configPath = storage_path('app/letterhead_config.json');
        if (File::exists($configPath)) {
            return json_decode(File::get($configPath), true);
        }
        return $this->getDefaultConfig();
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

    private function createPdfPreviewImage($pdfPath)
    {
        try {
            if (!extension_loaded('imagick')) {
                // Fallback: return null if Imagick is not available
                return null;
            }

            $imagick = new Imagick();
            $imagick->setResolution(150, 150); // Set resolution before reading
            $imagick->readImage($pdfPath . '[0]'); // Read only the first page
            $imagick->setImageFormat('png');
            $imagick->setImageAlphaChannel(Imagick::ALPHACHANNEL_REMOVE);
            $imagick->setImageBackgroundColor('white');
            
            // Resize to A4 proportions (595x842 at 72 DPI equivalent)
            $imagick->resizeImage(595, 842, Imagick::FILTER_LANCZOS, 1);
            
            $previewFilename = 'letterhead_preview.png';
            $previewPath = public_path('letterheads/' . $previewFilename);
            
            $imagick->writeImage($previewPath);
            $imagick->clear();
            $imagick->destroy();
            
            return $previewFilename;
        } catch (\Exception $e) {
            // Log the error and return null
            \Log::warning('Failed to create PDF preview: ' . $e->getMessage());
            return null;
        }
    }

    private function getDefaultConfig()
    {
        return [
            'letterhead_file' => null,
            'letterhead_type' => 'image',
            'preview_image' => null,
            'positions' => [
                ['field' => 'company_name', 'x' => 50, 'y' => 50, 'font_size' => 16, 'font_weight' => 'bold'],
                ['field' => 'company_address', 'x' => 50, 'y' => 80, 'font_size' => 12, 'font_weight' => 'normal'],
                ['field' => 'company_contact', 'x' => 50, 'y' => 110, 'font_size' => 10, 'font_weight' => 'normal'],
                ['field' => 'invoice_no', 'x' => 400, 'y' => 50, 'font_size' => 12, 'font_weight' => 'bold'],
                ['field' => 'invoice_date', 'x' => 400, 'y' => 70, 'font_size' => 12, 'font_weight' => 'normal'],
                ['field' => 'customer_name', 'x' => 50, 'y' => 150, 'font_size' => 12, 'font_weight' => 'bold'],
                ['field' => 'customer_phone', 'x' => 50, 'y' => 170, 'font_size' => 11, 'font_weight' => 'normal'],
                ['field' => 'customer_address', 'x' => 50, 'y' => 190, 'font_size' => 11, 'font_weight' => 'normal'],
                ['field' => 'customer_email', 'x' => 50, 'y' => 210, 'font_size' => 11, 'font_weight' => 'normal'],
                ['field' => 'items_table', 'x' => 50, 'y' => 240, 'font_size' => 11, 'font_weight' => 'normal'],
                ['field' => 'total_section', 'x' => 350, 'y' => 520, 'font_size' => 12, 'font_weight' => 'normal'],
                ['field' => 'warranty_section', 'x' => 50, 'y' => 620, 'font_size' => 9, 'font_weight' => 'normal'],
            ]
        ];
    }
}