<?php

namespace App\Inspections;

use Exception;

class InvalidKeywords
{
    /**
     * List of keywords that are considered spam.
     *
     * @var array
     */
    protected $keywords = [
        'xxx'
    ];

    /**
     * Detect spam.
     *
     * @param string $content
     * @throws \Exception
     */
    public function detect($content)
    {
        foreach ($this->keywords as $keyword) {
            if (stripos($content, $keyword) !== false) {
                throw new Exception('Your reply contains spam.');
            }
        }
    }
}
