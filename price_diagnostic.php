<?php
/**
 * Price Fix Diagnostic and Repair Tool
 * This will help identify and fix the "missing two zeros" issue
 */

echo "=== NEXORA PRICE DIAGNOSTIC TOOL ===\n\n";

// Test different scenarios
$testCases = [
    'Scenario 1: Raw database value 1500 (LKR 1500)' => [
        'raw_value' => 1500,
        'with_accessor' => 1500 / 100,  // 15.00 (WRONG!)
        'without_accessor' => 1500,      // 1500.00 (CORRECT!)
        'formatted_with_accessor' => number_format(1500 / 100, 2),
        'formatted_without_accessor' => number_format(1500, 2)
    ],
    'Scenario 2: Raw database value 150000 (stored in cents)' => [
        'raw_value' => 150000,
        'with_accessor' => 150000 / 100,  // 1500.00 (CORRECT!)
        'without_accessor' => 150000,      // 150000.00 (WRONG!)
        'formatted_with_accessor' => number_format(150000 / 100, 2),
        'formatted_without_accessor' => number_format(150000, 2)
    ]
];

foreach ($testCases as $scenario => $data) {
    echo "{$scenario}:\n";
    echo "  Raw DB Value: {$data['raw_value']}\n";
    echo "  With Accessor (÷100): {$data['with_accessor']}\n";
    echo "  Without Accessor: {$data['without_accessor']}\n";
    echo "  Formatted With Accessor: LKR {$data['formatted_with_accessor']}\n";
    echo "  Formatted Without Accessor: LKR {$data['formatted_without_accessor']}\n";
    echo "\n";
}

echo "DIAGNOSIS:\n";
echo "If you see prices like LKR 15.00 instead of LKR 1500.00:\n";
echo "- Problem: Prices stored as whole numbers but accessor divides by 100\n";
echo "- Solution: Either remove accessor OR ensure data is stored in cents\n\n";

echo "If you see prices like LKR 150,000.00 instead of LKR 1500.00:\n";
echo "- Problem: Prices stored in cents but accessor not used\n";
echo "- Solution: Use the accessor (current PDF template should work)\n\n";

echo "RECOMMENDED FIXES:\n";
echo "1. Check a few actual database records to confirm data format\n";
echo "2. If stored as whole numbers: Remove the accessor in OrderDetails model\n";
echo "3. If stored in cents: Keep current setup (should work with recent fix)\n";
echo "4. Clear cache after any changes: php artisan cache:clear\n\n";

echo "To verify: Create a test order and check the raw database values.\n";
?>
