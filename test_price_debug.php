<?php
// Test script to check actual price values
// Run with: php test_price_debug.php

// First, let's see what we can access directly
echo "=== PRICE DEBUG TEST ===\n\n";

// Simulate database values that might be causing the issue
$testPrices = [
    'stored_as_whole_number' => 1500,    // If stored as 1500 (meaning LKR 1500)
    'stored_as_cents' => 150000,         // If stored as 150000 cents (meaning LKR 1500)
    'expected_display' => 1500.00,       // What should display
];

echo "Test Scenarios:\n";
echo "1. If stored as whole number (1500):\n";
echo "   - Raw value: {$testPrices['stored_as_whole_number']}\n";
echo "   - With accessor (/100): " . ($testPrices['stored_as_whole_number'] / 100) . "\n";
echo "   - Formatted: " . number_format($testPrices['stored_as_whole_number'] / 100, 2) . "\n";
echo "   - Result: LKR " . number_format($testPrices['stored_as_whole_number'] / 100, 2) . " (WRONG - Missing zeros!)\n\n";

echo "2. If stored as cents (150000):\n";
echo "   - Raw value: {$testPrices['stored_as_cents']}\n";
echo "   - With accessor (/100): " . ($testPrices['stored_as_cents'] / 100) . "\n";
echo "   - Formatted: " . number_format($testPrices['stored_as_cents'] / 100, 2) . "\n";
echo "   - Result: LKR " . number_format($testPrices['stored_as_cents'] / 100, 2) . " (CORRECT)\n\n";

echo "3. Expected display: LKR " . number_format($testPrices['expected_display'], 2) . "\n\n";

echo "DIAGNOSIS:\n";
echo "If you're seeing prices like LKR 15.00 instead of LKR 1500.00,\n";
echo "then the prices are stored as whole numbers but accessor is dividing by 100.\n\n";

echo "SOLUTIONS:\n";
echo "1. Remove the /100 division in PDF if prices are stored as whole numbers\n";
echo "2. Or update database to store prices in cents\n";
echo "3. Or remove the accessor from OrderDetails model\n\n";

echo "To check actual database values, we need to examine the raw data.\n";
?>
