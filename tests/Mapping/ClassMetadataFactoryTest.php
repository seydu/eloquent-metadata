<?php
/**
 * Created by PhpStorm.
 * User: seydu
 * Date: 3/20/18
 * Time: 2:06 PM
 */

namespace Seydu\EloquentMetadata\Tests;


use Cache\Adapter\Void\VoidCachePool;
use PHPUnit\Framework\TestCase;
use Psr\Cache\CacheItemPoolInterface;
use Seydu\EloquentMetadata\Mapping\ArrayDriver;
use Seydu\EloquentMetadata\Mapping\ClassMetadata;
use Seydu\EloquentMetadata\Mapping\ClassMetadataFactory;
use Seydu\EloquentMetadata\Mapping\DriverInterface;
use Seydu\EloquentMetadata\Mapping\MappingException;
use Seydu\EloquentMetadata\Tests\Models\Comment;
use Seydu\EloquentMetadata\Tests\Models\Post;

class ClassMetadataFactoryTest extends TestCase
{
    private function getFixturesData()
    {
        return [
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
        ];
    }

    /**
     * @param DriverInterface $driver
     * @param CacheItemPoolInterface $cache
     * @return ClassMetadataFactory
     */
    private function createClassMetadataFactory($driver = null, $cache = null)
    {
        if(!$driver) {
            $driver = new ArrayDriver($this->getFixturesData());
        }
        if(!$cache) {
            $cache = new VoidCachePool();
        }
        $factory = new ClassMetadataFactory($driver, $cache);
        return $factory;
    }

    /**
     *
     */
    public function _testGetMetadataFor()
    {
        $factory = $this->createClassMetadataFactory();
        $metadata = $factory->getMetadataFor(Post::class);
        $this->assertNotEmpty($metadata);
        $this->assertTrue($metadata instanceof ClassMetadata);
    }

    /**
     *
     */
    public function testGetMetadataForUndefinedClass()
    {
        $this->expectException(MappingException::class);
        $factory = $this->createClassMetadataFactory();
        $factory->getMetadataFor(Post::class.'invalid_class');
    }
}
