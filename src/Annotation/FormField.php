<?php


namespace Mudde\Formgen4Symfony\Annotation;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class FormField
{
    private array $config = [];

    public function __construct(){
        
    }

    public function getConfig(): array
    {
        return $this->config;
    }

}