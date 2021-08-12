<?php

namespace Mudde\Formgen4Symfony\Input;

abstract class InputAbstract
{
    private array $data = [];

    public function __get(string $name): mixed
    {
        return $this->data[$name] ?? throw new \Exception('Item not set!');
    }

    public function __set(string $name, $value): void
    {
        $this->data[$name] = $value;
    }

    public function hasProperty(): bool
    {
        return isset($this->data['name']);
    }

    public function valid(): bool
    {
        return count($this->inValidFields()) > 0;
    }

    public function inValidFields():array
    {
        return array_filter($this->fields(), function ($field) {
            return !isset($this->data[$field]);
        });
    }


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
            'multilingual',
            'handlerBuilders',
            'handlerValidation',
            'form',
            'coreIds',
            'extraJs',
            'rules'
        ];
    }

}