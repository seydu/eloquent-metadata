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
 * Class Comment
 * @package Seydu\Tests\EloquentMetadata\Models
 * @ModelAnnotations\DefaultSort(field="createdAt", direction="DESC")
 */
class Comment
{
    /**
     * @ModelAnnotations\ManyToOne(targetEntity="Seydu\Tests\EloquentMetadata\Models\Post", inversedBy="comments")
     * @ModelAnnotations\JoinColumn(name="post_id", referencedColumnName="id", nullable=false)
     */
    public function post()
    {

    }


}