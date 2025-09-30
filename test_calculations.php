<?php
// Quick calculation test script
// Run this directly with: php test_calculations.php

echo "=== NEXORA CALCULATION TEST ===\n\n";

// Test data from your PDF example
$subtotal = 4487.00;  // LKR
$discount = 80.00;    // LKR
$serviceCharges = 35.00; // LKR

echo "Test Data:\n";
echo "Subtotal: LKR " . number_format($subtotal, 2) . "\n";
echo "Discount: -LKR " . number_format($discount, 2) . "\n";
echo "Service Charges: +LKR " . number_format($serviceCharges, 2) . "\n";
echo "\n";

// Manual calculation
$calculatedTotal = $subtotal - $discount + $serviceCharges;
echo "Manual Calculation:\n";
echo "$subtotal - $discount + $serviceCharges = " . number_format($calculatedTotal, 2) . "\n";
echo "\n";

// Test JavaScript-style formatting
function formatCurrency($amount) {
    return number_format($amount, 2);
}

// Test with proper rounding
$roundedTotal = round($calculatedTotal * 100) / 100;
echo "With Rounding:\n";
echo "Rounded Total: LKR " . formatCurrency($roundedTotal) . "\n";
echo "\n";

// Test database conversion (cents)
$subtotalCents = (int)($subtotal * 100);
$discountCents = (int)($discount * 100);
$serviceChargesCents = (int)($serviceCharges * 100);
$totalCents = $subtotalCents - $discountCents + $serviceChargesCents;

echo "Database Storage (in cents):\n";
echo "Subtotal: " . number_format($subtotalCents) . " cents\n";
echo "Discount: " . number_format($discountCents) . " cents\n";
echo "Service Charges: " . number_format($serviceChargesCents) . " cents\n";
echo "Total: " . number_format($totalCents) . " cents\n";
echo "\n";

// Convert back from cents
$convertedSubtotal = $subtotalCents / 100;
$convertedDiscount = $discountCents / 100;
$convertedServiceCharges = $serviceChargesCents / 100;
$convertedTotal = $totalCents / 100;

echo "Converted Back from Database:\n";
echo "Subtotal: LKR " . number_format($convertedSubtotal, 2) . "\n";
echo "Discount: -LKR " . number_format($convertedDiscount, 2) . "\n";
echo "Service Charges: +LKR " . number_format($convertedServiceCharges, 2) . "\n";
echo "Total: LKR " . number_format($convertedTotal, 2) . "\n";
echo "\n";

// Verification
$isCorrect = abs($calculatedTotal - $convertedTotal) < 0.01;
echo "VERIFICATION: " . ($isCorrect ? "✓ CORRECT" : "✗ ERROR") . "\n";
if (!$isCorrect) {
    echo "Difference: " . number_format($calculatedTotal - $convertedTotal, 2) . "\n";
}

echo "\n=== TEST COMPLETE ===\n";
?>
