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
 * @Target("CLASS")
 */
class DefaultSort
{
    public $field;
    public $direction = 'ASC';
}