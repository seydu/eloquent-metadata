<?php
/**
 * Created by PhpStorm.
 * User: seydu
 * Date: 3/20/18
 * Time: 2:06 PM
 */

namespace Seydu\EloquentMetadata\Tests;


use Seydu\EloquentMetadata\Mapping\ClassMetadata;
use Seydu\EloquentMetadata\Tests\Models\Comment;
use Seydu\EloquentMetadata\Tests\Models\Model;
use Seydu\EloquentMetadata\Tests\Models\Post;

class ClassMetadataTest extends \PHPUnit\Framework\TestCase
{
    private function createClassMetadata($name = null, $tableName = null, array $data = [])
    {
        $metadata = new ClassMetadata(
            $name ?: Model::class,
            $tableName ?: 'model_table'
        );
        return $metadata;
    }

    /**
     * Tests constructor
     */
    public function testCreateClassMetadata()
    {
        $metadata = $this->createClassMetadata(Model::class,'model_table');
        $this->assertEquals(Model::class, $metadata->getName());
        $this->assertEquals('model_table', $metadata->getTableName());
    }

    /**
     *
     */
    public function testMapField()
    {
        $metadata = $this->createClassMetadata();
        $metadata->mapField([
           'fieldName' => 'id',
           'columnName' => 'id',
           'id' => true,
           'type' => 'integer',

        ]);
        $idMapping = $metadata->getFieldMapping('id');
        $this->assertInternalType('array', $idMapping);
        $this->assertEquals('id', $idMapping['fieldName']);
        $this->assertEquals('id', $idMapping['columnName']);
        $this->assertEquals('integer', $idMapping['type']);
        $this->assertTrue($metadata->isIdentifier('id'));

        $metadata->mapField([
            'fieldName' => 'name',
            'columnName' => 'title',
            'id' => false,
            'type' => 'string',
            'length' => 60,
            'unique' => true,
        ]);
        $nameMapping = $metadata->getFieldMapping('name');
        $this->assertInternalType('array', $nameMapping);
        $this->assertEquals('name', $nameMapping['fieldName']);
        $this->assertEquals('title', $nameMapping['columnName']);
        $this->assertEquals('string', $nameMapping['type']);
        $this->assertFalse($metadata->isIdentifier('name'));
        $this->assertTrue($metadata->isUniqueField('name'));

        $metadata->mapField([
            'fieldName' => 'description',
            'columnName' => 'description',
            'type' => 'text',
        ]);
        $descriptionMapping = $metadata->getFieldMapping('description');
        $this->assertInternalType('array', $descriptionMapping);
        $this->assertEquals('description', $descriptionMapping['fieldName']);
        $this->assertEquals('description', $descriptionMapping['columnName']);
        $this->assertEquals('text', $descriptionMapping['type']);
        $this->assertFalse($metadata->isIdentifier('description'));
        $this->assertFalse($metadata->isUniqueField('description'));

        $this->assertCount(3, $metadata->getFieldNames());
        $this->assertCount(1, $metadata->getIdentifierFieldNames());
    }

    /**
     *
     */
    public function testMapOneToOne()
    {
        $metadata = $this->createClassMetadata();
        $metadata->mapOneToOne([
            'fieldName' => 'oneToOne',
            'targetEntity' => Model::class,
            'joinColumns' => [
                'one_to_one_id' => [
                    'referencedColumnName' => 'id'
                ],
            ],
        ]);
        $mapping = $metadata->getAssociationMapping('oneToOne');
        $this->assertArraySubset(
            [
                'fieldName' => 'oneToOne',
                'targetEntity' => Model::class,
                'joinColumns' => [
                    'one_to_one_id' => [
                        'referencedColumnName' => 'id'
                    ],
                ],
            ],
            $mapping
        );
        $this->assertTrue($metadata->isOneToOne('oneToOne'));
    }

    /**
     *
     */
    public function testMapManyToOne()
    {
        $metadata = $this->createClassMetadata();
        $metadata->mapManyToOne([
            'fieldName' => 'post',
            'targetEntity' => Post::class,
            'joinColumns' => [
                'post_id' => [
                    'referencedColumnName' => 'id'
                ],
            ],
        ]);
        $mapping = $metadata->getAssociationMapping('post');
        $this->assertArraySubset(
            [
                'fieldName' => 'post',
                'targetEntity' => Post::class,
                'joinColumns' => [
                    'post_id' => [
                        'referencedColumnName' => 'id'
                    ],
                ],
            ],
            $mapping
        );
        $this->assertTrue($metadata->isManyToOne('post'));
    }

    /**
     *
     */
    public function testMapOneToMany()
    {
        $metadata = $this->createClassMetadata();
        $metadata->mapManyToOne([
            'fieldName' => 'comments',
            'targetEntity' => Comment::class,
            'mappedBy' => 'post',
        ]);
        $mapping = $metadata->getAssociationMapping('comments');
        $this->assertArraySubset(
            [
                'fieldName' => 'comments',
                'targetEntity' => Comment::class,
                'mappedBy' => 'post',
            ],
            $mapping
        );
        $this->assertTrue($metadata->isOneToMany('post'));
    }

    /**
     *
     */
    public function testMapManyToMany()
    {
        $metadata = $this->createClassMetadata();
        $metadata->mapManyToMany([
            'fieldName' => 'tags',
            'targetEntity' => Tag::class,
            'mappedBy' => 'tags',
            'joinTable' => [
                'name' => 'post_tags',
                'joinColumns' => [
                    'post_id' => [
                        'referencedColumnName' => 'id,'
                    ],
                ],
                'inverseJoinColumns' => [
                    'tag_id' => [
                        'referencedColumnName' => 'id'
                    ],
                ],
            ],
        ]);
        $mapping = $metadata->getAssociationMapping('tags');
        $this->assertArraySubset(
            [
                'fieldName' => 'tags',
                'targetEntity' => Tag::class,
                'mappedBy' => 'tags',
                'joinTable' => [
                    'name' => 'post_tags',
                    'joinColumns' => [
                        'post_id' => [
                            'referencedColumnName' => 'id,'
                        ],
                    ],
                    'inverseJoinColumns' => [
                        'tag_id' => [
                            'referencedColumnName' => 'id'
                        ],
                    ],
                ],
            ],
            $mapping
        );
        $this->assertTrue($metadata->isManyToMany('tags'));
    }

    private function addAssociation(EloquentModelMetadata $metadata, Relation $relation, MmsAnotations\Association $assocAnnotation, $name)
    {
        $mapping = array(
            'fieldName' => $name,
            'targetEntity' => get_class($relation->getRelated())
        );
        switch (true) {
            case $relation instanceof EloquentRelations\BelongsTo;
                $mapping['joinColumns'] = [
                    $relation->getForeignKey() => [
                        'referencedColumnName' => $relation->getOtherKey()
                    ]
                ];
                $metadata->mapManyToOne($mapping);
                break;
            case $relation instanceof EloquentRelations\HasMany;
                $mapping['mappedBy'] = $assocAnnotation->mappedBy;
                $metadata->mapOneToMany($mapping);
                break;
            case $relation instanceof EloquentRelations\BelongsToMany;
                $refColumn = $this->removeTabelNamePrefix(
                    $relation->getTable(),
                    $relation->getForeignKey()
                );
                $inverseRefColumn = $this->removeTabelNamePrefix(
                    $relation->getTable(),
                    $relation->getOtherKey()
                );
                $mapping['joinTable'] = [
                    'name' => $relation->getTable(),
                    'joinColumns' => [
                        $refColumn => [
                            'referencedColumnName' =>
                                $assocAnnotation->sourceRef ?: $this->getModelIdentifier($metadata->name)
                        ]
                    ],
                    'inverseJoinColumns' => [
                        $inverseRefColumn => [
                            'referencedColumnName' =>
                                $assocAnnotation->destinationRef ?: $this->getModelIdentifier($mapping['targetEntity'])
                        ]
                    ],
                    'connectionName' => $assocAnnotation->connectionName
                ];
                $metadata->mapManyToMany($mapping);
                break;
            default:
                throw new MappingException(sprintf(
                    "Relation class '%s' not handled yet, found in '%s'",
                    get_class($relation),
                    $metadata->getName()
                ));
        }
    }
}

/*
 *
 * $mapping = [
            'fieldName' => $column->getName(),
            'type' => $this->getFieldType($column, $metadata),
            'length' => $column->getLength() ?: null,
            'nullable' => !$column->getNotnull(),
            'default' => $column->getDefault(),
            'virtual' => false,
        ];
 */