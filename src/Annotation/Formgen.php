<?php


namespace Mudde\Formgen4Symfony\Annotation;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class Formgen
{
    private array $config = [];

    public function __construct(
        string $id,
        array $attributes = [],
    )
    {
        $config= [];
        $config['fields'] = [];
        $config['buttons'] = [];
        $config['languages'] = ['en'];
        $config['builders'] = [];
        $config['data'] = null;

        $attributes['id'] = $id;

        $this->config = array_merge($config, $attributes);
    }

    public function getConfig(): array
    {
        return $this->config;
    }
}