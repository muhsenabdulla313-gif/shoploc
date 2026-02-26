<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';

// Bootstrap the application
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Product;

try {
    // Test creating a simple product with the updated model
    $product = Product::create([
        'name' => 'Test Product',
        'category' => 'men',
        'subcategory' => 't-shirts',
        'price' => 29.99,
        'stock' => 10,
        'colors' => json_encode(['red', 'blue']),
        'sizes' => json_encode(['m', 'l']),
        'description' => 'Test product description',
    ]);
    
    echo "Product created successfully! ID: " . $product->id . "\n";
    
    // Clean up - delete the test product
    $product->delete();
    echo "Test product cleaned up.\n";
    
} catch (Exception $e) {
    echo "Error creating product: " . $e->getMessage() . "\n";
}