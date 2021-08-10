<?php

namespace Mudde\Formgen4Symfony\Helper;

use ArrayObject;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\ORM\Mapping\Column;
use Laminas\EventManager\ResponseCollection;
use Mudde\Formgen4Symfony\Annotation\FormField;
use ReflectionClass;

class FormgenHelper
{

    static function toJson($entity): array
    {
        $fields = new ArrayObject();
        $languages = new ArrayObject();
        $buttons = new ArrayObject();
        $builders = new ArrayObject();
        $output = [
            'id' => null,
            "languages" => $languages,
            "buttons" => $buttons,
            "builders" => $builders,
            'fields' => $fields,
            "data" => null,
        ];

        $reflection = new ReflectionClass($entity);
        $annotationReader = new AnnotationReader();
        foreach ($reflection->getProperties() as $property) {
            $fields[] = $field = new ArrayObject();

            /** @var Column $columnMapping */
            $columnMapping = array_values(array_filter($annotationReader->getPropertyAnnotations($property), function ($item) {
                return $item instanceof Column;
            }));
            $columnMapping = array_merge(['type'=>'Text', 'unique'=>false,'nullable'=>false], $columnMapping ?? []);

            /** @var FormField $formAnnotationMapping */
            $attributes = $property->getAttributes(FormField::class);
            $formAnnotationMapping = count($attributes)> 0 ? $attributes[0]->newInstance()->getConfig() : [];

            $field['_type']= $formAnnotationMapping['type'] ?? $formAnnotationMapping['_type'] ?? $columnMapping['type'];
            $field['id'] = $formAnnotationMapping['id'] ?? $property->getName();
            $field['input']= (bool) ($formAnnotationMapping['input'] ?? true);
            $field['label']= $formAnnotationMapping['label'] ?? 'No label :(';
            $field['help']= $formAnnotationMapping['help'] ?? '';
            $field['unique']= $formAnnotationMapping['unique'] ?? $columnMapping['unique'];
            $field['validations']= $formAnnotationMapping['validators'] ?? new ArrayObject();
            $field['builders']= $formAnnotationMapping['builders']?? ['BootstrapBuilder'];
            $field['autofocus']= (bool) ($formAnnotationMapping['autofocus']?? false);
            $field['hidden']=  (bool) ($formAnnotationMapping['hidden'] ?? false);
            $field['multilingual']= (bool) ($formAnnotationMapping['multilingual'] ?? false);
            $field['panel']= $formAnnotationMapping['panel'] ?? null;
            $field['mask']= $formAnnotationMapping['mask'] ?? '';
            $field['format']= $formAnnotationMapping['format'] ?? '';
            $field['require']= $formAnnotationMapping['require'] ?? !$columnMapping['nullable'];
            $field['placeholder']= $formAnnotationMapping['placeholder'] ?? '';
            $field['prefix']= $formAnnotationMapping['prefix'] ?? '';
            $field['suffix']= $formAnnotationMapping['suffix'] ?? '';
            $field['multiple']= (bool) ($formAnnotationMapping['multiple'] ?? false);
            $field['spellcheck']= (bool) ($formAnnotationMapping['spellcheck'] ?? false);
        }

        foreach ($output as $key=>$value){
            if($value instanceof ArrayObject){
                $output[$key]=(array)$value;
            }
        }

        return $output;
    }
}
