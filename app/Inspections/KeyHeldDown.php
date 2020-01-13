<?php

namespace App\Inspections;

use Exception;

class KeyHeldDown
{
    /**
     * Detect spam.
     *
     * @param  string $content
     * @throws \Exception
     */
    public function detect($content)
    {
        if (preg_match('/(.)\\1{4,}/', $content)) {
            throw new Exception('Your reply contains spam.');
        }
    }
}
