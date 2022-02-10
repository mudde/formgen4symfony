<?php


namespace Mudde\Formgen4Symfony\Annotation;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class Formgen
{
    private array $config = [];

    public function getConfig(): array
    {
        return $this->config;
    }
}