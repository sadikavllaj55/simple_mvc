<?php

namespace App\View;

class Template
{
    private $layout;
    private $variables;

    public function __construct($layout = 'guest') {
        $this->layout = $layout;
        $this->variables = [];
    }

    function view($template, $variables = []) {
        extract(array_merge($this->variables, $variables));
        include TEMPLATES . 'layout/' . $this->layout .  '.phtml';
    }
}