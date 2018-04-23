<?php
/**
 * Created by PhpStorm.
 * User: seydu
 * Date: 4/13/18
 * Time: 3:57 PM
 */

namespace Seydu\EloquentMetadata\Mapping;


class ChainDriver implements DriverInterface
{
    /**
     * @var DriverInterface[]
     */
    private $drivers;

    /**
     * ChainDriver constructor.
     * @param DriverInterface[] $drivers List of drivers
     */
    public function __construct(array $drivers)
    {
        $this->drivers = $drivers;
    }

    /**
     * @inheritdoc
     */
    public function loadMetadataForClass($className, ClassMetadataInterface $metadata)
    {
        foreach ($this->drivers as $driver) {
            $driver->loadMetadataForClass($className, $metadata);
        }
    }

    /**
     * @inheritdoc
     */
    public function getAllClassNames()
    {
        $classNames = [];
        foreach ($this->drivers as $driver) {
            $classNames = array_merge($classNames, $driver->getAllClassNames());
        }
        return array_unique($classNames);
    }
}