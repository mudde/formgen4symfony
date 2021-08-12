<?php

namespace Mudde\Formgen4Symfony\Input;

class UploadFile extends InputAbstract
{
    public function fields(): array
    {
        return [
            'multiple',
            ...parent::fields()
        ];
    }
}