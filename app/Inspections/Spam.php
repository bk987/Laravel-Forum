<?php

namespace App\Inspections;

class Spam
{
    /**
     * All registered inspections.
     *
     * @var array
     */
    protected $inspections = [
        InvalidKeywords::class,
        KeyHeldDown::class
    ];

    /**
     * Detect spam.
     *
     * @param string $content
     * @return bool
     */
    public function detect($content)
    {
        foreach ($this->inspections as $inspection) {
            app($inspection)->detect($content);
        }

        return false;
    }
}
