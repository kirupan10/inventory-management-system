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

                                                    // Determine which image to use for positioning
                                                    if ($letterheadType === 'pdf' && $previewImage) {
                                                        $positioningImage = 'letterheads/' . $previewImage;
                                                        $showImage = true;
                                                    } elseif ($letterheadType === 'pdf') {
                                                        $showImage = false;
                                                    } else {
                                                        $positioningImage = 'letterheads/' . $config['letterhead_file'];
                                                        $showImage = true;
                                                    }
                                                @endphp

                                                @if($showImage)
                                                    <img src="{{ asset($positioningImage) }}"
                                                         alt="Letterhead {{ $letterheadType === 'pdf' ? '(PDF Preview)' : '' }}"
                                                         id="letterheadImage"
                                                         style="width: 100%; max-width: 595px; height: auto; position: relative;">

                                                    @if($letterheadType === 'pdf')
                                                    <div class="alert alert-info mt-2">
                                                        <small><strong>Note:</strong> This is a preview of your PDF letterhead for positioning elements. The actual PDF will be used in the final invoice.</small>
                                                    </div>
                                                    @endif
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
                                                        <small><strong>PDF Letterhead Positioning:</strong> Use the grid above to position elements. Your PDF letterhead will be used as the background in the final invoice. Preview generation requires ImageMagick extension.</small>
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
    border: 2px solid #ddd;
    background: #f8f9fa;
    display: inline-block;
}

.draggable-element {
    position: absolute;
    background: linear-gradient(135deg, rgba(0, 123, 255, 0.9) 0%, rgba(0, 86, 179, 0.8) 100%);
    border: 2px solid #007bff;
    padding: 10px 12px;
    cursor: move;
    font-family: Arial, sans-serif;
    font-weight: 600;
    color: white;
    min-width: 120px;
    min-height: 35px;
    user-select: none;
    border-radius: 6px;
    box-shadow:
        0 4px 8px rgba(0,0,0,0.2),
        0 2px 4px rgba(0,123,255,0.3),
        inset 0 1px 0 rgba(255,255,255,0.2);
    z-index: 1000;
    transition: all 0.2s ease;
    backdrop-filter: blur(2px);
    text-shadow: 0 1px 2px rgba(0,0,0,0.3);
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
    font-size: 9px;
    color: rgba(255, 255, 255, 0.8);
    margin-bottom: 3px;
    font-weight: bold;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    text-shadow: 0 1px 2px rgba(0,0,0,0.5);
}

.draggable-element .element-content {
    font-size: 11px;
    color: white;
    line-height: 1.2;
    margin-bottom: 2px;
    text-shadow: 0 1px 2px rgba(0,0,0,0.3);
}

.position-coordinates {
    position: absolute;
    top: -25px;
    right: -5px;
    font-size: 9px;
    color: #495057;
    background: rgba(255, 255, 255, 0.95);
    padding: 2px 6px;
    border: 1px solid #dee2e6;
    border-radius: 4px;
    font-weight: bold;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
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
    initializeDraggableElements();

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

        if (!positions || positions.length === 0) {
            console.error('No positions data found! Creating default elements...');
            // Create default elements as fallback
            const defaultPositions = [
                {field: 'customer_name', x: 50, y: 50, font_size: 12, font_weight: 'bold'},
                {field: 'invoice_no', x: 400, y: 50, font_size: 12, font_weight: 'bold'},
                {field: 'customer_phone', x: 50, y: 80, font_size: 11, font_weight: 'normal'},
                {field: 'invoice_date', x: 400, y: 80, font_size: 12, font_weight: 'normal'}
            ];
            defaultPositions.forEach((pos, index) => {
                console.log(`Creating default element ${index + 1}:`, pos);
                createDraggableElement(pos, editor, container);
            });
            return;
        }

        positions.forEach((pos, index) => {
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

        // Calculate position relative to container size
        let containerWidth, containerHeight;

        if (container.tagName === 'IMG') {
            containerWidth = container.offsetWidth;
            containerHeight = container.offsetHeight;
        } else {
            // For div containers (PDF without preview)
            containerWidth = 595; // A4 width in pixels
            containerHeight = 842; // A4 height in pixels
        }

        const x = (position.x / 595) * containerWidth;
        const y = (position.y / 842) * containerHeight;

        element.style.left = x + 'px';
        element.style.top = y + 'px';
        element.style.fontSize = (position.font_size || 12) + 'px';
        element.style.fontWeight = position.font_weight || 'normal';
        element.style.zIndex = '10'; // Ensure elements are above the background

        element.innerHTML = `
            <div class="element-label">${position.field.replace('_', ' ').toUpperCase()}</div>
            <div class="element-content">${getSampleContent(position.field)}</div>
            <div class="position-coordinates">${Math.round(position.x)}, ${Math.round(position.y)}</div>
        `;

        // Add event listeners
        element.addEventListener('mousedown', handleMouseDown);
        element.addEventListener('click', function(e) {
            e.stopPropagation();
            selectElement(element);
        });

        console.log('Appending element to container:', element);
        console.log('Container:', container);
        container.appendChild(element);
        console.log('Element successfully appended to container');
    }

    function getSampleContent(field) {
        const samples = {
            'company_name': 'AURA PC FACTORY',
            'company_address': 'KALANCHIYAM THODDAM, KARAVEDDY',
            'company_contact': '📧 AuraPCFactory@gmail.com 📞 +94 77 022 1046',
            'invoice_no': 'ORDR00001',
            'invoice_date': '29/09/2025',
            'customer_name': 'John Doe',
            'customer_phone': '+94 77 123 4567',
            'customer_address': '123 Main Street, Colombo 01',
            'customer_email': 'john.doe@email.com',
            'items_table': '[TABLE AREA]',
            'total_section': 'TOTAL: LKR 25,000.00',
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

        const editor = document.getElementById('positionEditor');
        const editorRect = editor.getBoundingClientRect();
        const container = document.getElementById('letterheadImage');

        let x = e.clientX - editorRect.left - dragOffset.x;
        let y = e.clientY - editorRect.top - dragOffset.y;

        // Get container dimensions
        let containerWidth, containerHeight;
        if (container.tagName === 'IMG') {
            containerWidth = container.offsetWidth;
            containerHeight = container.offsetHeight;
        } else {
            containerWidth = 595;
            containerHeight = 842;
        }

        // Constrain to container bounds
        x = Math.max(0, Math.min(x, containerWidth - selectedElement.offsetWidth));
        y = Math.max(0, Math.min(y, containerHeight - selectedElement.offsetHeight));

        selectedElement.style.left = x + 'px';
        selectedElement.style.top = y + 'px';

        // Update coordinates display
        const actualX = (x / containerWidth) * 595;
        const actualY = (y / containerHeight) * 842;

        const coordsElement = selectedElement.querySelector('.position-coordinates');
        coordsElement.textContent = `${Math.round(actualX)}, ${Math.round(actualY)}`;

        // Update properties panel
        updatePropertiesPanel(selectedElement, actualX, actualY);
    }

    function handleMouseUp() {
        if (selectedElement) {
            selectedElement.classList.remove('dragging');
        }
        isDragging = false;
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

        // Update properties panel
        const container = document.getElementById('letterheadImage');
        let containerWidth;

        if (container.tagName === 'IMG') {
            containerWidth = container.offsetWidth;
            containerHeight = container.offsetHeight;
        } else {
            containerWidth = 595;
            containerHeight = 842;
        }

        const x = (parseFloat(element.style.left) / containerWidth) * 595;
        const y = (parseFloat(element.style.top) / containerHeight) * 842;

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
        const container = document.getElementById('letterheadImage');

        let containerWidth, containerHeight;
        if (container.tagName === 'IMG') {
            containerWidth = container.offsetWidth;
            containerHeight = container.offsetHeight;
        } else {
            containerWidth = 595;
            containerHeight = 842;
        }

        const pixelX = (x / 595) * containerWidth;
        const pixelY = (y / 842) * containerHeight;

        selectedElement.style.left = pixelX + 'px';
        selectedElement.style.top = pixelY + 'px';

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
        const container = document.getElementById('letterheadImage');

        let containerWidth, containerHeight;
        if (container.tagName === 'IMG') {
            containerWidth = container.offsetWidth;
            containerHeight = container.offsetHeight;
        } else {
            containerWidth = 595;
            containerHeight = 842;
        }

        elements.forEach(element => {
            const x = (parseFloat(element.style.left) / containerWidth) * 595;
            const y = (parseFloat(element.style.top) / containerHeight) * 842;

            positions.push({
                field: element.dataset.field,
                x: Math.round(x),
                y: Math.round(y),
                font_size: parseFloat(element.style.fontSize) || 12,
                font_weight: element.style.fontWeight || 'normal'
            });
        });

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
                showToast('Positions saved successfully!', 'success');
            } else {
                showToast('Error saving positions', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Error saving positions', 'error');
        });
    });

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
