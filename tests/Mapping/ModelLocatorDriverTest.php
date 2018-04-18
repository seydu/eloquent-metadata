<?php
/**
 * Created by PhpStorm.
 * User: seydu
 * Date: 3/20/18
 * Time: 2:06 PM
 */

namespace Seydu\EloquentMetadata\Tests;


use PHPUnit\Framework\TestCase;
use Seydu\EloquentMetadata\Mapping\ClassMetadata;
use Seydu\EloquentMetadata\Mapping\ModelLocatorDriver;
use Seydu\Tests\EloquentMetadata\Models\Comment;
use Seydu\Tests\EloquentMetadata\Models\Model;
use Seydu\Tests\EloquentMetadata\Models\Post;
use Seydu\Tests\EloquentMetadata\Models\Tag;

class ModelLocatorDriverTest extends TestCase
{

    private function createDriver(array $paths = [])
    {
        if(!$paths) {
            $paths = [
                __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'Models'
            ];
        }
        return new ModelLocatorDriver($paths);
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

    public function testGetMetadataForClass()
    {
        $driver = $this->createDriver();
        $metadata = new ClassMetadata(Post::class);
        $driver->loadMetadataForClass(Post::class, $metadata);
        $this->assertEmpty($metadata->getTableName());

        $fieldNames = $metadata->getFieldNames();
        $this->assertInternalType('array', $fieldNames);
        $this->assertEmpty($fieldNames);

        $associationNames = $metadata->getAssociationNames();
        $this->assertInternalType('array', $associationNames);
        $this->assertEmpty($associationNames);
    }
}
