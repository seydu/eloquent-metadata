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

    public function __construct(DriverInterface $driver, CacheItemPoolInterface $cache)
    {
        $this->driver         = $driver;
        $this->cache          = $cache;
        $this->loadedMetadata = [];
    }

    /**
     * Forces the factory to load the metadata of all classes known to the underlying
     * mapping driver.
     *
     * @return ClassMetadataInterface[] The ClassMetadata instances of all mapped classes.
     */
    public function getAllMetadata()
    {
        $metadata = [];
        foreach ($this->driver->getAllClassNames() as $className) {
            $metadata[] = $this->getMetadataFor($className);
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
     * @param $className
     * @return ClassMetadata
     */
    private function newClassMetadata($className)
    {
        return new ClassMetadata($className);
    }

    /**
     * @param $className
     * @return array
     * @throws MappingException
     */
    private function loadMetadata($className)
    {
        if(!$this->driver->handlesClass($className)) {
            throw new MappingException("Class '$className'  not handled by driver");
        }
        $metadata = $this->newClassMetadata($className);
        $this->loadedMetadata[$className] = $metadata;
        $this->driver->loadMetadataForClass($className, $metadata);
        return [$className];
    }

    /**
     * Gets the class metadata descriptor for a class.
     *
     * @param string $className The name of the class.
     *
     * @return ClassMetadataInterface
     * @throws MappingException
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
     * Checks whether the factory has the metadata for a class loaded already.
     *
     * @param string $className
     *
     * @return boolean TRUE if the metadata of the class in question is already loaded, FALSE otherwise.
     */
    public function hasMetadataFor($className)
    {
        // TODO: Implement hasMetadataFor() method.
    }

    /**
     * Sets the metadata descriptor for a specific class.
     *
     * @param string $className
     * @param ClassMetadataInterface $class
     */
    public function setMetadataFor($className, ClassMetadataInterface $class)
    {
        // TODO: Implement setMetadataFor() method.
    }
}