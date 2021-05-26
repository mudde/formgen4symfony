<?php

namespace Mudde\Formgen4Symfony\Helper;

class FormgenHelper
{

    static function toJson($entity): array
    {
        $reflection = new \ReflectionClass($entity);
        var_dump($reflection);
        exit;

        return [];
    }
}
