<?php

namespace RezaFikkri\MVC\App;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class RegexTest extends TestCase
{
    #[Test]
    public function regex(): void
    {
        $path = '/products/2refefe/categories/fe3434';
        $pattern = '#^/products/([\d\w]+)/categories/([\d\w]+)$#';
        $result = preg_match($pattern, $path, $variables);

        $this->assertEquals(1, $result);

        array_shift($variables);
        var_dump($variables);
    }
}
