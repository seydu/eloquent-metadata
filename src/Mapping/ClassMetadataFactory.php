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
     * Gets the class metadata descriptor for a class.
     *
     * @param string $className The name of the class.
     *
     * @return ClassMetadataInterface
     */
    public function getMetadataFor($className)
    {
        if (isset($this->loadedMetadata[$className])) {
            return $this->loadedMetadata[$className];
        }
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