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
 *
 */
class JoinColumn implements AnnotationInterface
{
    public $name;
    public $referencedColumnName;
    public $nullable = true;
}
