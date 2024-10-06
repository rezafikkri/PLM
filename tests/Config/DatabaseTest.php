<?php

namespace RezaFikkri\PLM\Config;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class DatabaseTest extends TestCase
{
    #[Test]
    public function getConnection(): void
    {
        $dbc = Database::getConnection();
        $this->assertNotNull($dbc);
    }

    #[Test]
    public function getConnectionSingleton(): void
    {
        $dbc1 = Database::getConnection();
        $dbc2 = Database::getConnection();

        $this->assertSame($dbc1, $dbc2);
    }
}
