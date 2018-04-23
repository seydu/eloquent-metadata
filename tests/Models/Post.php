<?php
/**
 * Created by PhpStorm.
 * User: seydu
 * Date: 3/22/18
 * Time: 1:13 PM
 */

namespace Seydu\Tests\EloquentMetadata\Models;

use Seydu\EloquentMetadata\Mapping\Annotations as ModelAnnotations;

/**
 * Class Post
 * @package Seydu\Tests\EloquentMetadata\Models
 * @ModelAnnotations\DefaultSort(field="name")
 */
class Post
{
    /**
     * @ModelAnnotations\OneToMany(targetEntity="Seydu\Tests\EloquentMetadata\Models\Comment", mappedBy="post")
     */
    public function comments()
    {

    }
}