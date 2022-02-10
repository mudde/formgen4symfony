<?php

namespace Mudde\Formgen4Symfony\Input;

class TextRead extends InputAbstract
{
    public function fields(): array
    {
        return [
            'mask',
            'format',
            'prefix',
            'suffix',
            'multiple',
            'spellcheck',
            ...parent::fields()
        ];
    }
}