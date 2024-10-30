<?php

use RezaFikkri\PLM\Library\Redirect;

function redirect(): Redirect
{
    return new Redirect();
}

function old(string $key, string $default = ''): string
{
    $oldValue = flash()->getData('form')[$key] ?? null;
    if (is_null($oldValue) || empty(trim($oldValue))) {
        return htmlspecialchars($default);
    }
    return htmlspecialchars($oldValue);
}
