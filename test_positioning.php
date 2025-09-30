<?php
require_once 'vendor/autoload.php';

use Barryvdh\DomPDF\Facade\Pdf as PDF;

echo "Creating test overlay PDF to verify positioning...\n";

// Get current letterhead config
$configPath = storage_path('app/letterhead_config.json');
$config = json_decode(file_get_contents($configPath), true);

echo "Positions in config:\n";
foreach ($config['positions'] as $pos) {
    echo "- {$pos['field']}: ({$pos['x']}, {$pos['y']}) size: {$pos['font_size']}px\n";
}

// Simple HTML to test positioning
$html = '<!DOCTYPE html>
<html>
<head>
    <style>
        body { margin: 0; padding: 0; font-family: Arial; background: transparent; }
        .page { width: 595px; height: 842px; position: relative; }
        .test-element { position: absolute; color: red; font-weight: bold; border: 1px solid red; padding: 2px; }
    </style>
</head>
<body>
    <div class="page">';

foreach ($config['positions'] as $pos) {
    $html .= '<div class="test-element" style="left: ' . $pos['x'] . 'px; top: ' . $pos['y'] . 'px; font-size: ' . $pos['font_size'] . 'px;">' . strtoupper($pos['field']) . '</div>';
}

$html .= '    </div>
</body>
</html>';

$pdf = PDF::loadHTML($html);
$pdf->setPaper('A4', 'portrait');
$pdf->setOptions(['dpi' => 72]);

$testPath = storage_path('app/test_positioning.pdf');
$pdf->save($testPath);

echo "Test PDF saved to: $testPath\n";
echo "File size: " . filesize($testPath) . " bytes\n";
