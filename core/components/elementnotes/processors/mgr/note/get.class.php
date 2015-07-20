<?php

/**
 * Get a note for element
 */
class elementNoteGetProcessor extends modObjectGetProcessor {
	public $objectType = 'elementNote';
	public $classKey = 'elementNote';
	public $languageTopics = array('elementnotes:default');
	//public $permission = 'view';


	/**
	 * {@inheritDoc}
	 * @return boolean
	 */
	public function initialize() {
		$id = $this->getProperty('id');
		$type = $this->getProperty('type');
		$primaryKeys =  array('type'=>$type, 'id'=>$id);

		if (empty($primaryKeys)) return $this->modx->lexicon($this->objectType.'_err_nf');
		if (!$this->object = $this->modx->getObject($this->classKey,$primaryKeys)) {
			return $this->modx->lexicon($this->objectType . '_err_nf');
		}

		return true;
	}

	/**
	 * We doing special check of permission
	 * because of our objects is not an instances of modAccessibleObject
	 *
	 * @return mixed
	 */
	public function process() {
		if (!$this->checkPermissions()) {
			return $this->failure($this->modx->lexicon('access_denied'));
		}
		return parent::process();
	}
}

return 'elementNoteGetProcessor';