<?php
/**
 * Created by PhpStorm.
 * User: seydu
 * Date: 3/20/18
 * Time: 11:53 AM
 */

namespace Seydu\EloquentMetadata\Mapping;

/**
 * Contract for a Doctrine persistence layer ClassMetadata class to implement.
 *
 * @author Saidou Gueye <seydu@piyangki.com>
 */
interface ClassMetadataInterface
{
    /**
     * Sets a class information entry
     *
     * @param $name
     * @param $value
     * @return void
     */
    public function setInformation($name, $value);

    /**
     * Gets class information by name.
     *
     * @return string
     */
    public function getInformation($name);

    /**
     * Gets the fully-qualified class name of this persistent class.
     *
     * @return string
     */
    public function getName();

    /**
     * Gets the database schema name.
     *
     * @return string
     */
    public function getTableName();

    /**
     * Gets the mapped identifier field name.
     *
     * The returned structure is an array of the identifier field names.
     *
     * @return array
     */
    public function getIdentifier();

    /**
     * Checks if the given field name is a mapped identifier for this class.
     *
     * @param string $fieldName
     *
     * @return boolean
     */
    public function isIdentifier($fieldName);

    /**
     * Checks if the given field is a mapped property for this class.
     *
     * @param string $fieldName
     *
     * @return boolean
     */
    public function hasField($fieldName);

    /**
     * Checks if the given field is a mapped association for this class.
     *
     * @param string $fieldName
     *
     * @return boolean
     */
    public function hasAssociation($fieldName);

    /**
     * Checks if the given field is a mapped single valued association for this class.
     *
     * @param string $fieldName
     *
     * @return boolean
     */
    public function isSingleValuedAssociation($fieldName);

    /**
     * Checks if the given field is a mapped collection valued association for this class.
     *
     * @param string $fieldName
     *
     * @return boolean
     */
    public function isCollectionValuedAssociation($fieldName);

    /**
     * A numerically indexed list of field names of this persistent class.
     *
     * This array includes identifier fields if present on this class.
     *
     * @return array
     */
    public function getFieldNames();

    /**
     * Returns an array of identifier field names numerically indexed.
     *
     * @return array
     */
    public function getIdentifierFieldNames();

    /**
     * Returns a numerically indexed list of association names of this persistent class.
     *
     * This array includes identifier associations if present on this class.
     *
     * @return array
     */
    public function getAssociationNames();

    /**
     * Returns a type name of this field.
     *
     * This type names can be implementation specific but should at least include the php types:
     * integer, string, boolean, float/double, datetime.
     *
     * @param string $fieldName
     *
     * @return string
     */
    public function getTypeOfField($fieldName);

    /**
     * Returns the target class name of the given association.
     *
     * @param string $assocName
     *
     * @return string
     */
    public function getAssociationTargetClass($assocName);

    /**
     * Checks if the association is the inverse side of a bidirectional association.
     *
     * @param string $assocName
     *
     * @return boolean
     */
    public function isAssociationInverseSide($assocName);

    /**
     * Returns the target field of the owning side of the association.
     *
     * @param string $assocName
     *
     * @return string
     */
    public function getAssociationMappedByTargetField($assocName);

    /**
     * Returns the identifier of this object as an array with field name as key.
     *
     * Has to return an empty array if no identifier isset.
     *
     * @param object $object
     *
     * @return array
     */
    public function getIdentifierValues($object);

    /**
     * @param array $mapping
     */
    public function mapField(array $mapping);

    /**
     * @param string $type
     * @param array $mapping
     * @return void
     */
    public function mapAssociation($type, $mapping);

    /**
     * @param array $mapping
     */
    public function mapOneToOne(array $mapping);

    /**
     * @param array $mapping
     */
    public function mapManyToOne(array $mapping);

    /**
     * @param array $mapping
     */
    public function mapOneToMany(array $mapping);

    /**
     * @param array $mapping
     */
    public function mapManyToMany(array $mapping);

    /**
     * @param $fieldName
     * @return boolean
     */
    public function isOneToOne($fieldName);

    /**
     * @param $fieldName
     * @return boolean
     */
    public function isManyToOne($fieldName);

    /**
     * @param $fieldName
     * @return boolean
     */
    public function isOneToMany($fieldName);

    /**
     * @param $fieldName
     * @return boolean
     */
    public function isManyToMany  ($fieldName);
}