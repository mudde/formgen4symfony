<?php


namespace Mudde\Formgen4Symfony\Annotation;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class FormGen
{
    private array $config = [];

    public function __construct(
        string $id,
        array $attributes = [],
    )
    {
        $this->config = $attributes;
        $this->config['id'] = $id;
    }

    public function getConfig(): array
    {
        return $this->config;
    }
}