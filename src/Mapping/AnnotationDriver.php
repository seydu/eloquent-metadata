<?php
/**
 * Created by PhpStorm.
 * User: seydu
 * Date: 4/13/18
 * Time: 3:57 PM
 */

namespace Seydu\EloquentMetadata\Mapping;

use Doctrine\Common\Annotations\AnnotationReader;
use Seydu\EloquentMetadata\Mapping\Annotations;

class AnnotationDriver implements DriverInterface
{
    /**
     * @var AnnotationReader
     */
    private $reader;

    public function __construct(AnnotationReader $reader)
    {
        $this->reader = $reader;
    }

    private function processMetadata(ClassMetadataInterface $metadata, \ReflectionClass $reflectionClass)
    {
        $defaultSortAnnotation = $this->reader->getClassAnnotation(
            $reflectionClass,
            Annotations\DefaultSort::class
        );
        if($defaultSortAnnotation) {
            $metadata->setInformation(
                'sort',
                [
                    'field' => $defaultSortAnnotation->field,
                    'direction' => $defaultSortAnnotation->direction
                ]
            );
        }
    }

    /**
     * @inheritdoc
     */
    public function loadMetadataForClass($className, ClassMetadataInterface $metadata)
    {
        $reflectionClass = new \ReflectionClass($className);
        $this->processMetadata($metadata, $reflectionClass);
    }

    /**
     * @inheritdoc
     */
    public function getAllClassNames()
    {
        return [];
    }
}