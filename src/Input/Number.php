<?php

namespace Mudde\Formgen4Symfony\Input;

class Number extends InputAbstract
{
    public function fields(): array
    {
        return [
            'mask',
            'format',
            'prefix',
            'suffix',
            ...parent::fields()
        ];
    }
}