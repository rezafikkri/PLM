<?php

namespace RezaFikkri\PLM\Library {
    function header(string $value) {
        echo $value;
    }

    function setcookie(
        string $name,
        string $value = '',
        int $expires_or_options = 0,
        string $path = '',
    ): void {
        echo "$name: $value";
    }
};

namespace RezaFikkri\PLM\Service {
    function setcookie(
        string $name,
        string $value = '',
        int $expires_or_options = 0,
        string $path = '',
    ): void {
        echo "$name: $value";
    }
};
