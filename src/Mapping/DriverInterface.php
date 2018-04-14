<?php
/**
 * Created by PhpStorm.
 * User: seydu
 * Date: 4/12/18
 * Time: 12:56 PM
 */

namespace Seydu\EloquentMetadata\Mapping;


interface DriverInterface
{

    /**
     * Loads the metadata for the specified class into the provided container.
     *
     * @param string        $className
     * @param ClassMetadataInterface $metadata
     *
     * @return void
     */
    public function loadMetadataForClass($className, ClassMetadataInterface $metadata);

    /**
     * Gets the names of all mapped classes known to this driver.
     *
     * @return array The names of all mapped classes known to this driver.
     */
    public function getAllClassNames();

}