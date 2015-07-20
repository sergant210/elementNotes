<?php

/**
 * Disable a calendar
 */
class elementNoteSaveProcessor extends modObjectProcessor {
	public $objectType = 'elementNote';
	public $classKey = 'elementNote';
	public $languageTopics = array('elementNotes');
	//public $permission = 'save';


	/**
	 * @return array|string
	 */
	public function process() {
		if (!$this->checkPermissions()) {
			return $this->failure($this->modx->lexicon('access_denied'));
		}

		$id = $this->getProperty('id');
		$type = $this->getProperty('type');
		$text = $this->getProperty('text');
		if (empty($id)) {
			return $this->failure($this->modx->lexicon('elementNote_err_no_id'));
		}
		if (empty($type)) {
			return $this->failure($this->modx->lexicon('elementNote_err_no_type'));
		}

		/** @var elementNote $noteObject */
		if (!$noteObject = $this->modx->getObject($this->classKey, array('type'=>$type, 'id'=>$id))) {
			$noteObject = $this->modx->newObject($this->classKey);
			$noteObject->set('type', $type);
			$noteObject->set('id', $id);
		}

		$noteObject->set('text', $text);
		$noteObject->save();

		return $this->success();
	}

}

return 'elementNoteSaveProcessor';
