<?php

namespace Mudde\Formgen4Symfony\Service;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\ORM\Mapping\Column;
use Mudde\Formgen4Symfony\Annotation\FormField;
use Mudde\Formgen4Symfony\Annotation\Formgen;
use Mudde\Formgen4Symfony\Annotation\FormIgnore;
use Mudde\Formgen4Symfony\Exception\FieldException;
use Mudde\Formgen4Symfony\Input\InputAbstract;
use ReflectionClass;

class FormgenService
{

    public function toJson($entity): array
    {
        $reflection = new ReflectionClass($entity);
        $attribute = $reflection->getAttributes(Formgen::class)[0] ?? null;
        $output = [];
        $fields = [];

        //  Form properties
        if ($attribute) {
            $instance = $attribute->newInstance();
            $config = $instance->getConfig();
            $output = array_merge($config, $output);
        }

        //  Field properties
        foreach ($reflection->getProperties() as $property) {
            $ignoreAttribute = $property->getAttributes(FormIgnore::class)[0] ?? null;
            if ($ignoreAttribute)
                continue;

            $formAnnotationMapping = $this->getFieldConfig($property);
            $type = $formAnnotationMapping['_type'];
            $classname = substr(InputAbstract::class, 0, -13) . ucfirst($type);
            $inputObject = new $classname();

            foreach ($inputObject->fields() as $field) {
                $inputObject->$field = $formAnnotationMapping[$field] ?? null;
            }

            $inputObject->valid() || throw new FieldException(`Object with id ${$inputObject->id} is not valid!`);

            $fields[] = $inputObject->getData();
        }

        $output['fields'] = array_merge($fields, $output['fields']);

        return $output;
    }

    private function getFieldConfig($property): array
    {
        $annotationReader = new AnnotationReader();
        $columnMapping = array_values(array_filter($annotationReader->getPropertyAnnotations($property), function ($item) {
            return $item instanceof Column;
        }));
        $columnMapping = array_merge(['_type' => 'Text', 'unique' => false, 'nullable' => false], $columnMapping ?? []);
        $attributes = $property->getAttributes(FormField::class);
        $attribute = count($attributes) > 0 ? $attributes[0]->newInstance()->getConfig() : [];

        return array_merge(
            [
                'id' => $property->getName(),
                '_type' => $formAnnotationMapping['_type'] ?? $formAnnotationMapping['_type'] ?? $columnMapping['_type'],
                'input' => true,
                'label' => 'No label :(',
                'help' => '',
                'unique' => $columnMapping['unique'],
                'validations' => [],
                'builders' => ['BootstrapBuilder'],
                'autofocus' => false,
                'hidden' => false,
                'multilingual' => false,
                'panel' => null,
                'mask' => '',
                'format' => '',
                'require' => !$columnMapping['nullable'],
                'placeholder' => '',
                'prefix' => '',
                'suffix' => '',
                'multiple' => false,
                'spellcheck' => false
            ],
            $attribute
        );

    }

}
