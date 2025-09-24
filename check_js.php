<?php
// Simple test to check if our JavaScript is syntactically correct

$jsContent = <<<'JS'
console.log('📝 Stock management script loading...');

// Global variables
var currentStockOperation = null;

// Debug function to test if JavaScript is working
window.debugTest = function() {
    console.log('JavaScript is working!');
    alert('JavaScript is working!');
};

// Stock management functions - define directly on window
window.testAddStock = function(id, name, currentStock, unit) {
    console.log('Add stock:', { id, name, currentStock, unit });
    alert('Add Stock clicked for: ' + name);
};

window.testSubtractStock = function(id, name, currentStock, unit) {
    console.log('Subtract stock:', { id, name, currentStock, unit });
    alert('Subtract Stock clicked for: ' + name);
};

window.testStockHistory = function(id, name) {
    console.log('Stock history:', { id, name });
    alert('Stock History clicked for: ' + name);
};

console.log('✅ All functions defined');
JS;

// Create a simple HTML page to test this JavaScript
$html = <<<HTML
<!DOCTYPE html>
<html>
<head>
    <title>Stock Management JS Test</title>
    <meta name="csrf-token" content="test-token">
</head>
<body>
    <h1>Stock Management JavaScript Test</h1>
    
    <button onclick="window.testAddStock(1, 'Test Medicine', 50, 'pcs')">Test Add Stock</button>
    <button onclick="window.testSubtractStock(1, 'Test Medicine', 50, 'pcs')">Test Subtract Stock</button>
    <button onclick="window.testStockHistory(1, 'Test Medicine')">Test Stock History</button>
    <button onclick="window.debugTest()">Test Debug Function</button>
    
    <script>
{$jsContent}
    </script>
</body>
</html>
HTML;

echo $html;
?>