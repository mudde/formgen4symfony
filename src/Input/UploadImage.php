<?php

namespace Mudde\Formgen4Symfony\Input;

class UploadImage extends InputAbstract
{
    public function fields(): array
    {
        return [
            'multiple',
            'accept',
            ...parent::fields()
        ];
    }

}