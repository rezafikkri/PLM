<?php

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use RezaFikkri\PLM\Library\Flash;
use RezaFikkri\PLM\Library\Redirect;

class RedirectHelperTest extends TestCase
{
    private Flash $flash;

    protected function setUp(): void
    {
        $this->flash = flash();

        $_COOKIE = [];
    }

    #[Test]
    public function redirect(): void
    {
        $redirect = redirect();
        $this->assertInstanceOf(Redirect::class, $redirect);
    }

    #[Test]
    public function oldExist(): void
    {
        $_COOKIE[$this->flash->getFlashName('form')] = json_encode(['username' => 'rezafikkriusername']);
        $old = old('username');
        $this->assertEquals('rezafikkriusername', $old);
    }

    #[Test]
    public function oldNotExist(): void
    {
        $old = old('username');
        $this->assertEmpty($old);
    }
}
