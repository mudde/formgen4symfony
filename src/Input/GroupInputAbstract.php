<?php

namespace Mudde\Formgen4Symfony\Input;

class GroupInputAbstract extends InputAbstract
{
    public function fields(): array
    {
        return [
            'data',
            'currentData',
            ...parent::fields()
        ];
    }
}