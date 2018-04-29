<?php
/**
 * Created by PhpStorm.
 * User: seydu
 * Date: 3/20/18
 * Time: 2:06 PM
 */

namespace Seydu\EloquentMetadata\Tests;

use PHPUnit\Framework\TestCase;
use Seydu\EloquentMetadata\Mapping\ArrayDriver;
use Seydu\EloquentMetadata\Mapping\ClassMetadata;
use Seydu\Tests\EloquentMetadata\Models\Comment;
use Seydu\Tests\EloquentMetadata\Models\Model;
use Seydu\Tests\EloquentMetadata\Models\Post;

class DumperTest extends TestCase
{

    private function createDriver()
    {
        return new ArrayDriver([
            Post::class => [
                'table' => 'post',
                'information' => [
                    'sort' => [
                        'field' => 'name',
                        'direction' => 'DESC',
                    ],
                ],
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
    }

    public function testSerializeMetadata()
    {
        $driver = $this->createDriver();
        $metadata = new ClassMetadata(Post::class);
        $driver->loadMetadataForClass(Post::class, $metadata);
        $metadata = unserialize(serialize($metadata));

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


        $defaultSort = $metadata->getInformation('sort');
        $this->assertInternalType('array', $defaultSort);
        $this->assertCount(2, $defaultSort);
        $this->assertArraySubset(
            [
                'field' => 'name',
                'direction' => 'DESC',
            ],
            $defaultSort
        );
    }
}
