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
        $formGenAnnotations = $reflection->getAttributes(Formgen::class)[0] ?? null;
        $output = [];
        $fields = [];

        //  Form properties
        if ($formGenAnnotations) {
            $instance = $formGenAnnotations->newInstance();
            $config = $instance->getConfig();
            $output = [...$config, ...$output];
        }

        //  Field properties
        foreach ($reflection->getProperties() as $property) {
            $ignoreAnnotations = $property->getAttributes(FormIgnore::class)[0] ?? null;
            if ($ignoreAnnotations) continue;

            $formAnnotationMapping = self::getFieldConfig($property);
            $type = $formAnnotationMapping['_type'];
            $className = substr(InputAbstract::class, 0, -13) . ucfirst($type);
            $inputObject = new $className();

            foreach ($inputObject->fields() as $field) {
                $inputObject->$field = $formAnnotationMapping[$field] ?? null;
            }

            $inputObject->valid() || throw new FieldException(`Object with id ` . $inputObject->id . ` is not valid!`);

            $fields[] = $inputObject->getData();
        }

        $output['fields'] = [...$fields, ...$output['fields'] ?? []];
        $output = [...$output, ...self::$outputExtra];

        return $output;
    }

    private static function getFieldConfig(\ReflectionProperty $property): array
    {
        $annotationReader = new AnnotationReader();
        $doctrineColumnAnnotations = new ArrayObject(array_values(array_filter($annotationReader->getPropertyAnnotations($property), function ($item) {
            return $item instanceof Column;
        })));

        $doctrineColumnAnnotations['_type'] = $doctrineColumnAnnotations['_type'] ?? 'Text';
        $doctrineColumnAnnotations['unique'] = $doctrineColumnAnnotations['unique'] ?? false;
        $doctrineColumnAnnotations['nullable'] = $doctrineColumnAnnotations['nullable'] ?? false;

        $formFieldAnnotations = $property->getAttributes(FormField::class);
        $formGenAnnotations = count($formFieldAnnotations) ? $formFieldAnnotations[0]->newInstance()->getConfig() : [];

        return [
            ...[
                'id' => $property->getName(),
                '_type' => $formGenAnnotations['_type'] ?? $doctrineColumnAnnotations['_type'],
                'input' => true,
                'label' => 'No label :(',
                'help' => '',
                'unique' => $doctrineColumnAnnotations['unique'],
                'validations' => [],
                'builders' => ['BootstrapBuilder'],
                'autofocus' => false,
                'hidden' => false,
                'multilingual' => false,
                'panel' => null,
                'mask' => '',
                'format' => '',
                'require' => !$doctrineColumnAnnotations['nullable'],
                'placeholder' => '',
                'prefix' => '',
                'suffix' => '',
                'multiple' => false,
                'spellcheck' => false
            ],
            ...$formGenAnnotations
        ];
    }
}
