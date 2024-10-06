<?php

namespace RezaFikkri\PLM\App;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class ViewTest extends TestCase
{
    #[Test]
    public function render(): void
    {
        View::render('Home/index', [
            'title' => 'PHP Login Management',
        ]);

        $this->expectOutputRegex('#PHP Login Management#');
        $this->expectOutputRegex('#html#');
        $this->expectOutputRegex('#body#');
        $this->expectOutputRegex('#Register#');
        $this->expectOutputRegex('#Login#');
    }
}
