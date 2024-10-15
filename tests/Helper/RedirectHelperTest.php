<?php

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use RezaFikkri\PLM\Library\Redirect;

class RedirectHelperTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        session()->startSession();
    }

    protected function setUp(): void
    {
        // clear session (flash, form)
        session()->clear();
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
        $_SESSION['form']['username'] = 'rezafikkriusername';
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
