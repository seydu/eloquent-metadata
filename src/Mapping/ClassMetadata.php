<?php
/**
 * Created by PhpStorm.
 * User: seydu
 * Date: 3/20/18
 * Time: 12:08 PM
 */

namespace Seydu\EloquentMetadata\Mapping;


class ClassMetadata implements ClassMetadataInterface
{
    /**
     * Identifies a one-to-one association.
     */
    const ONE_TO_ONE = 1;

    /**
     * Identifies a many-to-one association.
     */
    const MANY_TO_ONE = 2;

    /**
     * Identifies a one-to-many association.
     */
    const ONE_TO_MANY = 4;

    /**
     * Identifies a many-to-many association.
     */
    const MANY_TO_MANY = 8;

    /**
     * Combined bitmask for to-one (single-valued) associations.
     */
    const TO_ONE = 3;

    /**
     * Combined bitmask for to-many (collection-valued) associations.
     */
    const TO_MANY = 12;

    private $information = [];

    /**
     * READ-ONLY: The name of the entity class.
     *
     * @var string
     */
    private $name;

    /**
     * READ-ONLY: The field names of all fields that are part of the identifier/primary key
     * of the mapped entity class.
     *
     * @var array
     */
    private $identifier = array();

    /**
     * READ-ONLY: The field mappings of the class.
     * Keys are field names and values are mapping definitions.
     *
     * The mapping definition array has the following values:
     *
     * - <b>fieldName</b> (string)
     * The name of the field in the Entity.
     *
     * - <b>type</b> (string)
     * The type name of the mapped field. Can be one of Doctrine's mapping types
     * or a custom mapping type.
     *
     * - <b>columnName</b> (string, optional)
     * The column name. Optional. Defaults to the field name.
     *
     * - <b>length</b> (integer, optional)
     * The database length of the column. Optional. Default value taken from
     * the type.
     *
     * - <b>id</b> (boolean, optional)
     * Marks the field as the primary key of the entity. Multiple fields of an
     * entity can have the id attribute, forming a composite key.
     *
     * - <b>nullable</b> (boolean, optional)
     * Whether the column is nullable. Defaults to FALSE.
     *
     * - <b>columnDefinition</b> (string, optional, schema-only)
     * The SQL fragment that is used when generating the DDL for the column.
     *
     * - <b>precision</b> (integer, optional, schema-only)
     * The precision of a decimal column. Only valid if the column type is decimal.
     *
     * - <b>scale</b> (integer, optional, schema-only)
     * The scale of a decimal column. Only valid if the column type is decimal.
     *
     * - <b>'unique'</b> (string, optional, schema-only)
     * Whether a unique constraint should be generated for the column.
     *
     * @var array
     */

    private $fieldMappings = array();

    /**
     * READ-ONLY: The association mappings of this class.
     *
     * The mapping definition array supports the following keys:
     *
     * - <b>fieldName</b> (string)
     * The name of the field in the entity the association is mapped to.
     *
     * - <b>targetEntity</b> (string)
     * The class name of the target entity. If it is fully-qualified it is used as is.
     * If it is a simple, unqualified class name the namespace is assumed to be the same
     * as the namespace of the source entity.
     *
     * - <b>mappedBy</b> (string, required for bidirectional associations)
     * The name of the field that completes the bidirectional association on the owning side.
     * This key must be specified on the inverse side of a bidirectional association.
     *
     * - <b>inversedBy</b> (string, required for bidirectional associations)
     * The name of the field that completes the bidirectional association on the inverse side.
     * This key must be specified on the owning side of a bidirectional association.
     *
     * - <b>cascade</b> (array, optional)
     * The names of persistence operations to cascade on the association. The set of possible
     * values are: "persist", "remove", "detach", "merge", "refresh", "all" (implies all others).
     *
     * - <b>orderBy</b> (array, one-to-many/many-to-many only)
     * A map of field names (of the target entity) to sorting directions (ASC/DESC).
     * Example: array('priority' => 'desc')
     *
     * - <b>fetch</b> (integer, optional)
     * The fetching strategy to use for the association, usually defaults to FETCH_LAZY.
     * Possible values are: ClassMetadata::FETCH_EAGER, ClassMetadata::FETCH_LAZY.
     *
     * - <b>joinTable</b> (array, optional, many-to-many only)
     * Specification of the join table and its join columns (foreign keys).
     * Only valid for many-to-many mappings. Note that one-to-many associations can be mapped
     * through a join table by simply mapping the association as many-to-many with a unique
     * constraint on the join table.
     *
     * - <b>indexBy</b> (string, optional, to-many only)
     * Specification of a field on target-entity that is used to index the collection by.
     * This field HAS to be either the primary key or a unique column. Otherwise the collection
     * does not contain all the entities that are actually related.
     *
     * A join table definition has the following structure:
     * <pre>
     * array(
     *     'name' => <join table name>,
     *      'joinColumns' => array(<join column mapping from join table to source table>),
     *      'inverseJoinColumns' => array(<join column mapping from join table to target table>)
     * )
     * </pre>
     *
     * @var array
     */
    private $associationMappings = array();

    /**
     * Table name
     */
    private $tableName;

    /**
     * READ-ONLY: Flag indicating whether the identifier/primary key of the class is composite.
     *
     * @var boolean
     */
    private $isIdentifierComposite = false;

    /**
     *
     * @var string
     */
    private $defaultSortField;

    /**
     *
     * @var string
     */
    private $defaultSortDirection;

    /**
     *
     * @var string
     */
    private $connectionName;

    /**
     * @var array
     */
    private $lifecycleHooks = [];

    /**
     *
     * @var array
     */
    private static $associationTypes = [
        self::ONE_TO_ONE     => 'ONE_TO_ONE',
        self::ONE_TO_MANY    => 'ONE_TO_MANY',
        self::MANY_TO_MANY   => 'MANY_TO_MANY',
        self::MANY_TO_ONE    => 'MANY_TO_ONE',
        self::TO_ONE         => 'TO_ONE',
        self::TO_MANY        => 'TO_MANY',
    ];

    /**
     * Determines which fields get serialized.
     *
     * It only serializeS what is necessary for best unserialization performance.
     * That means any metadata properties that are not set or empty or simply have
     * their default value are NOT serialized.
     *
     * Parts that are also NOT serialized because they can not be properly unserialized:
     *      - reflClass (ReflectionClass)
     *      - reflFields (ReflectionProperty array)
     *
     * @return array The names of all the fields that should be serialized.
     */
    public function __sleep()
    {
        // This metadata is always serialized/cached.
        return [
            //'fieldNames',
            //'embeddedClasses',
            'identifier',
            'code',
            'name',
            'namespace', // TODO: REMOVE
            'table',
            'rootEntityName',
            'defaultSortField',
            'defaultSortDirection',
            'connectionName',
            'connectionPath',
            'schemaTable',
            'lifecycleHooks',
            'associationMappings',
            'columnNames', //TODO: Not really needed. Can use fieldMappings[$fieldName]['columnName']
            'fieldMappings',
        ];
    }

    public function __construct($name)
    {
        $this->name      = $name;
    }

    /**
     * {@inheritDoc}
     */
    public function getFieldNames()
    {
        return array_keys($this->fieldMappings);
    }

    /**
     * {@inheritDoc}
     */
    public function getAssociationNames()
    {
        return array_keys($this->associationMappings);
    }

    /**
     * @param string $assocName
     * @return mixed
     */
    public function getAssociationTargetClass($assocName)
    {
        if ( ! isset($this->associationMappings[$assocName])) {
            throw new \InvalidArgumentException("Association name expected, '" . $assocName ."' is not an association.");
        }

        return $this->associationMappings[$assocName]['targetEntity'];
    }

    /**
     * @return array
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * @return mixed
     */
    public function getSingleIdentifier()
    {
        if ($this->isIdentifierComposite) {
            throw new \InvalidArgumentException("Class " . $this->name . " has a composite identifier.");
        }
        return $this->identifier[0];
    }

    /**
     * {@inheritDoc}
     */
    public function getIdentifierFieldNames()
    {
        return $this->identifier;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;

    }

    /**
     * @return string
     */
    public function getTableName()
    {
        return $this->getInformation('table');

    }

    /**
     * Gets the type of a field.
     *
     * @param string $fieldName
     *
     * @return string|null
     */
    public function getTypeOfField($fieldName)
    {
        return isset($this->fieldMappings[$fieldName]) ?
            $this->fieldMappings[$fieldName]['type'] : null;
    }

    /**
     * {@inheritDoc}
     */
    public function hasAssociation($fieldName)
    {
        return isset($this->associationMappings[$fieldName]);
    }

    /**
     * {@inheritDoc}
     */
    public function hasField($fieldName)
    {
        return isset($this->fieldMappings[$fieldName]);
    }

    /**
     * @param string $assocName
     * @return bool
     */
    public function isAssociationInverseSide($assocName)
    {
        throw new \RuntimeException(sprintf("Method %s not implemented yet", __METHOD__));
    }

    /**
     * @param $fieldName
     * @return bool
     */
    public function isCollectionValuedAssociation($fieldName)
    {
        return isset($this->associationMappings[$fieldName])
            && ! ($this->associationMappings[$fieldName]['type'] & self::TO_ONE);
    }

    /**
     * @param $fieldName
     * @return bool
     */
    public function isOneToOne($fieldName)
    {
        return isset($this->associationMappings[$fieldName])
            && ($this->associationMappings[$fieldName]['type'] === self::ONE_TO_ONE);
    }

    /**
     * @param $fieldName
     * @return bool
     */
    public function isManyToOne($fieldName)
    {
        return isset($this->associationMappings[$fieldName])
            && ($this->associationMappings[$fieldName]['type'] === self::MANY_TO_ONE);
    }

    /**
     * @param $fieldName
     * @return bool
     */
    public function isManyToMany($fieldName)
    {
        return isset($this->associationMappings[$fieldName])
            && ($this->associationMappings[$fieldName]['type'] === self::MANY_TO_MANY);
    }

    /**
     * @param $fieldName
     * @return bool
     */
    public function isOneToMany($fieldName)
    {
        return isset($this->associationMappings[$fieldName])
            && ($this->associationMappings[$fieldName]['type'] === self::ONE_TO_MANY);
    }

    /**
     * @param string $fieldName
     * @return bool
     */
    public function isIdentifier($fieldName)
    {
        return in_array($fieldName, $this->identifier);
    }

    /**
     * {@inheritDoc}
     */
    public function isSingleValuedAssociation($fieldName)
    {
        return isset($this->associationMappings[$fieldName]) &&
            ($this->associationMappings[$fieldName]['type'] & self::TO_ONE);
    }
    /**
     * Gets the mapping of a (regular) field that holds some data but not a
     * reference to another object.
     *
     * @param string $fieldName The field name.
     *
     * @return array The field mapping.
     *
     * @throws MappingException
     */
    public function getFieldMapping($fieldName)
    {
        if ( ! isset($this->fieldMappings[$fieldName])) {
            throw new MappingException(sprintf(
                "Cannot find field mapping '%s' in class '%s'",
                $fieldName, $this->name
            ));
        }
        return $this->fieldMappings[$fieldName];
    }

    /**
     * Gets the mapping of an association.
     *
     * @see ClassMetadataInfo::$associationMappings
     *
     * @param string $fieldName The field name that represents the association in
     *                          the object model.
     *
     * @return array The mapping.
     *
     * @throws MappingException
     */
    public function getAssociationMapping($fieldName)
    {
        if ( ! isset($this->associationMappings[$fieldName])) {
            throw new MappingException(sprintf(
                "Cannot find field mapping '%s' in class '%s'",
                $fieldName, $this->name
            ));
        }
        return $this->associationMappings[$fieldName];
    }


    /**
     * @param string $fieldName
     * @throws MappingException
     */
    private function assertFieldNotMapped($fieldName)
    {
        if (isset($this->fieldMappings[$fieldName]) ||
            isset($this->associationMappings[$fieldName]) ||
            isset($this->embeddedClasses[$fieldName])) {
            throw new MappingException(sprintf(
                "Duplicate field mapping '%s' in class '%s'",
                $fieldName, $this->name
            ));
        }
    }

    /**
     * Validates & completes the given field mapping.
     *
     * @param array $mapping The field mapping to validate & complete.
     *
     * @return array The validated and completed field mapping.
     *
     * @throws MappingException
     */
    protected function _validateAndCompleteFieldMapping(array &$mapping)
    {
        // Check mandatory fields
        if ( ! isset($mapping['fieldName']) || strlen($mapping['fieldName']) == 0) {
            throw MappingException::missingFieldName($this->name);
        }
        if ( ! isset($mapping['type'])) {
            // Default to string
            $mapping['type'] = 'string';
        }

        // Complete fieldName and columnName mapping
        if ( ! isset($mapping['columnName'])) {
            $mapping['columnName'] = $mapping['fieldName'];
        }

        if ($mapping['columnName'][0] === '`') {
            $mapping['columnName']  = trim($mapping['columnName'], '`');
            $mapping['quoted']      = true;
        }

        $this->columnNames[$mapping['fieldName']] = $mapping['columnName'];
        if (isset($this->fieldNames[$mapping['columnName']])) {
            throw MappingException::duplicateColumnName($this->name, $mapping['columnName']);
        }

        $this->fieldNames[$mapping['columnName']] = $mapping['fieldName'];

        // Complete id mapping
        if (isset($mapping['id']) && $mapping['id'] === true) {

            if ( ! in_array($mapping['fieldName'], $this->identifier)) {
                $this->identifier[] = $mapping['fieldName'];
            }
            // Check for composite key
            if ( ! $this->isIdentifierComposite && count($this->identifier) > 1) {
                $this->isIdentifierComposite = true;
            }
        }
    }

    /**
     * Stores the association mapping.
     *
     * @param array $assocMapping
     *
     * @return void
     *
     * @throws MappingException
     */
    protected function _storeAssociationMapping(array $assocMapping)
    {
        $sourceFieldName = $assocMapping['fieldName'];

        $this->assertFieldNotMapped($sourceFieldName);
        if(!isset($assocMapping['targetEntity'])) {
            throw new MappingException(sprintf(
               "Association '%s' in '%s' does not defined target entity",
               $sourceFieldName,
               $this->getName()
            ));
        }

        $this->associationMappings[$sourceFieldName] = $assocMapping;
    }

    /**
     * Adds a mapped field to the class.
     *
     * @param array $mapping The field mapping.
     *
     * @return void
     *
     * @throws MappingException
     */
    public function mapField(array $mapping)
    {
        $this->_validateAndCompleteFieldMapping($mapping);
        $this->assertFieldNotMapped($mapping['fieldName']);
        $this->fieldMappings[$mapping['fieldName']] = $mapping;
    }

    /**
     * @param string $type
     * @param array $mapping
     * @return void
     * @throws MappingException
     */
    public function mapAssociation($type, $mapping)
    {
        switch ($type) {
            case self::ONE_TO_ONE:
                $this->mapOneToOne($mapping);
                break;
            case self::ONE_TO_MANY:
                $this->mapOneToMany($mapping);
                break;
            case self::MANY_TO_ONE:
                $this->mapManyToOne($mapping);
                break;
            case self::MANY_TO_MANY:
                $this->mapManyToMany($mapping);
                break;
            default:
                throw new MappingException(
                    "Invalid association mapping type '$type'"
                );
        }
    }

    /**
     * Adds a one-to-one mapping.
     *
     * @param array $mapping The mapping.
     *
     * @return void
     */
    public function mapOneToOne(array $mapping)
    {
        $mapping['type'] = self::ONE_TO_ONE;
        $this->_storeAssociationMapping($mapping);
    }

    /**
     * Adds a many-to-one mapping.
     *
     * @param array $mapping The mapping.
     *
     * @return void
     */
    public function mapManyToOne(array $mapping)
    {
        $mapping['type'] = self::MANY_TO_ONE;
        $this->_storeAssociationMapping($mapping);
    }

    /**
     * Adds a one-to-many mapping.
     *
     * @param array $mapping The mapping.
     *
     * @return void
     * @throws MappingException
     */
    public function mapOneToMany(array $mapping)
    {
        $mapping['type'] = self::ONE_TO_MANY;
        //If it does not specify a 'mapped' by attribute, there should be 'joinColumns'
        if(empty($mapping['mappedBy']) && empty($mapping['joinColumns'])) {
            throw new MappingException(sprintf(
                "No 'mappedBy' or 'joinColumns' attribute for association '%s' in '%s'",
                $mapping['fieldName'],
                $this->getName()
            ));
        }
        $this->_storeAssociationMapping($mapping);
    }

    /**
     * Adds a many-to-many mapping.
     *
     * @param array $mapping The mapping.
     *
     * @return void
     */
    public function mapManyToMany(array $mapping)
    {
        $mapping['type'] = self::MANY_TO_MANY;
        $this->_storeAssociationMapping($mapping);
    }


    /**
     * Is this an association that only has a single join column?
     *
     * @param string $fieldName
     *
     * @return bool
     */
    public function isAssociationWithSingleJoinColumn($fieldName)
    {
        return (
            isset($this->associationMappings[$fieldName]) &&
            isset($this->associationMappings[$fieldName]['joinColumns'][0]) &&
            !isset($this->associationMappings[$fieldName]['joinColumns'][1])
        );
    }


    /**
     * Checks if the field is unique.
     *
     * @param string $fieldName The field name.
     *
     * @return boolean TRUE if the field is unique, FALSE otherwise.
     */
    public function isUniqueField($fieldName)
    {
        $mapping = $this->getFieldMapping($fieldName);
        if ($mapping !== false) {
            return isset($mapping['unique']) && $mapping['unique'] == true;
        }
        return false;
    }

    /**
     * Checks if the field is not null.
     *
     * @param string $fieldName The field name.
     *
     * @return boolean TRUE if the field is not null, FALSE otherwise.
     */
    public function isNullable($fieldName)
    {
        $mapping = $this->getFieldMapping($fieldName);
        if ($mapping !== false) {
            return isset($mapping['nullable']) && $mapping['nullable'] == true;
        }
        return false;
    }

    /**
     *
     * @return array
     */
    public function getVirtualFieldMappings()
    {
        $result = [];
        foreach ($this->fieldMappings as $fieldName => $mapping) {
            if(!empty($mapping['virtual'])) {
                $result[$fieldName] = $mapping;
            }
        }
        return $result;
    }

    /**
     *
     * @param string $fieldName
     * @return boolean
     */
    public function isVirtualField($fieldName)
    {
        return !empty($this->fieldMappings[$fieldName]['virtual']);
    }

    /**
     * Returns the target field of the owning side of the association.
     *
     * @param string $assocName
     *
     * @return string
     */
    public function getAssociationMappedByTargetField($assocName)
    {
        // TODO: Implement getAssociationMappedByTargetField() method.
    }

    /**
     * Returns the identifier of this object as an array with field name as key.
     *
     * Has to return an empty array if no identifier isset.
     *
     * @param object $object
     *
     * @return array
     */
    public function getIdentifierValues($object)
    {
        // TODO: Implement getIdentifierValues() method.
    }

    /**
     * Sets a class information entry
     *
     * @param $name
     * @param $value
     * @return void
     */
    public function setInformation($name, $value)
    {
        $this->information[$name] = $value;
    }

    /**
     * Gets class information by name.
     *
     * @param $name
     * @return string
     */
    public function getInformation($name)
    {
        return $this->information[$name] ?? null;
    }
}
