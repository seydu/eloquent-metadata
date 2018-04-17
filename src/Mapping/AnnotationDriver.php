<?php
/**
 * Created by PhpStorm.
 * User: seydu
 * Date: 4/13/18
 * Time: 3:57 PM
 */

namespace Seydu\EloquentMetadata\Mapping;

use Doctrine\Common\Annotations\AnnotationReader;

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

    /**
     * @inheritdoc
     */
    public function loadMetadataForClass($className, ClassMetadataInterface $metadata)
    {

    }

    /**
     * @inheritdoc
     */
    public function getAllClassNames()
    {
        return [];
    }
}