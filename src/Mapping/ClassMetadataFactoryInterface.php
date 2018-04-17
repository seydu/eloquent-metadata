<?php

namespace Seydu\EloquentMetadata\Mapping;

/**
 * Contract for a class metadata loader (heavily inspired by Doctrine 2).
 *
 * @author Saidou Gueye <seydu@piyangki.com>
 */
interface ClassMetadataFactoryInterface
{
    /**
     * Forces the factory to load the metadata of all classes known to the underlying
     * mapping driver.
     *
     * @return ClassMetadataInterface[] The ClassMetadata instances of all mapped classes.
     */
    public function getAllMetadata();

    /**
     * Gets the class metadata descriptor for a class.
     *
     * @param string $className The name of the class.
     *
     * @return ClassMetadataInterface
     */
    public function getMetadataFor($className);

    /**
     * Checks whether the factory has the metadata for a class loaded already.
     *
     * @param string $className
     *
     * @return boolean TRUE if the metadata of the class in question is already loaded, FALSE otherwise.
     */
    public function hasMetadataFor($className);

    /**
     * Sets the metadata descriptor for a specific class.
     *
     * @param string $className
     * @param ClassMetadataInterface $metadata
     */
    public function setMetadataFor($className, ClassMetadataInterface $metadata);
}