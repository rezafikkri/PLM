<?php

namespace RezaFikkri\PLM\Library;

class Redirect
{
    private bool $withInput = false;

    public function __construct(
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
        foreach ($_POST as $key => $value) {
            if ($key != 'password') {
                $_SESSION['form'][$key] = $value;
            }
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
