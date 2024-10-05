<?php

namespace RezaFikkri\PLM\Controller;

class ProductController
{
    public function category(string $productId, string $categoryId): void
    {
        var_dump([$productId, $categoryId]);
    }
}
