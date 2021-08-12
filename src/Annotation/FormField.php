<?php


namespace Mudde\Formgen4Symfony\Annotation;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class FormField
{
    private array $config = [];

    public function __construct(
        string $id,
        string $label,
        array  $validators = [],
        array  $attributes = [],
    )
    {
        $this->config = $attributes;
        $this->config['id'] = $id;
        $this->config['label'] = $label;
        $this->config['validators'] = $validators;
    }

    public function getConfig(): array
    {
        return $this->config;
    }

}