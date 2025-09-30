@extends('layouts.tabler')

@section('content')
<div class="page-body">
    <div class="container-fluid">
        <x-alert/>

        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">📄 Letterhead Configuration</h3>
                        <div class="card-actions">
                            <a href="{{ route('orders.create') }}" class="btn btn-outline-primary">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <path d="M9 14l-4 -4l4 -4"/>
                                    <path d="M5 10h11a4 4 0 1 1 0 8h-1"/>
                                </svg>
                                Back to POS
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Upload Section -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h4>1. Upload Letterhead Background</h4>
                                <form id="letterheadForm" enctype="multipart/form-data">
                                    @csrf
                                    <div class="mb-3">
                                        <label class="form-label">Select Letterhead File (PNG, JPG, JPEG, PDF)</label>
                                        <input type="file" class="form-control" name="letterhead" accept="image/*,.pdf" required>
                                        <div class="form-text">
                                            <strong>Images:</strong> Recommended size: 595px x 842px (A4 at 72 DPI)<br>
                                            <strong>PDF:</strong> Single page A4 PDF letterhead (max 5MB)
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-primary">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                            <path d="M14 3v4a1 1 0 0 0 1 1h4"/>
                                            <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z"/>
                                            <path d="M12 11l0 6"/>
                                            <path d="M9 14l3 -3l3 3"/>
                                        </svg>
                                        Upload Letterhead
                                    </button>
                                </form>
                            </div>
                            <div class="col-md-6">
                                <h4>2. Current Letterhead</h4>
                                <div id="currentLetterhead">
                                    @if(isset($config['letterhead_file']) && $config['letterhead_file'])
                                        @php
                                            $fileExtension = pathinfo($config['letterhead_file'], PATHINFO_EXTENSION);
                                            $isPdf = strtolower($fileExtension) === 'pdf';
                                        @endphp

                                        @if($isPdf)
                                            <div class="pdf-preview" style="border: 1px solid #ddd; padding: 10px; text-align: center; background: #f8f9fa;">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-lg text-danger mb-2" width="48" height="48" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                    <path d="M14 3v4a1 1 0 0 0 1 1h4"/>
                                                    <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z"/>
                                                    <path d="M9 9l1 0"/>
                                                    <path d="M9 13l6 0"/>
                                                    <path d="M9 17l6 0"/>
                                                </svg>
                                                <p class="mb-2"><strong>PDF Letterhead</strong></p>
                                                <a href="{{ asset('letterheads/' . $config['letterhead_file']) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                        <path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0"/>
                                                        <path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6"/>
                                                    </svg>
                                                    View PDF
                                                </a>
                                            </div>
                                        @else
                                            <img src="{{ asset('letterheads/' . $config['letterhead_file']) }}" alt="Current Letterhead" style="max-width: 100%; max-height: 200px; border: 1px solid #ddd;">
                                        @endif
                                        <p class="text-muted mt-2">File: {{ $config['letterhead_file'] }} ({{ $isPdf ? 'PDF' : 'Image' }})</p>
                                    @else
                                        <div class="empty">
                                            <div class="empty-img"><img src="{{ asset('static/illustrations/undraw_printing_invoices_5r4r.svg') }}" height="128" alt=""></div>
                                            <p class="empty-title">No letterhead uploaded</p>
                                            <p class="empty-subtitle text-muted">Upload a letterhead image or PDF to get started</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <hr>

                        <!-- Positioning Section -->
                        <div class="row">
                            <div class="col-12">
                                <h4>3. Position Text Elements</h4>
                                <p class="text-muted mb-3">Click and drag the elements on the preview to position them precisely on your letterhead.</p>

                                @if(isset($config['letterhead_file']) && $config['letterhead_file'])
                                <div class="position-editor-container">
                                    <div class="row">
                                        <div class="col-md-8">
                                            <div class="position-editor" id="positionEditor">
                                                @php
                                                    $letterheadType = $config['letterhead_type'] ?? 'image';
                                                    $previewImage = $config['preview_image'] ?? null;

                                                    // Always show PDF/image when letterhead is uploaded
                                                    if ($letterheadType === 'pdf') {
                                                        // For PDF, use preview if available, otherwise use PDF directly
                                                        if ($previewImage && file_exists(public_path('letterheads/' . $previewImage))) {
                                                            $positioningImage = 'letterheads/' . $previewImage;
                                                        } else {
                                                            // Fallback to original PDF file
                                                            $positioningImage = 'letterheads/' . $config['letterhead_file'];
                                                        }
                                                        $showImage = true;
                                                    } else {
                                                        // For regular images
                                                        $positioningImage = 'letterheads/' . $config['letterhead_file'];
                                                        $showImage = true;
                                                    }
                                                @endphp

                                @if($showImage || isset($config['letterhead_file']))
                                    <div class="pdf-canvas-container" style="position: relative; display: inline-block; border: 3px solid #007bff; background: #fff; box-shadow: 0 8px 24px rgba(0,0,0,0.2); border-radius: 8px; overflow: visible;">
                                        <!-- Canvas Accuracy Indicator -->
                                        <div class="canvas-indicator" style="position: absolute; top: -35px; left: 0; background: {{ $letterheadType === 'pdf' ? '#28a745' : '#007bff' }}; color: white; padding: 4px 8px; border-radius: 4px; font-size: 10px; font-weight: bold; z-index: 15;">
                                            {{ $letterheadType === 'pdf' ? '� PDF Canvas' : '🖼️ Image Canvas' }} (595×842px)
                                        </div>

                                        <div id="letterheadImage"
                                             style="width: 595px; height: 842px;
                                                    background-image: url('{{ asset($positioningImage) }}?t={{ time() }}');
                                                    background-size: cover;
                                                    background-repeat: no-repeat;
                                                    background-position: center center;
                                                    position: relative;
                                                    overflow: visible;
                                                    border-radius: 5px;
                                                    border: 1px solid rgba(0,123,255,0.3);">

                                            <!-- PDF Grid Overlay for Precise Positioning -->
                                            <div class="positioning-grid" id="positioningGrid" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;
                                                        background-image:
                                                            linear-gradient(rgba(0,123,255,0.15) 1px, transparent 1px),
                                                            linear-gradient(90deg, rgba(0,123,255,0.15) 1px, transparent 1px),
                                                            linear-gradient(rgba(255,0,0,0.1) 1px, transparent 1px),
                                                            linear-gradient(90deg, rgba(255,0,0,0.1) 1px, transparent 1px);
                                                        background-size: 25px 25px, 25px 25px, 5px 5px, 5px 5px;
                                                        opacity: 0.3;
                                                        z-index: 1;
                                                        pointer-events: none;
                                                        transition: opacity 0.3s ease;"></div>

                                            <!-- Ruler markers -->
                                            <div class="ruler-top" style="position: absolute; top: -20px; left: 0; right: 0; height: 20px; background: #f8f9fa; border: 1px solid #dee2e6; font-size: 10px; z-index: 10;">
                                                @for($i = 0; $i <= 595; $i += 50)
                                                    <div style="position: absolute; left: {{ $i }}px; top: 5px; font-size: 8px; color: #666;">{{ $i }}</div>
                                                    <div style="position: absolute; left: {{ $i }}px; top: 0; width: 1px; height: 20px; background: #999;"></div>
                                                @endfor
                                            </div>

                                            <div class="ruler-left" style="position: absolute; left: -20px; top: 0; bottom: 0; width: 20px; background: #f8f9fa; border: 1px solid #dee2e6; font-size: 10px; z-index: 10;">
                                                @for($i = 0; $i <= 842; $i += 50)
                                                    <div style="position: absolute; top: {{ $i }}px; left: 2px; font-size: 8px; color: #666; writing-mode: vertical-rl;">{{ $i }}</div>
                                                    <div style="position: absolute; top: {{ $i }}px; left: 0; width: 20px; height: 1px; background: #999;"></div>
                                                @endfor
                                            </div>
                                        </div>
                                    </div>

                                    <div class="canvas-controls mt-2 mb-2" style="display: flex; gap: 10px; align-items: center;">
                                        <button type="button" class="btn btn-sm btn-outline-primary" id="toggleGrid">
                                            <i class="fas fa-th"></i> Toggle Grid
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-success" id="toggleRulers">
                                            <i class="fas fa-ruler"></i> Toggle Rulers
                                        </button>
                                        <span class="text-muted" style="font-size: 12px;">💡 Hover canvas to enhance grid visibility</span>
                                    </div>

                                    <div class="alert alert-success mt-2">
                                        <strong>{{ $letterheadType === 'pdf' ? '🎯 PDF Letterhead Canvas Active:' : '🖼️ Image Letterhead Canvas Active:' }}</strong>
                                        Your uploaded {{ $letterheadType === 'pdf' ? 'PDF' : 'image' }} is displayed as the positioning canvas.
                                        Elements positioned here will appear in identical locations on the final PDF invoice.
                                        @if($letterheadType === 'pdf' && !$previewImage)
                                            <br><small class="text-warning">⚠️ PDF preview generation failed. Canvas positioning still works perfectly - your final invoices will use the PDF background correctly.</small>
                                            <br><button type="button" class="btn btn-sm btn-outline-primary mt-2" id="regeneratePreview">
                                                <i class="fas fa-sync"></i> Regenerate PDF Preview
                                            </button>
                                        @endif
                                    </div>
                                                @else
                                                    {{-- PDF without preview - show blank canvas --}}
                                                    <div id="letterheadImage"
                                                         style="width: 595px; height: 842px; background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); border: 2px solid #dee2e6; position: relative; overflow: hidden;">

                                                        {{-- Grid pattern for positioning guide --}}
                                                        <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;
                                                                    background-image:
                                                                        linear-gradient(rgba(0,0,0,.1) 1px, transparent 1px),
                                                                        linear-gradient(90deg, rgba(0,0,0,.1) 1px, transparent 1px);
                                                                    background-size: 50px 50px; opacity: 0.3; z-index: 1;"></div>

                                                        {{-- Center guidance --}}
                                                        <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);
                                                                    text-align: center; color: #6c757d; z-index: 2;">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-lg mb-2" width="48" height="48" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                                <path d="M14 3v4a1 1 0 0 0 1 1h4"/>
                                                                <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z"/>
                                                                <path d="M9 9l1 0"/>
                                                                <path d="M9 13l6 0"/>
                                                                <path d="M9 17l6 0"/>
                                                            </svg>
                                                            <div><strong>PDF Letterhead Canvas</strong></div>
                                                            <div>A4 Size (595 x 842 px)</div>
                                                            <small>Drag elements to position them<br>Your PDF background will show in final invoice</small>
                                                        </div>

                                                        {{-- Corner markers --}}
                                                        <div style="position: absolute; top: 10px; left: 10px; width: 20px; height: 20px;
                                                                    border-top: 2px solid #007bff; border-left: 2px solid #007bff; z-index: 2;"></div>
                                                        <div style="position: absolute; top: 10px; right: 10px; width: 20px; height: 20px;
                                                                    border-top: 2px solid #007bff; border-right: 2px solid #007bff; z-index: 2;"></div>
                                                        <div style="position: absolute; bottom: 10px; left: 10px; width: 20px; height: 20px;
                                                                    border-bottom: 2px solid #007bff; border-left: 2px solid #007bff; z-index: 2;"></div>
                                                        <div style="position: absolute; bottom: 10px; right: 10px; width: 20px; height: 20px;
                                                                    border-bottom: 2px solid #007bff; border-right: 2px solid #007bff; z-index: 2;"></div>
                                                    </div>

                                                    <div class="alert alert-warning mt-2">
                                                        <small><strong>PDF Letterhead Positioning:</strong> Use the grid above to position elements. Your PDF letterhead will be used as the background in the final invoice. Note: Preview generation requires ImageMagick extension - positioning works regardless.</small>
                                                    </div>
                                                @endif

                                                <!-- Draggable elements will be added here via JavaScript -->
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h5>Element Properties</h5>
                                                </div>
                                                <div class="card-body">
                                                    <div id="elementProperties">
                                                        <p class="text-muted">Select an element to edit its properties</p>
                                                    </div>
                                                    <button type="button" class="btn btn-success w-100 mt-3" id="savePositions">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                            <path d="M6 4h10l4 4v10a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-16a2 2 0 0 1 2 -2"/>
                                                            <path d="M12 14m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0"/>
                                                            <path d="M14 4l0 4l-6 0l0 -4"/>
                                                        </svg>
                                                        Save Positions
                                                    </button>
                                                </div>
                                            </div>

                                            <!-- Test PDF Button -->
                                            <div class="mt-3">
                                                @if($testOrderId)
                                                <a href="{{ route('orders.download-pdf-bill', $testOrderId) }}" class="btn btn-info w-100" target="_blank">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                        <path d="M14 3v4a1 1 0 0 0 1 1h4"/>
                                                        <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z"/>
                                                        <path d="M9 9l1 0"/>
                                                        <path d="M9 13l6 0"/>
                                                        <path d="M9 17l6 0"/>
                                                    </svg>
                                                    Test PDF with Current Settings (Order #{{ $testOrderId }})
                                                </a>
                                                @else
                                                <div class="alert alert-warning">
                                                    <strong>No orders available for testing.</strong><br>
                                                    Create an order first to test PDF generation.
                                                    <a href="{{ route('orders.create') }}" class="btn btn-sm btn-primary mt-2">Create Order</a>
                                                </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @else
                                <div class="position-editor-container">
                                    <div class="row">
                                        <div class="col-md-8">
                                            <div class="alert alert-warning mb-3">
                                                <strong>Preview Mode:</strong> Upload a letterhead above to see it as background. For now, you can position elements on this blank canvas.
                                            </div>
                                            <div class="position-editor" id="positionEditor">
                                                {{-- Default blank canvas for positioning --}}
                                                <div id="letterheadImage"
                                                     style="width: 595px; height: 842px; background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); border: 2px solid #dee2e6; position: relative; overflow: hidden;">

                                                    {{-- Grid pattern for positioning guide --}}
                                                    <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;
                                                                background-image:
                                                                    linear-gradient(rgba(0,0,0,.1) 1px, transparent 1px),
                                                                    linear-gradient(90deg, rgba(0,0,0,.1) 1px, transparent 1px);
                                                                background-size: 50px 50px; opacity: 0.3; z-index: 1;"></div>

                                                    {{-- Center guidance --}}
                                                    <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);
                                                                text-align: center; color: #6c757d; z-index: 2;">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-lg mb-2" width="48" height="48" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                            <path d="M14 3v4a1 1 0 0 0 1 1h4"/>
                                                            <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z"/>
                                                            <path d="M9 9l1 0"/>
                                                            <path d="M9 13l6 0"/>
                                                            <path d="M9 17l6 0"/>
                                                        </svg>
                                                        <div><strong>Default Positioning Canvas</strong></div>
                                                        <div>A4 Size (595 x 842 px)</div>
                                                        <small>Position elements, then upload letterhead later<br>Your settings will be preserved</small>
                                                    </div>

                                                    {{-- Corner markers --}}
                                                    <div style="position: absolute; top: 10px; left: 10px; width: 20px; height: 20px;
                                                                border-top: 2px solid #007bff; border-left: 2px solid #007bff; z-index: 2;"></div>
                                                    <div style="position: absolute; top: 10px; right: 10px; width: 20px; height: 20px;
                                                                border-top: 2px solid #007bff; border-right: 2px solid #007bff; z-index: 2;"></div>
                                                    <div style="position: absolute; bottom: 10px; left: 10px; width: 20px; height: 20px;
                                                                border-bottom: 2px solid #007bff; border-left: 2px solid #007bff; z-index: 2;"></div>
                                                    <div style="position: absolute; bottom: 10px; right: 10px; width: 20px; height: 20px;
                                                                border-bottom: 2px solid #007bff; border-right: 2px solid #007bff; z-index: 2;"></div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h5>Element Properties</h5>
                                                </div>
                                                <div class="card-body" id="elementProperties">
                                                    <p class="text-muted">Click on any element to edit its properties</p>
                                                </div>
                                            </div>

                                            <div class="mt-3">
                                                <button id="savePositions" class="btn btn-success w-100">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                        <path d="M6 4h10l4 4v10a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2"/>
                                                        <path d="M12 14m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0"/>
                                                        <path d="M14 4l0 4l-6 0l0 -4"/>
                                                    </svg>
                                                    Save Element Positions
                                                </button>

                                                @if($testOrderId)
                                                <a href="{{ route('orders.download-pdf-bill', $testOrderId) }}" class="btn btn-info w-100 mt-2" target="_blank">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                        <path d="M14 3v4a1 1 0 0 0 1 1h4"/>
                                                        <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z"/>
                                                        <path d="M9 9l1 0"/>
                                                        <path d="M9 13l6 0"/>
                                                        <path d="M9 17l6 0"/>
                                                    </svg>
                                                    Test PDF (Order #{{ $testOrderId }})
                                                </a>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.position-editor {
    position: relative;
    background: #f8f9fa;
    display: inline-block;
    padding: 25px;
}

.pdf-canvas-container {
    box-shadow: 0 8px 24px rgba(0,0,0,0.15) !important;
    border-radius: 8px;
    overflow: visible;
}

.positioning-grid {
    transition: opacity 0.3s ease;
}

.pdf-canvas-container:hover .positioning-grid {
    opacity: 0.6 !important;
}

.ruler-top, .ruler-left {
    font-family: 'Courier New', monospace;
    user-select: none;
    pointer-events: none;
    transition: all 0.3s ease;
}

.canvas-indicator {
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% { opacity: 1; }
    50% { opacity: 0.7; }
    100% { opacity: 1; }
}

.btn.active {
    background-color: #007bff !important;
    color: white !important;
    border-color: #007bff !important;
}

#letterheadImage {
    box-shadow: inset 0 0 0 1px rgba(0,123,255,0.2);
}

.draggable-element {
    position: absolute;
    background: linear-gradient(135deg, rgba(0, 123, 255, 0.95) 0%, rgba(0, 86, 179, 0.9) 100%);
    border: 2px solid #007bff;
    padding: 6px 10px;
    cursor: move;
    font-family: Arial, sans-serif;
    font-weight: 600;
    color: white;
    min-width: 90px;
    min-height: 28px;
    user-select: none;
    border-radius: 4px;
    box-shadow:
        0 3px 6px rgba(0,0,0,0.2),
        0 1px 3px rgba(0,123,255,0.3),
        inset 0 1px 0 rgba(255,255,255,0.2);
    z-index: 1002;
    transition: all 0.2s ease;
    backdrop-filter: blur(2px);
    text-shadow: 0 1px 1px rgba(0,0,0,0.3);
    font-size: 10px;
    line-height: 1.1;
}

.draggable-element:hover {
    background: linear-gradient(135deg, rgba(0, 123, 255, 1) 0%, rgba(0, 86, 179, 0.95) 100%);
    transform: scale(1.05);
    box-shadow:
        0 6px 12px rgba(0,0,0,0.3),
        0 3px 6px rgba(0,123,255,0.4),
        inset 0 1px 0 rgba(255,255,255,0.3);
}

.draggable-element.dragging {
    transform: rotate(2deg) scale(1.1);
    box-shadow:
        0 8px 16px rgba(0,0,0,0.4),
        0 4px 8px rgba(0,123,255,0.5);
    z-index: 1001;
}

.draggable-element.selected {
    border-color: #dc3545;
    background: rgba(220, 53, 69, 0.15);
    box-shadow: 0 4px 12px rgba(220, 53, 69, 0.3);
}

.draggable-element .element-label {
    font-size: 8px;
    color: rgba(255, 255, 255, 0.9);
    margin-bottom: 2px;
    font-weight: bold;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    text-shadow: 0 1px 2px rgba(0,0,0,0.5);
    display: block;
}

.draggable-element .element-content {
    font-size: 10px;
    color: white;
    line-height: 1.1;
    margin-bottom: 1px;
    text-shadow: 0 1px 2px rgba(0,0,0,0.3);
    display: block;
    max-width: 150px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.position-coordinates {
    position: absolute;
    top: -20px;
    right: -2px;
    font-size: 8px;
    color: #495057;
    background: rgba(255, 255, 255, 0.98);
    padding: 1px 4px;
    border: 1px solid #007bff;
    border-radius: 3px;
    font-weight: bold;
    font-family: 'Courier New', monospace;
    box-shadow: 0 1px 3px rgba(0,0,0,0.2);
    z-index: 1003;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let selectedElement = null;
    let isDragging = false;
    let dragOffset = { x: 0, y: 0 };

    // Form submission for letterhead upload
    document.getElementById('letterheadForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);

        fetch('{{ route("letterhead.upload") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast('Letterhead uploaded successfully!', 'success');
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            } else {
                showToast('Error uploading letterhead: ' + (data.message || 'Unknown error'), 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Error uploading letterhead', 'error');
        });
    });

    // Always initialize draggable elements
    console.log('Config data:', @json($config));
    console.log('Initializing draggable elements...');

    // Check if letterhead image loaded properly
    const letterheadContainer = document.getElementById('letterheadImage');
    if (letterheadContainer) {
        const bgImage = letterheadContainer.style.backgroundImage;
        if (bgImage && bgImage !== 'none') {
            // Test if background image loads
            const testImg = new Image();
            testImg.onload = function() {
                console.log('Letterhead background loaded successfully');
            };
            testImg.onerror = function() {
                console.log('Letterhead background failed to load, showing placeholder');
                showPlaceholderCanvas();
            };
            // Extract URL from background-image CSS property
            const urlMatch = bgImage.match(/url\(['"]?(.*?)['"]?\)/);
            if (urlMatch) {
                testImg.src = urlMatch[1];
            }
        } else {
            showPlaceholderCanvas();
        }
    }

    initializeDraggableElements();

    function showPlaceholderCanvas() {
        const container = document.getElementById('letterheadImage');
        if (container) {
            container.style.background = `
                linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%),
                repeating-linear-gradient(45deg, transparent, transparent 10px, rgba(0,123,255,0.1) 10px, rgba(0,123,255,0.1) 20px)
            `;

            // Add placeholder content if not already present
            if (!container.querySelector('.canvas-placeholder')) {
                const placeholder = document.createElement('div');
                placeholder.className = 'canvas-placeholder';
                placeholder.style.cssText = `
                    position: absolute;
                    top: 50%;
                    left: 50%;
                    transform: translate(-50%, -50%);
                    text-align: center;
                    color: #6c757d;
                    font-size: 14px;
                    z-index: 2;
                    pointer-events: none;
                `;
                placeholder.innerHTML = `
                    <div style="font-size: 48px; margin-bottom: 10px;">📄</div>
                    <div><strong>PDF Letterhead Canvas</strong></div>
                    <div>A4 Size (595 × 842 px)</div>
                    <div style="font-size: 12px; margin-top: 8px;">
                        Your letterhead background will appear here<br>
                        Positioning coordinates remain accurate
                    </div>
                `;
                container.appendChild(placeholder);
            }
        }
    }

    function initializeDraggableElements() {
        const editor = document.getElementById('positionEditor');
        const container = document.getElementById('letterheadImage');

        console.log('Editor:', editor);
        console.log('Container:', container);
        console.log('Container tagName:', container ? container.tagName : 'null');

        if (!container) {
            console.error('No letterheadImage container found!');
            return;
        }

        if (container.tagName === 'IMG') {
            // Wait for image to load to get correct dimensions
            container.onload = function() {
                console.log('Image loaded, creating elements...');
                createDraggableElements();
            };

            if (container.complete) {
                console.log('Image already complete, creating elements...');
                createDraggableElements();
            }
        } else {
            // For div containers (PDF without preview), create elements immediately
            console.log('DIV container found, creating elements immediately...');
            createDraggableElements();
        }
    }

    function createDraggableElements() {
        const positions = @json($config['positions'] ?? []);
        const editor = document.getElementById('positionEditor');
        const container = document.getElementById('letterheadImage');

        console.log('Creating draggable elements...');
        console.log('Positions data:', positions);
        console.log('Editor element:', editor);
        console.log('Container element:', container);

        // Essential elements to always include
        const essentialElements = [
            {field: 'invoice_no', x: 400, y: 50, font_size: 14, font_weight: 'bold'},
            {field: 'invoice_date', x: 400, y: 80, font_size: 14, font_weight: 'normal'},
            {field: 'product_name', x: 50, y: 130, font_size: 13, font_weight: 'bold'},
            {field: 'customer_name', x: 50, y: 150, font_size: 14, font_weight: 'bold'},
            {field: 'customer_phone', x: 50, y: 170, font_size: 13, font_weight: 'normal'},
            {field: 'payment_details', x: 50, y: 580, font_size: 13, font_weight: 'normal'}
        ];

        let elementsToCreate = positions && positions.length > 0 ? positions : essentialElements;

        // Always ensure we have the essential elements
        essentialElements.forEach(essential => {
            const exists = elementsToCreate.find(pos => pos.field === essential.field);
            if (!exists) {
                elementsToCreate.push(essential);
            }
        });

        console.log('Elements to create:', elementsToCreate);

        elementsToCreate.forEach((pos, index) => {
            console.log(`Creating element ${index + 1}:`, pos);
            createDraggableElement(pos, editor, container);
        });

        console.log('Finished creating all draggable elements');
    }

    function createDraggableElement(position, editor, container) {
        console.log('Creating element for field:', position.field);
        console.log('Position data:', position);
        console.log('Container for element:', container);

        const element = document.createElement('div');
        element.className = 'draggable-element';
        element.dataset.field = position.field;

        // Always use exact A4 pixel dimensions for consistent positioning
        const canvasWidth = 595;  // A4 width in pixels at 72 DPI
        const canvasHeight = 842; // A4 height in pixels at 72 DPI

        // Position element based on exact PDF coordinates
        element.style.left = position.x + 'px';
        element.style.top = position.y + 'px';
        element.style.fontSize = (position.font_size || 12) + 'px';
        element.style.fontWeight = position.font_weight || 'normal';
        element.style.zIndex = '1001'; // Ensure elements are above everything

        // Enhanced visual styling based on field type
        const fieldColors = {
            'invoice_no': 'linear-gradient(135deg, rgba(220, 53, 69, 0.95), rgba(176, 42, 55, 0.9))',
            'invoice_date': 'linear-gradient(135deg, rgba(40, 167, 69, 0.95), rgba(32, 134, 55, 0.9))',
            'product_name': 'linear-gradient(135deg, rgba(255, 87, 34, 0.95), rgba(204, 69, 27, 0.9))',
            'customer_name': 'linear-gradient(135deg, rgba(255, 193, 7, 0.95), rgba(204, 154, 6, 0.9))',
            'customer_phone': 'linear-gradient(135deg, rgba(23, 162, 184, 0.95), rgba(18, 130, 147, 0.9))',
            'payment_details': 'linear-gradient(135deg, rgba(108, 117, 125, 0.95), rgba(86, 94, 100, 0.9))'
        };

        if (fieldColors[position.field]) {
            element.style.background = fieldColors[position.field];
        }

        element.innerHTML = `
            <div class="element-label">${getFieldLabel(position.field)}</div>
            <div class="element-content">${getSampleContent(position.field)}</div>
            <div class="position-coordinates">${Math.round(position.x)}, ${Math.round(position.y)}</div>
        `;

        // Add event listeners
        element.addEventListener('mousedown', handleMouseDown);
        element.addEventListener('click', function(e) {
            e.stopPropagation();
            selectElement(element);
        });

        // Add visual feedback on hover
        element.addEventListener('mouseenter', function() {
            element.style.transform = 'scale(1.05)';
        });

        element.addEventListener('mouseleave', function() {
            if (!element.classList.contains('dragging')) {
                element.style.transform = '';
            }
        });

        console.log('Appending element to container:', element);
        console.log('Container:', container);
        container.appendChild(element);
        console.log('Element successfully appended to container');
    }

    function getFieldLabel(field) {
        const labels = {
            'invoice_no': 'INVOICE #',
            'invoice_date': 'DATE',
            'product_name': 'PRODUCT',
            'customer_name': 'CUSTOMER',
            'customer_phone': 'PHONE',
            'customer_address': 'ADDRESS',
            'customer_email': 'EMAIL',
            'company_name': 'COMPANY',
            'company_address': 'ADDRESS',
            'company_contact': 'CONTACT',
            'payment_details': 'PAYMENT'
        };
        return labels[field] || field.replace('_', ' ').toUpperCase();
    }

    function getSampleContent(field) {
        const samples = {
            'company_name': 'AURA PC FACTORY',
            'company_address': 'KALANCHIYAM THODDAM, KARAVEDDY',
            'company_contact': 'AuraPCFactory@gmail.com | +94 77 022 1046',
            'invoice_no': 'ORDR00001',
            'invoice_date': new Date().toLocaleDateString('en-GB'),
            'product_name': 'Gaming Laptop Dell XPS 15',
            'customer_name': 'John Doe',
            'customer_phone': '+94 77 123 4567',
            'customer_address': '123 Main Street, Colombo 01',
            'customer_email': 'john.doe@email.com',
            'payment_details': 'Cash | LKR 50,000 | PAID',
            'items_table': '[ITEMS TABLE]',
            'total_section': 'LKR 25,000.00',
            'warranty_section': '[WARRANTY TEXT]'
        };
        return samples[field] || field.replace('_', ' ').toUpperCase();
    }

    function handleMouseDown(e) {
        e.preventDefault();
        isDragging = true;
        selectedElement = e.currentTarget;
        selectedElement.classList.add('dragging');

        const rect = selectedElement.getBoundingClientRect();
        dragOffset.x = e.clientX - rect.left;
        dragOffset.y = e.clientY - rect.top;

        selectElement(selectedElement);

        document.addEventListener('mousemove', handleMouseMove);
        document.addEventListener('mouseup', handleMouseUp);
    }

    function handleMouseMove(e) {
        if (!isDragging || !selectedElement) return;

        const container = document.getElementById('letterheadImage');
        const containerRect = container.getBoundingClientRect();

        // Calculate position relative to the container
        let x = e.clientX - containerRect.left - dragOffset.x;
        let y = e.clientY - containerRect.top - dragOffset.y;

        // Always use exact A4 dimensions for consistent positioning
        const canvasWidth = 595;
        const canvasHeight = 842;

        // Constrain to canvas bounds
        x = Math.max(0, Math.min(x, canvasWidth - selectedElement.offsetWidth));
        y = Math.max(0, Math.min(y, canvasHeight - selectedElement.offsetHeight));

        selectedElement.style.left = x + 'px';
        selectedElement.style.top = y + 'px';

        // Update coordinates display (x,y are already in PDF coordinates)
        const coordsElement = selectedElement.querySelector('.position-coordinates');
        coordsElement.textContent = `${Math.round(x)}, ${Math.round(y)}`;

        // Show positioning crosshairs
        showPositioningCrosshairs(x, y);

        // Update properties panel
        updatePropertiesPanel(selectedElement, x, y);
    }

    function showPositioningCrosshairs(x, y) {
        const container = document.getElementById('letterheadImage');

        // Remove existing crosshairs
        const existingCrosshairs = container.querySelectorAll('.positioning-crosshair');
        existingCrosshairs.forEach(ch => ch.remove());

        // Create vertical crosshair
        const verticalCrosshair = document.createElement('div');
        verticalCrosshair.className = 'positioning-crosshair';
        verticalCrosshair.style.cssText = `
            position: absolute;
            left: ${x}px;
            top: 0;
            width: 1px;
            height: 100%;
            background: rgba(255, 0, 0, 0.6);
            z-index: 1001;
            pointer-events: none;
        `;

        // Create horizontal crosshair
        const horizontalCrosshair = document.createElement('div');
        horizontalCrosshair.className = 'positioning-crosshair';
        horizontalCrosshair.style.cssText = `
            position: absolute;
            left: 0;
            top: ${y}px;
            width: 100%;
            height: 1px;
            background: rgba(255, 0, 0, 0.6);
            z-index: 1001;
            pointer-events: none;
        `;

        container.appendChild(verticalCrosshair);
        container.appendChild(horizontalCrosshair);
    }

    function handleMouseUp() {
        if (selectedElement) {
            selectedElement.classList.remove('dragging');
        }
        isDragging = false;

        // Remove positioning crosshairs
        const container = document.getElementById('letterheadImage');
        const crosshairs = container.querySelectorAll('.positioning-crosshair');
        crosshairs.forEach(ch => ch.remove());

        document.removeEventListener('mousemove', handleMouseMove);
        document.removeEventListener('mouseup', handleMouseUp);
    }

    function selectElement(element) {
        // Remove previous selection
        document.querySelectorAll('.draggable-element').forEach(el => {
            el.classList.remove('selected');
        });

        element.classList.add('selected');
        selectedElement = element;

        // Get current position (already in PDF coordinates)
        const x = parseFloat(element.style.left);
        const y = parseFloat(element.style.top);

        updatePropertiesPanel(element, x, y);
    }

    function updatePropertiesPanel(element, x, y) {
        const panel = document.getElementById('elementProperties');
        const field = element.dataset.field;
        const fontSize = parseFloat(element.style.fontSize) || 12;
        const fontWeight = element.style.fontWeight || 'normal';

        panel.innerHTML = `
            <div class="mb-3">
                <label class="form-label">Field</label>
                <input type="text" class="form-control" value="${field}" readonly>
            </div>
            <div class="mb-3">
                <label class="form-label">X Position</label>
                <input type="number" class="form-control" id="posX" value="${Math.round(x)}" min="0" max="595">
            </div>
            <div class="mb-3">
                <label class="form-label">Y Position</label>
                <input type="number" class="form-control" id="posY" value="${Math.round(y)}" min="0" max="842">
            </div>
            <div class="mb-3">
                <label class="form-label">Font Size</label>
                <input type="number" class="form-control" id="fontSize" value="${fontSize}" min="8" max="24">
            </div>
            <div class="mb-3">
                <label class="form-label">Font Weight</label>
                <select class="form-control" id="fontWeight">
                    <option value="normal" ${fontWeight === 'normal' ? 'selected' : ''}>Normal</option>
                    <option value="bold" ${fontWeight === 'bold' ? 'selected' : ''}>Bold</option>
                </select>
            </div>
        `;

        // Add event listeners for property changes
        document.getElementById('posX').addEventListener('input', updateElementPosition);
        document.getElementById('posY').addEventListener('input', updateElementPosition);
        document.getElementById('fontSize').addEventListener('input', updateElementStyle);
        document.getElementById('fontWeight').addEventListener('change', updateElementStyle);
    }

    function updateElementPosition() {
        if (!selectedElement) return;

        const x = parseInt(document.getElementById('posX').value);
        const y = parseInt(document.getElementById('posY').value);

        // Direct positioning in PDF coordinates
        selectedElement.style.left = x + 'px';
        selectedElement.style.top = y + 'px';

        const coordsElement = selectedElement.querySelector('.position-coordinates');
        coordsElement.textContent = `${x}, ${y}`;
    }

    function updateElementStyle() {
        if (!selectedElement) return;

        const fontSize = document.getElementById('fontSize').value;
        const fontWeight = document.getElementById('fontWeight').value;

        selectedElement.style.fontSize = fontSize + 'px';
        selectedElement.style.fontWeight = fontWeight;
    }

    // Save positions
    document.getElementById('savePositions').addEventListener('click', function() {
        const elements = document.querySelectorAll('.draggable-element');
        const positions = [];

        elements.forEach(element => {
            // Get position directly from element style (already in PDF coordinates)
            const x = parseFloat(element.style.left);
            const y = parseFloat(element.style.top);

            positions.push({
                field: element.dataset.field,
                x: Math.round(x),
                y: Math.round(y),
                font_size: parseFloat(element.style.fontSize) || 12,
                font_weight: element.style.fontWeight || 'normal'
            });
        });

        console.log('Saving positions:', positions);

        fetch('{{ route("letterhead.save-positions") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ positions: positions })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast('Element positions saved successfully! Your letterhead is ready to use.', 'success');
            } else {
                showToast('Error saving positions: ' + (data.message || 'Unknown error'), 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Error saving positions', 'error');
        });
    });

    // Grid and Ruler Toggle Controls
    document.getElementById('toggleGrid')?.addEventListener('click', function() {
        const grid = document.getElementById('positioningGrid');
        if (grid) {
            const currentOpacity = parseFloat(grid.style.opacity) || 0.3;
            grid.style.opacity = currentOpacity > 0 ? '0' : '0.3';
            this.classList.toggle('active');
        }
    });

    document.getElementById('toggleRulers')?.addEventListener('click', function() {
        const rulers = document.querySelectorAll('.ruler-top, .ruler-left');
        rulers.forEach(ruler => {
            ruler.style.display = ruler.style.display === 'none' ? 'block' : 'none';
        });
        this.classList.toggle('active');
    });

    // Enhanced grid visibility on canvas hover
    const canvasContainer = document.querySelector('.pdf-canvas-container');
    if (canvasContainer) {
        canvasContainer.addEventListener('mouseenter', function() {
            const grid = document.getElementById('positioningGrid');
            if (grid && parseFloat(grid.style.opacity) > 0) {
                grid.style.opacity = '0.6';
            }
        });

        canvasContainer.addEventListener('mouseleave', function() {
            const grid = document.getElementById('positioningGrid');
            if (grid && parseFloat(grid.style.opacity) > 0) {
                grid.style.opacity = '0.3';
            }
        });
    }

    // Click outside to deselect
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.draggable-element') && !e.target.closest('#elementProperties')) {
            document.querySelectorAll('.draggable-element').forEach(el => {
                el.classList.remove('selected');
            });
            selectedElement = null;
            document.getElementById('elementProperties').innerHTML = '<p class="text-muted">Select an element to edit its properties</p>';
        }
    });

    // Regenerate Preview Button
    const regenerateButton = document.getElementById('regeneratePreview');
    if (regenerateButton) {
        regenerateButton.addEventListener('click', function() {
            const button = this;
            const originalText = button.innerHTML;
            
            button.disabled = true;
            button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Generating...';
            
            fetch('{{ route("letterhead.regenerate-preview") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast('PDF preview generated successfully! Refreshing page...', 'success');
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else {
                    showToast('Failed to generate preview: ' + (data.message || 'Unknown error'), 'error');
                    button.disabled = false;
                    button.innerHTML = originalText;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('Error generating preview', 'error');
                button.disabled = false;
                button.innerHTML = originalText;
            });
        });
    }

    function showToast(message, type = 'info') {
        // Create toast notification
        const toast = document.createElement('div');
        toast.className = `alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible position-fixed`;
        toast.style.top = '20px';
        toast.style.right = '20px';
        toast.style.zIndex = '9999';
        toast.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;

        document.body.appendChild(toast);

        setTimeout(() => {
            toast.remove();
        }, 5000);
    }
});
</script>
@endsection
