<?php

namespace Mudde\Formgen4Symfony\Input;

abstract class InputAbstract
{
    private array $data = [];

    public function fields(): array
    {
        return [
            '_type',
            'id',
            'label',
            'help',
            'placeholder',
            'panel',
            'unique',
            'input',
            'autofocus',
            'hidden',
            'require',
            'readonly',
            'multilingual',
            'builders',
            'validation',
            'form',
            'coreIds',
            'extraJs',
            'rules'
        ];
    }

    public function valid(): bool
    {
        return count($this->inValidFields()) > 0;
    }

    public function inValidFields(): array
    {
        return array_filter($this->fields(), function ($field) {
            return !isset($this->data[$field]);
        });
    }

    public function getData(): array
    {
        return $this->data;
    }

}