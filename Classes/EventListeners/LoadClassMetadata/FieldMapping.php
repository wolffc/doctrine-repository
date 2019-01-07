<?php
/**
 * Created by PhpStorm.
 * User: cwolff
 * Date: 09.03.2017
 * Time: 16:22
 */

namespace Wolffc\DocrineRepository\EventListeners\LoadClassMetadata;

use Doctrine\ORM\Mapping\ClassMetadataInfo;

class FieldMapping
{
    /**
     * @var ClassMetadataInfo
     */
    protected $classMetadata;

    /**
     * @var array
     */
    protected $fieldMappings;

    /**
     * @var array
     */
    protected $associationMappings;

    public function __construct(ClassMetadataInfo $classMetaData)
    {
        $this->classMetadata = $classMetaData;
        $this->fieldMappings = $this->classMetadata->fieldMappings;
        $this->associationMappings = $this->classMetadata->associationMappings;
    }

    /**
     * Change a Given Column Name
     * @param $property
     * @param $columnName
     */
    public function changeColumnNameOfProperty($property, $columnName)
    {
        $this->fieldMappings[$property]['columnName'] = $columnName;
    }

    /**
     * Removes a Given Property from the Mapping
     * @param $property
     */
    public function removeProperty($property)
    {
        unset($this->fieldMappings[$property]);
        unset($this->associationMappings[$property]);
    }

    /**
     * Persist the Modified Field Mapping
     */
    public function commit()
    {
        $this->classMetadata->fieldMappings = [];
        $this->classMetadata->fieldNames = [];
        $this->classMetadata->columnNames = [];
        foreach ($this->fieldMappings as $mapping) {
            $this->classMetadata->mapField($mapping);
        }
        $this->classMetadata->associationMappings = $this->associationMappings;
    }

    /**
     * @return array
     */
    public function getFieldMappingArray()
    {
        return $this->fieldMappings;
    }

    /**
     * @return ClassMetadataInfo
     */
    public function getClassMetadata()
    {
        return $this->classMetadata;
    }
}
