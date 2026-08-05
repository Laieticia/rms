<?php

namespace Tests\Unit;

use App\Models\Product;
use PHPUnit\Framework\TestCase;

class ProductTest extends TestCase
{
    public function test_allergens_attribute_is_normalized_to_array_when_string(): void
    {
        $product = new Product();
        $product->forceFill(['allergens' => 'Gluten, lait']);

        $this->assertSame(['Gluten', 'lait'], $product->allergens);
    }
}
