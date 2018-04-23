<?php
/**
 * Created by PhpStorm.
 * User: seydu
 * Date: 4/17/18
 * Time: 3:50 PM
 */

namespace Seydu\EloquentMetadata\Mapping\Annotations;


/**
 * @Annotation
 * Description of Association
 *
 */
class Association implements AnnotationInterface
{
    public $type;
    public $sourceRef = null;
    public $destinationRef = null;
    public $mappedBy = null;
    public $inversedBy = null;
    public $owningSite = true;
}
