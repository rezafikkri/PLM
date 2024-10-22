<?php

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use RezaFikkri\PLM\Library\Flash;

class FlashHelperTest extends TestCase
{
    #[Test]
    public function flash(): void
    {
        $flash = flash();
        $this->assertInstanceOf(Flash::class, $flash);
    }
}
