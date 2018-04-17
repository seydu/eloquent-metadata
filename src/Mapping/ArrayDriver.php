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
        $metadata->setInformation('table', $data['table']);
        foreach ($data['information'] ?? [] as $name => $value) {
            $metadata->setInformation($name, $value);
        }
    }

    /**
     * @param ClassMetadataInterface $metadata
     * @param array $data
     */
    private function processAssociations(ClassMetadataInterface $metadata, array $data)
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
    private function processFields(ClassMetadataInterface $metadata, array $data)
    {
        foreach ($data['fields'] ?? [] as $fieldMapping) {
            $metadata->mapField($fieldMapping);
        }
    }

    /**
     * @inheritdoc
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
     * @inheritdoc
     */
    public function getAllClassNames()
    {
        return array_keys($this->configuration);
    }
}