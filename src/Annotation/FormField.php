<?php


namespace Mudde\Formgen4Symfony\Annotation;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class FormField
{
    private array $config = [];

    public function __construct(string $id, string $name, array $validations , array $config)
    {
        $config['id'] = $id;
        $config['name'] = $name;
        $config['validations'] = $validations;

        $this->config = $config;
    }

    public function getConfig(): array
    {
        return $this->config;
    }

}