<?php

namespace RezaFikkri\MVC\Controller;

class ProductController
{
    public function category(string $productId, string $categoryId): void
    {
        var_dump([$productId, $categoryId]);
    }
}
