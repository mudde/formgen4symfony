<?php

namespace Mudde\Formgen4Symfony\Input;

class Text extends InputAbstract
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