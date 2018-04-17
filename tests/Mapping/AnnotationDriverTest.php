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
use Seydu\EloquentMetadata\Tests\Models\Post;

class AnnotationDriverTest extends TestCase
{

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
        $this->assertEquals('name', $metadata->getInformation('default_sort'));
    }
}
