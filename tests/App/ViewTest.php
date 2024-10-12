<?php

namespace RezaFikkri\PLM\App;

use PHPUnit\Framework\TestCase;

class ViewTest extends TestCase
{
    public function testRender(): void
    {
        View::render('Home/index', [
            'title' => 'PHP Login Management',
        ]);

        // var_dump(preg_match(pattern: '#(?=.*viewport)(?=.*hahaha)#', subject: 'gege hahaha viewport [][][] hehehe', matches: $variables));
        // var_dump($variables);

        // Menggunakan lookahead assertion (?=...)
        $this->expectOutputRegex('#(?=.*viewport)(?=.*Register)(?=.*Login)(?=.*<script)#s');

        // note: jika kamu memanggil expectOutputRegex lebih dari 1 kali, maka
        // value dari regex pattern yang telah di set, akan di replace oleh value
        // regex pattern dibawahnya. Ex:
        //
        // expectOutputRegex('#pertama#')
        // expectOutputRegex('#kedua#')
        //
        // maka regex pattern #edua# akan me-replace regex pattern #pertama#,
        // jadinya yang akan di cek hanya regex pattern kedua.
    }
}
