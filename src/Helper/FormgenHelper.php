<?php

namespace Mudde\Formgen4Symfony\Helper;

use ArrayObject;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\ORM\Mapping\Column;
use Mudde\Formgen4Symfony\Annotation\FormField;
use Mudde\Formgen4Symfony\Exception\FieldException;
use Mudde\Formgen4Symfony\Input\InputAbstract;
use ReflectionClass;

class FormgenHelper
{

    static function toJson($entity): array
    {
        $output = [
            'id' => null,
            "languages" => $languages = new ArrayObject(),
            "buttons" => $buttons = new ArrayObject(),
            "builders" => $builders = new ArrayObject(),
            'fields' => $fields = new ArrayObject(),
            "data" => null,
        ];

        $reflection = new ReflectionClass($entity);
        foreach ($reflection->getProperties() as $property) {
            $fields[] = new ArrayObject();
            $formAnnotationMapping = self::getFieldConfig($$property);
            $type = $formAnnotationMapping['type'];
            $classname = basename(InputAbstract::class) . ucfirst($type);

            /** @var InputAbstract $object */
            $fields[] = $object = new $classname();
            foreach ($object->fields() as $field) {
                $object->$field = $formAnnotationMapping[$field];
            }

            $object->valid() || throw new FieldException(
                `Object with id ${$object->id} is not valid!  ` .
                implode(array_map(
                    function ($item) {
                        return $item->id;
                    },
                    $object->inValidFields()
                )),
                '; '
            );
        }

        return array_map(
            function ($value) {
                return $value instanceof ArrayObject ? (array)$value : $value;
            },
            $output
        );
    }

    private static function getFieldConfig($property): array
    {
        $annotationReader = new AnnotationReader();

        /** @var Column $columnMapping */
        $columnMapping = array_values(array_filter($annotationReader->getPropertyAnnotations($property), function ($item) {
            return $item instanceof Column;
        }));
        $columnMapping = array_merge(['type' => 'Text', 'unique' => false, 'nullable' => false], $columnMapping ?? []);
        $attributes = $property->getAttributes(FormField::class);

        return array_merge(
            count($attributes) > 0 ? $attributes[0]->newInstance()->getConfig() : [],
            [
                'id' => $property->getName(),
                'type' => $formAnnotationMapping['type'] ?? $formAnnotationMapping['_type'] ?? $columnMapping['type'],
                'input' => true,
                'label' => 'No label :(',
                'help' => '',
                'unique' => $columnMapping['unique'],
                'validations' => new ArrayObject(),
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
            ]);

    }

}
