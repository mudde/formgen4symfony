<?php

namespace Mudde\Formgen4Symfony\Input;

class Textarea extends InputAbstract
{
    public function fields(): array
    {
        return [
            'spellcheck',
            ...parent::fields()
        ];
    }
}