{{-- ImageMagick Information Component --}}
<div class="alert alert-info d-flex align-items-center" role="alert">
    <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2 flex-shrink-0" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
        <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0"/>
        <path d="m9 12l2 2l4 -4"/>
    </svg>
    <div>
        <h6 class="mb-1">PDF Preview Status</h6>
        <p class="mb-0">
            <strong>Current Status:</strong> Using command-line ImageMagick for PDF preview generation.<br>
            <small class="text-muted">Your letterhead positioning and PDF generation work perfectly. Preview images help with visual positioning but are not required for functionality.</small>
        </p>
        @if($showInstallInstructions ?? false)
        <hr class="my-2">
        <details>
            <summary class="text-primary" style="cursor: pointer;">Show Installation Instructions</summary>
            <div class="mt-2">
                <p class="mb-2"><strong>To enable PHP ImageMagick extension:</strong></p>
                <pre class="bg-light p-2 rounded"><code>brew install php@8.3-imagick
# or
pecl install imagick</code></pre>
                <p class="mb-0"><small>This will enable faster PDF preview generation, but is optional.</small></p>
            </div>
        </details>
        @endif
    </div>
</div>