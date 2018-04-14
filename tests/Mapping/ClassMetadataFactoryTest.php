<?php
/**
 * Created by PhpStorm.
 * User: seydu
 * Date: 3/20/18
 * Time: 2:06 PM
 */

namespace Seydu\EloquentMetadata\Tests;


use PHPUnit\Framework\TestCase;
use Psr\Cache\CacheItemPoolInterface;
use Seydu\EloquentMetadata\Mapping\ClassMetadata;
use Seydu\EloquentMetadata\Mapping\ClassMetadataFactory;
use Seydu\EloquentMetadata\Mapping\DriverInterface;
use Seydu\EloquentMetadata\Tests\Models\Comment;
use Seydu\EloquentMetadata\Tests\Models\Model;
use Seydu\EloquentMetadata\Tests\Models\Post;

class ClassMetadataFactoryTest extends TestCase
{
    private function createClassMetadataFactory($driver = null, $cache = null)
    {
        $factory = new ClassMetadataFactory($driver, $cache);
        return $factory;
    }

    /**
     * Tests constructor
     */
    public function testGetMetadataFor()
    {
        $driver = $this->createMock(DriverInterface::class);
        $cache = $this->createMock(CacheItemPoolInterface::class);
        $factory = $this->createClassMetadataFactory($driver, $cache);
        $metadata = $factory->getMetadataFor(Post::class);
        $this->assertNotEmpty($metadata);
        $this->assertTrue($metadata instanceof ClassMetadata);
    }
}
