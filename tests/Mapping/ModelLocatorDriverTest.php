<?php
/**
 * Created by PhpStorm.
 * User: seydu
 * Date: 3/20/18
 * Time: 2:06 PM
 */

namespace Seydu\EloquentMetadata\Tests;


use Doctrine\Common\Annotations\AnnotationReader;
use PHPUnit\Framework\TestCase;
use Seydu\EloquentMetadata\Mapping\AnnotationDriver;
use Seydu\EloquentMetadata\Mapping\ClassMetadata;
use Seydu\EloquentMetadata\Tests\Models\Comment;
use Seydu\EloquentMetadata\Tests\Models\Model;
use Seydu\EloquentMetadata\Tests\Models\Post;
use Seydu\EloquentMetadata\Tests\Models\Tag;

class AnnotationDriverTest extends TestCase
{

    private function createDriver($reader = null, array $paths = [])
    {
        if(!$reader) {
            $reader = new AnnotationReader();
        }
        if(!$paths) {
            $paths = [__DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'Models'];
        }
        return new AnnotationDriver($reader, $paths);
    }

    public function testGetAllClassNames()
    {
        $driver = $this->createDriver();
        $classNames = $driver->getAllClassNames();
        $this->assertInternalType('array', $classNames);
        $this->assertCount(4    , $classNames);
        $this->assertContains(Post::class, $classNames);
        $this->assertContains(Comment::class, $classNames);
        $this->assertContains(Model::class, $classNames);
        $this->assertContains(Tag::class, $classNames);
    }

    public function testHandleClass()
    {
        $driver = $this->createDriver();
        $this->assertTrue($driver->handlesClass(Post::class));
        $this->assertFalse($driver->handlesClass(Post::class.'_invalid_class'));
    }

    /**
     * Tests constructor
     */
    public function _testGetMetadataForClass()
    {
        $driver = new AnnotationDriver([
            Post::class => [
                'table' => 'post',
                'fields' => [
                    [
                        'fieldName' => 'id',
                        'columnName' => 'id',
                        'id' => true,
                        'type' => 'integer',
                    ],
                    [
                        'fieldName' => 'name',
                        'columnName' => 'title',
                        'id' => false,
                        'type' => 'string',
                        'length' => 60,
                        'unique' => true,
                    ]
                ],
                'associations' => [
                    [
                        'type' => ClassMetadata::ONE_TO_MANY,
                        'fieldName' => 'comments',
                        'targetEntity' => Comment::class,
                        'mappedBy' => 'post',
                    ]
                ]
            ],
        ]);
        $metadata = new ClassMetadata(Post::class);
        $driver->loadMetadataForClass(Post::class, $metadata);
        $this->assertEquals('post', $metadata->getTableName());
        $idMapping = $metadata->getFieldMapping('id');
        $this->assertEquals('id', $idMapping['fieldName']);
        $this->assertArraySubset(
            [
                'fieldName' => 'id',
                'columnName' => 'id',
                'id' => true,
                'type' => 'integer',
            ],
            $idMapping
        );
        $fieldNames = $metadata->getFieldNames();
        $this->assertCount(2, $fieldNames);
        $this->assertArraySubset(['id', 'name'], $fieldNames);

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
    }
}
