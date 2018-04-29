<?php
/**
 * Created by PhpStorm.
 * User: seydu
 * Date: 4/10/18
 * Time: 7:59 PM
 */

namespace Seydu\EloquentMetadata\Mapping;

use Psr\Cache\CacheItemPoolInterface;

class ClassMetadataFactory implements ClassMetadataFactoryInterface
{
    /**
     * @var DriverInterface
     */
    private $driver;

    /**
     * @var CacheItemPoolInterface
     */
    private $cache;

    /**
     * @var ClassMetadataInterface[]
     */
    private $loadedMetadata;

    /**
     * ClassMetadataFactory constructor.
     * @param DriverInterface $driver
     * @param CacheItemPoolInterface $cache
     */
    public function __construct(DriverInterface $driver, CacheItemPoolInterface $cache)
    {
        $this->driver         = $driver;
        $this->cache          = $cache;
        $this->loadedMetadata = [];
    }

    /**
     * @inheritdoc
     */
    public function getAllMetadata()
    {
        $metadata = [];
        foreach ($this->driver->getAllClassNames() as $className) {
            $metadata[$className] = $this->getMetadataFor($className);
        }

        return $metadata;
    }

    /**
     * @param string $className
     * @return string
     */
    private function getClassCacheKey($className)
    {
        return str_replace('\\', '-', $className);
    }

    /**
     * @param string $className
     * @return ClassMetadata
     */
    private function newClassMetadata($className)
    {
        return new ClassMetadata($className);
    }

    /**
     * @param string $className
     * @return array
     * @throws MappingException
     */
    private function loadMetadata($className)
    {
        if(!in_array($className, $this->driver->getAllClassNames())) {
            throw new MappingException("Class '$className'  not handled by driver");
        }
        $metadata = $this->newClassMetadata($className);
        $this->loadedMetadata[$className] = $metadata;
        $this->driver->loadMetadataForClass($className, $metadata);
        return [$className];
    }

    /**
     * @inheritdoc
     */
    public function getMetadataFor($className)
    {
        if (isset($this->loadedMetadata[$className])) {
            return $this->loadedMetadata[$className];
        }

        $cacheItemKey = $this->getClassCacheKey($className);
        $cacheItem = $this->cache->getItem($cacheItemKey);
        if($cacheItem->isHit()) {
            $this->loadedMetadata[$className] = $cacheItem->get();
        } else {
            foreach ($this->loadMetadata($className) as $loadedClassName) {
                $loadCacheItemKey = $this->getClassCacheKey($loadedClassName);
                $loadedCacheItem = $this->cache->getItem($loadCacheItemKey);
                if(!$loadedCacheItem->isHit()) {
                    $loadedCacheItem->set($this->loadedMetadata[$loadedClassName]);
                    $this->cache->save($loadedCacheItem);
                }
            }
        }
        if(!isset($this->loadedMetadata[$className])) {
            throw new MappingException("Cannot load metadata for class '$className'");
        }
        return $this->loadedMetadata[$className];
    }


    /**
     * @inheritdoc
     */
    public function hasMetadataFor($className)
    {
        return isset($this->loadedMetadata[$className]);
    }

    /**
     * @inheritdoc
     */
    public function setMetadataFor($className, ClassMetadataInterface $metadata)
    {
        $this->loadedMetadata[$className] = $metadata;
    }
}