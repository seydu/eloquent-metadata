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
        if ($defaultSortAnnotation) {
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
     * @param Annotations\JoinColumn[] $joinColumns
     * @return array
     */
    private function processJoinColumns(array $joinColumns)
    {
        $columns = [];
        foreach ($joinColumns as $joinColumn) {
            $columns[$joinColumn->name] = [
                'referencedColumnName' => $joinColumn->referencedColumnName,
                'nullable' => $joinColumn->nullable
            ];
        }
        return $columns;
    }

    private function mapAssociationMappingForOneToMany(
        ClassMetadataInterface $metadata,
        Annotations\OneToMany $annotation,
        \ReflectionMethod $reflectionMethod
    )
    {
        $mapping = [
            'fieldName' => $reflectionMethod->getName(),
            'targetEntity' => $annotation->targetEntity,
            'mappedBy' => $annotation->mappedBy,
            'inversedBy' => $annotation->inversedBy
        ];
        $metadata->mapOneToMany($mapping);
    }

    /**
     * @param ClassMetadataInterface $metadata
     * @param Annotations\ManyToOne $annotation
     * @param \ReflectionMethod $reflectionMethod
     * @param Annotations\JoinColumn[] $joinColumns
     */
    private function mapAssociationMappingForManyToOne(
        ClassMetadataInterface $metadata,
        Annotations\ManyToOne $annotation,
        \ReflectionMethod $reflectionMethod,
        array $joinColumns
    )
    {
        $mapping = [
            'fieldName' => $reflectionMethod->getName(),
            'targetEntity' => $annotation->targetEntity,
            'mappedBy' => $annotation->mappedBy,
            'inversedBy' => $annotation->inversedBy,
            'joinColumns' => $this->processJoinColumns($joinColumns),
        ];
        $metadata->mapManyToOne($mapping);
    }

    /**
     * @param ClassMetadataInterface $metadata
     * @param Annotations\OneToOne $annotation
     * @param \ReflectionMethod $reflectionMethod
     * @param Annotations\JoinColumn[] $joinColumns
     */
    private function mapAssociationMappingForOneToOne(
        ClassMetadataInterface $metadata,
        Annotations\OneToOne $annotation,
        \ReflectionMethod $reflectionMethod,
        array $joinColumns
    )
    {
        $mapping = [
            'fieldName' => $reflectionMethod->getName(),
            'targetEntity' => $annotation->targetEntity,
            'mappedBy' => $annotation->mappedBy,
            'inversedBy' => $annotation->inversedBy,
            'joinColumns' => $this->processJoinColumns($joinColumns),
        ];
        $metadata->mapOneToOne($mapping);
    }

    /**
     * @param ClassMetadataInterface $metadata
     * @param Annotations\ManyToMany $annotation
     * @param \ReflectionMethod $reflectionMethod
     * @param Annotations\JoinColumn[] $joinColumns
     */
    private function mapAssociationMappingForManyToMany(
        ClassMetadataInterface $metadata,
        Annotations\ManyToMany $annotation,
        \ReflectionMethod $reflectionMethod,
        array $joinColumns
    )
    {
        /**
         * @var Annotations\JoinTable $joinTableAnnotation
         */
        $joinTableAnnotation = $this->reader->getMethodAnnotation(
            $reflectionMethod,
            Annotations\JoinTable::class
        );
        $mapping = [
            'fieldName' => $reflectionMethod->getName(),
            'targetEntity' => $annotation->targetEntity,
            'mappedBy' => $annotation->mappedBy,
            'inversedBy' => $annotation->inversedBy,
            'joinTable' => [
                'name' =>$joinTableAnnotation->name,
                'joinColumns' => $this->processJoinColumns($joinTableAnnotation->joinColumns),
                'inversedJoinColumns' => $this->processJoinColumns($joinTableAnnotation->inverseJoinColumns),
            ],
        ];
        $metadata->mapManyToMany($mapping);
    }
    private function processAssociations(ClassMetadataInterface $metadata, \ReflectionClass $reflectionClass)
    {
        $associations = array();
        $annotations = $reflectionClass->getMethods(\ReflectionMethod::IS_PUBLIC);
        foreach ($annotations as $reflectionMethod) {
            $annotationClasses = [
                'OneToMany' => Annotations\OneToMany::class,
                'ManyToOne' => Annotations\ManyToOne::class,
                'OneToOne' => Annotations\OneToOne::class,
                'ManyToMany' => Annotations\ManyToMany::class,
            ];
            $associationAnnotation = null;
            foreach ($annotationClasses as $name => $class) {
                $associationAnnotation =
                    $this->reader->getMethodAnnotation($reflectionMethod, $class);
                if (!$associationAnnotation) {
                    continue;
                }
                $joinColumnAnnotation = $this->reader->getMethodAnnotation(
                    $reflectionMethod,
                    Annotations\JoinColumn::class
                );
                $configMethod = "mapAssociationMappingFor$name";
                $this->$configMethod(
                    $metadata,
                    $associationAnnotation,
                    $reflectionMethod,
                    $joinColumnAnnotation ? [$joinColumnAnnotation] : []
                );
                break;
            }
            if($associationAnnotation) {
                continue;
            }
        }
        return $associations;
    }

    /**
     * @inheritdoc
     */
    public function loadMetadataForClass($className, ClassMetadataInterface $metadata)
    {
        $reflectionClass = new \ReflectionClass($className);
        $this->processMetadata($metadata, $reflectionClass);
        $this->processAssociations($metadata, $reflectionClass);
    }

    /**
     * @inheritdoc
     */
    public function getAllClassNames()
    {
        return [];
    }
}