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
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;
use Seydu\EloquentMetadata\Mapping\ArrayDriver;
use Seydu\EloquentMetadata\Mapping\ClassMetadata;
use Seydu\EloquentMetadata\Mapping\ClassMetadataFactory;
use Seydu\EloquentMetadata\Mapping\ClassMetadataInterface;
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
    public function testGetMetadataForUndefinedClass()
    {
        $this->expectException(MappingException::class);
        $factory = $this->createClassMetadataFactory();
        $factory->getMetadataFor(Comment::class);
    }

    /**
     *
     */
    public function testGetMetadataFor()
    {
        $factory = $this->createClassMetadataFactory();
        $metadata = $factory->getMetadataFor(Post::class);
        $this->assertNotEmpty($metadata);
        $this->assertTrue($metadata instanceof ClassMetadata);
    }

    public function testHasMetadataFor()
    {
        $factory = $this->createClassMetadataFactory();
        $this->assertFalse($factory->hasMetadataFor(Post::class));
        $factory->getMetadataFor(Post::class);
        $this->assertTrue($factory->hasMetadataFor(Post::class));
    }

    public function testSetMetadataFor()
    {
        $factory = $this->createClassMetadataFactory();
        $this->assertFalse($factory->hasMetadataFor(Post::class));
        $metadata = new ClassMetadata(Post::class);
        $factory->setMetadataFor(Post::class, $metadata);
        $this->assertTrue($factory->hasMetadataFor(Post::class));
    }

    public function testGetMetadataForCacheCalls()
    {
        $cache = $this->createMock(CacheItemPoolInterface::class);
        $cacheItem = $this->createMock(CacheItemInterface::class);
        $metadata = new ClassMetadata(Post::class);
        $cacheItem->method('isHit')->willReturn(true);
        $cacheItem->method('get')->willReturn($metadata);
        $cache->expects($this->once())
            ->method('getItem')
            ->willReturn($cacheItem);
        $factory = $this->createClassMetadataFactory(null, $cache);
        $readMetadata = $factory->getMetadataFor(Post::class);
        $this->assertSame($metadata, $readMetadata);
        //Read again, to make sure the local array only is read
        $readMetadata = $factory->getMetadataFor(Post::class);
        $this->assertSame($metadata, $readMetadata);
    }
}
