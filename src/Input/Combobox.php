<?php

namespace Mudde\Formgen4Symfony\Input;

class Combobox extends InputAbstract
{
    public function fields(): array
    {
        return [
            'multiple',
            'data',
            ...parent::fields()
        ];
    }
}