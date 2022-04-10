<?php

namespace Mudde\Formgen4Symfony\Input;

class Combobox extends InputAbstract
{
    public function fields(): array
    {
        return [
            'multiple'=>false,
            'data' => [
                'type' => 'array',
            ],
            ...parent::fields()
        ];
    }
}