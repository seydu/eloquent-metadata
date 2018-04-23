<?php
/**
 * Created by PhpStorm.
 * User: seydu
 * Date: 3/20/18
 * Time: 2:06 PM
 */

namespace Seydu\EloquentMetadata\Tests;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use PHPUnit\Framework\TestCase;
use Seydu\EloquentMetadata\Mapping\AnnotationDriver;
use Seydu\EloquentMetadata\Mapping\ClassMetadata;
use Seydu\Tests\EloquentMetadata\Models\Comment;
use Seydu\Tests\EloquentMetadata\Models\Post;

class AnnotationDriverTest extends TestCase
{
    protected function setUp()
    {
        AnnotationRegistry::registerUniqueLoader(function ($name) {
            return class_exists($name, true);
        });
    }
    private function createDriver($reader = null)
    {
        if(!$reader) {
            $reader = new AnnotationReader();
        }
        return new AnnotationDriver($reader);
    }

    public function testGetAllClassNames()
    {
        $driver = $this->createDriver();
        $classNames = $driver->getAllClassNames();
        $this->assertInternalType('array', $classNames);
        $this->assertCount(0    , $classNames);
    }


    public function testGetMetadataForClass()
    {
        $driver = $this->createDriver();
        $metadata = new ClassMetadata(Post::class);
        $driver->loadMetadataForClass(Post::class, $metadata);
        $sort = $metadata->getInformation('sort');
        $this->assertEquals('name', $sort['field']);
        $this->assertEquals('ASC', $sort['direction']);

        $commentsMapping = $metadata->getAssociationMapping('comments');
        $this->assertInternalType('array', $commentsMapping);
        $this->assertArraySubset(
            [
                'type' => ClassMetadata::ONE_TO_MANY,
                'fieldName' => 'comments',
                'targetEntity' => Comment::class,
                'mappedBy' => 'post',
            ],
            $commentsMapping
        );
        $associationNames = $metadata->getAssociationNames();
        $this->assertInternalType('array', $associationNames);
        $this->assertCount(1, $associationNames);
        $this->assertArraySubset(['comments'], $associationNames);

        $commentMetadata = new ClassMetadata(Comment::class);
        $driver->loadMetadataForClass(Comment::class, $commentMetadata);
        $sort = $commentMetadata->getInformation('sort');
        $this->assertEquals('createdAt', $sort['field']);
        $this->assertEquals('DESC', $sort['direction']);
        $postMapping = $commentMetadata->getAssociationMapping('post');
        $this->assertInternalType('array', $postMapping);
        $this->assertArraySubset(
            [
                'type' => ClassMetadata::MANY_TO_ONE,
                'fieldName' => 'post',
                'targetEntity' => Post::class,
                'inversedBy' => 'comments',
                'joinColumns' => [
                    'post_id' => [
                        'referencedColumnName' => 'id',
                        'nullable' => false,
                    ]
                ]
            ],
            $postMapping
        );
        $associationNames = $commentMetadata->getAssociationNames();
        $this->assertInternalType('array', $associationNames);
        $this->assertCount(1, $associationNames);
        $this->assertArraySubset(['post'], $associationNames);
    }
}
