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

    /**
     * @ModelAnnotations\OneToOne(targetEntity="Seydu\Tests\EloquentMetadata\Models\Comment")
     * @ModelAnnotations\JoinColumn(name="first_comment_id", referencedColumnName="id", nullable=true)
     */
    public function firstComment()
    {

    }


    /**
     * @ModelAnnotations\ManyToMany(targetEntity="Seydu\Tests\EloquentMetadata\Models\Tag")
     * @ModelAnnotations\JoinTable(name="post_tags",
     *     joinColumns={@ModelAnnotations\JoinColumn(name="post_id", referencedColumnName="id", nullable=false)},
     *     inverseJoinColumns={@ModelAnnotations\JoinColumn(name="tag_id", referencedColumnName="id", nullable=false)}
     *     )
     */
    public function tags()
    {

    }


}