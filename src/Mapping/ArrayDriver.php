<?php
/**
 * Created by PhpStorm.
 * User: seydu
 * Date: 4/13/18
 * Time: 3:57 PM
 */

namespace Seydu\EloquentMetadata\Mapping;


class ArrayDriver implements DriverInterface
{
    /**
     * @var array
     */
    private $configuration;

    /**
     * ArrayDriver constructor.
     * @param array $configuration List of class metadata configurations
     */
    public function __construct(array $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * @param ClassMetadataInterface $metadata
     * @param array $data
     */
    private function processMetadata(ClassMetadataInterface $metadata, array $data)
    {
        foreach ($data['associations'] ?? [] as $associationMapping) {
            $type = $associationMapping['type'];
            $metadata->mapAssociation($type, $associationMapping);
        }
    }

    /**
     * @param ClassMetadataInterface $metadata
     * @param array $data
     */
    private function processAssociations(ClassMetadataInterface $metadata, array $data)
    {
        foreach ($data['fields'] ?? [] as $fieldMapping) {
            $metadata->mapField($fieldMapping);
        }
    }

    /**
     * @param ClassMetadataInterface $metadata
     * @param array $data
     */
    private function processFields(ClassMetadataInterface $metadata, array $data)
    {
        $metadata->setInformation('table', $data['table']);
    }

    /**
     * Loads the metadata for the specified class into the provided container.
     *
     * @param string $className
     * @param ClassMetadataInterface $metadata
     *
     * @return void
     */
    public function loadMetadataForClass($className, ClassMetadataInterface $metadata)
    {
        if(!isset($this->configuration[$className])) {
            return;
        }
        $data = $this->configuration[$className];
        $this->processMetadata($metadata, $data);
        $this->processAssociations($metadata, $data);
        $this->processFields($metadata, $data);
    }

    /**
     * Gets the names of all mapped classes known to this driver.
     *
     * @return array The names of all mapped classes known to this driver.
     */
    public function getAllClassNames()
    {
        return array_keys($this->configuration);
    }
}