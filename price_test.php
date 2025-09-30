<?php
// Test different price formatting approaches
// This will help us identify the exact issue

echo "=== PRICE FORMATTING TEST ===\n\n";

// Test different scenarios
$testValues = [
    'Scenario 1: Raw DB 88000 (stored as whole LKR)' => 88000,
    'Scenario 2: Raw DB 8800000 (stored as cents)' => 8800000,
    'Scenario 3: Raw DB 880 (incorrect storage)' => 880,
];

foreach ($testValues as $scenario => $rawValue) {
    echo "$scenario:\n";
    echo "  Raw Value: $rawValue\n";
    echo "  Accessor (÷100): " . ($rawValue / 100) . "\n";
    echo "  No Division: $rawValue\n";
    echo "  Formatted Accessor: " . number_format($rawValue / 100, 2, '.', ',') . "\n";
    echo "  Formatted No Division: " . number_format($rawValue, 2, '.', ',') . "\n";
    echo "\n";
}

echo "=== LIKELY SOLUTIONS ===\n";
echo "1. If raw DB has 88000 and you want to show 88,000.00 → Don't divide\n";
echo "2. If raw DB has 8800000 and you want to show 88,000.00 → Divide by 100\n";
echo "3. If raw DB has 880 and you want to show 88,000.00 → Multiply by 100\n\n";

echo "To fix: We need to know the actual raw database values first!\n";
?>
