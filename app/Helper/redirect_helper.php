<?php

use RezaFikkri\PLM\Library\Redirect;

function redirect(): Redirect
{
    return new Redirect(flash());
}

function old(string $key): string
{
    return htmlspecialchars(flash()->getData('form')[$key] ?? '');
}
