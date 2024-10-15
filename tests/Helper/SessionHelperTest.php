<?php

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use RezaFikkri\PLM\Library\Session;

class SessionHelperTest extends TestCase
{
    #[Test]
    public function session(): void
    {
        $session = session();
        $this->assertInstanceOf(Session::class, $session);
    }
}
