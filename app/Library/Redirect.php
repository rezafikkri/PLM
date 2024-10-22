<?php

namespace RezaFikkri\PLM\Library;

class Redirect
{
    private bool $withInput = false;

    public function __construct(
        private Flash $flash,
        private string $path = '/',
    ) {
        
    }

    private function redirect(): void
    {
        header("Location: $this->path");
        if ($_ENV['APP_ENV'] != 'development') {
            exit();
        }
    }

    private function setOldForm(): void
    {
        if (count($_POST) > 0) {
            $this->flash->setFlashData('form', array_filter(
                $_POST,
                fn($k) => $k != 'password',
                ARRAY_FILTER_USE_KEY,
            ));
        }
    }

    public function to(string $path): void
    {
        $this->path = $path;
        if ($this->withInput) {
            $this->setOldForm();
        }
        
        $this->redirect();
    }

    public function withInput(): self
    {
        $this->withInput = true;
        return $this;
    }
}
