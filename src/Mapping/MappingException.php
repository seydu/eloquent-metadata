<?php
/**
 * Created by PhpStorm.
 * User: seydu
 * Date: 3/20/18
 * Time: 1:03 PM
 */

namespace Seydu\EloquentMetadata\Mapping;


class MappingException extends \Exception
{
    /**
     * @param string $entity    The entity's name.
     * @param string $fieldName The name of the field that was already declared.
     *
     * @return MappingException
     */
    public static function duplicateFieldMapping($entity, $fieldName)
    {
        return new self('Property "'.$fieldName.'" in "'.$entity.'" was already declared, but it must be declared only once');
    }

    /**
     * @param string $entity
     * @param string $fieldName
     *
     * @return MappingException
     */
    public static function duplicateAssociationMapping($entity, $fieldName)
    {
        return new self('Property "'.$fieldName.'" in "'.$entity.'" was already declared, but it must be declared only once');
    }
    /**
     * @return MappingException
     */
    public static function pathRequired()
    {
        return new self("Specifying the paths to your models is required ".
            "in the AnnotationDriver to retrieve all class names.");
    }

    /**
     * @param string $className
     * @param array  $namespaces
     *
     * @return self
     */
    public static function classNotFoundInNamespaces($className, $namespaces)
    {
        return new self("The class '" . $className . "' was not found in the ".
            "chain configured namespaces " . implode(", ", $namespaces));
    }

    /**
     * @param string|null $path
     *
     * @return self
     */
    public static function fileMappingDriversRequireConfiguredDirectoryPath($path = null)
    {
        if ( ! empty($path)) {
            $path = '[' . $path . ']';
        }

        return new self(
            'File mapping drivers must have a valid directory path, ' .
            'however the given path ' . $path . ' seems to be incorrect!'
        );
    }

    /**
     * @param string $entityName
     * @param string $fileName
     *
     * @return self
     */
    public static function mappingFileNotFound($entityName, $fileName)
    {
        return new self("No mapping file found named '$fileName' for class '$entityName'.");
    }

    /**
     * @param string $entityName
     * @param string $fileName
     *
     * @return self
     */
    public static function invalidMappingFile($entityName, $fileName)
    {
        return new self("Invalid mapping file '$fileName' for class '$entityName'.");
    }

    /**
     * @param string $className
     *
     * @return self
     */
    public static function nonExistingClass($className)
    {
        return new self("Class '$className' does not exist");
    }


    /**
     * @param string $className
     * @param string $fieldName
     *
     * @return MappingException
     */
    public static function mappingNotFound($className, $fieldName)
    {
        return new self("No mapping found for field '$fieldName' on class '$className'.");
    }
}