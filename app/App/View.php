<?php

namespace RezaFikkri\PLM\App;

class View
{
    public static function render(string $view, array $models): void
    {
        foreach ($models as $key => $val) {
            $$key = $val;
        }

        require __DIR__ . '/../View/header.php';
        require __DIR__ . "/../View/$view.php";
        require __DIR__ . '/../View/footer.php';
    }
}
