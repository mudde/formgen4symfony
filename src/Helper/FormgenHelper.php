<?php

namespace Mudde\Formgen4Symfony\Helper;

use ArrayObject;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\ORM\Mapping\Column;
use Mudde\Formgen4Symfony\Annotation\FormField;
use Mudde\Formgen4Symfony\Annotation\Formgen;
use Mudde\Formgen4Symfony\Annotation\FormIgnore;
use Mudde\Formgen4Symfony\Exception\FieldException;
use Mudde\Formgen4Symfony\Input\InputAbstract;
use ReflectionClass;

class FormgenHelper
{
    public static array $outputExtra = [
        'buttons' =>  [[
            "_type" => "Submit",
            "label" => "Save"
        ]],
        'builders' => [
            "TabsBuilder"
        ]
    ];

    public static function toJson($entity): array
    {
        $reflection = new ReflectionClass($entity);
        $formAnnotations = $reflection->getAttributes(Formgen::class)[0] ?? null;
        $output = [];
        $fields = [];

        //  Form properties
        if ($formAnnotations) {
            $instance = $formAnnotations->newInstance();
            $config = $instance->getConfig();
            $output = $config;
        }

        //  Field properties
        foreach ($reflection->getProperties() as $property) {
            $ignoreAnnotations = $property->getAttributes(FormIgnore::class)[0] ?? null;
            if ($ignoreAnnotations) continue;

            $formAnnotation = self::getFieldConfig($property);
            $type = $formAnnotation['_type'];
            $className = substr(InputAbstract::class, -13) . ucfirst($type);
            $inputObject = new $className();

            foreach ($inputObject->fields() as $field) {
                $inputObject->$field = $formAnnotation[$field] ?? null;
            }

            $inputObject->valid() || throw new FieldException(`Object with id ` . $inputObject->id . ` is not valid!`);

            $fields[] = $inputObject->getData();
        }

        $output['fields'] = [...$fields, ...$output['fields'] ?? []];
        $output = [...$output, ...self::$outputExtra];

        return $output;
    }

    private static function getDoctrineAnnotations(\ReflectionProperty $property): ArrayObject
    {
        $annotationReader = new AnnotationReader();
        $output = new ArrayObject(
            array_values(
                array_filter(
                    $annotationReader->getPropertyAnnotations($property),
                    function ($item) {
                        return $item instanceof Column;
                    }
                )
            )
        );

        $output['_type'] = $output['_type'] ?? 'Text';
        $output['unique'] = $output['unique'] ?? false;
        $output['nullable'] = $output['nullable'] ?? false;

        return $output;
    }

    private static function getFieldConfig(\ReflectionProperty $property): array
    {
        $doctrineAnnotations = self::getDoctrineAnnotations($property);
        $fieldAnnotations = $property->getAttributes(FormField::class);
        $fieldInstance = $fieldAnnotations[0] ?? null;
        $fieldConfig = $fieldInstance ? $fieldInstance->newInstance()->getConfig() : [];

        return [
            ...[
                'id' => $property->getName(),
                '_type' => $formGenAnnotations['_type'] ?? $doctrineAnnotations['_type'],
                'input' => true,
                'label' => 'No label :(',
                'help' => '',
                'unique' => $doctrineAnnotations['unique'],
                'validations' => [],
                'builders' => ['BootstrapBuilder'],
                'autofocus' => false,
                'hidden' => false,
                'multilingual' => false,
                'panel' => null,
                'mask' => '',
                'format' => '',
                'require' => !$doctrineAnnotations['nullable'],
                'placeholder' => '',
                'prefix' => '',
                'suffix' => '',
                'multiple' => false,
                'spellcheck' => false
            ],
            ...$fieldConfig
        ];
    }
}
