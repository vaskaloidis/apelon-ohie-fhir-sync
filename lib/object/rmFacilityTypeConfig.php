<?php

namespace Apelon\Object;

/**
 *
 * @author vkaloidis
 *        
 */
class rmFacilityTypeConfig {
	private $collectionId; // 1666
	private $layerId; // 1670
	private $layerName; // Medical Facility Information
	private $layerOrder; // 2
	private $valuesetName; // valueset-c80-facilitycodes
	private $defaultValuesetName; //valueset-c80-facilitycodes
	private $fieldName; // "Facility Type"
	private $fieldCode; // facility_type
	private $fieldOrder; // 1
	private $fieldId;
	private $nextId; // 2
	public function getDefaultValuesetName() {
	    return $this->defaultValuesetName;
	}
	public function setDefaultValuesetName($defaultValuesetName) {
	    $this->defaultValuesetName = $defaultValuesetName;
	}
	public function getFieldId() {
	    return $this->fieldId;
	}
	public function setFieldId($fieldId) {
	    $this->fieldId = $fieldId;
	}
	public function getCollectionId() {
		return $this->collectionId;
	}
	public function setCollectionId($collectionId) {
		$this->collectionId = $collectionId;
	}
	public function getLayerId() {
		return $this->layerId;
	}
	public function setLayerId($layerId) {
		$this->layerId = $layerId;
	}
	public function getLayerName() {
		return $this->layerName;
	}
	public function setLayerName($layerName) {
		$this->layerName = $layerName;
	}
	public function getLayerOrder() {
		return $this->layerOrder;
	}
	public function setLayerOrder($layerOrder) {
		$this->layerOrder = $layerOrder;
	}
	public function getValuesetName() {
		return $this->valuesetName;
	}
	public function setValuesetName($valuesetName) {
		$this->valuesetName = $valuesetName;
	}
	public function getFieldName() {
		return $this->optionName;
	}
	public function setFieldName($fieldName) {
		$this->optionName = $fieldName;
	}
	public function getFieldCode() {
		return $this->optionCode;
	}
	public function setFieldCode($fieldCode) {
		$this->optionCode = $fieldCode;
	}
	public function getFieldOrder() {
		return $this->optionOrder;
	}
	public function setFieldOrder($fieldOrder) {
		$this->optionOrder = $fieldOrder;
	}
	public function getNextId() {
		return $this->nextId;
	}
	public function setNextId($nextId) {
		$this->nextId = $nextId;
	}
}