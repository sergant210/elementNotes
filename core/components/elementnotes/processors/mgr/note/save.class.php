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
		$name = $this->getProperty('name');
		if (empty($id)) {
			return $this->failure($this->modx->lexicon('elementNote_err_no_id'));
		}
		if (empty($type)) {
			return $this->failure($this->modx->lexicon('elementNote_err_no_type'));
		}
		if (empty($name)) {
			return $this->failure($this->modx->lexicon('elementNote_err_no_name'));
		}

		/** @var elementNote $noteObject */
		if (!$noteObject = $this->modx->getObject($this->classKey, array('type'=>$type, 'id'=>$id))) {
			if (!$noteObject = $this->modx->getObject($this->classKey, array('type'=>$type, 'name'=>$name))) {
				$noteObject = $this->modx->newObject($this->classKey);
				$noteObject->set('type', $type);
				$noteObject->set('id', $id);
			} else {
				//$noteObject->set('id', $id);
				$query = $this->modx->newQuery($this->classKey);
				$query->command('UPDATE');
				$query->set(array(
					'id'=>$id,
					'text'=>$text
				));
				$query->where(array('type'=>$type,'name'=>$name));
				$query->prepare();
				$query->stmt->execute();
				return $this->success();
			}
		}
		$noteObject->set('name', $name);
		$noteObject->set('text', $text);
		if (!$noteObject->save()) return $this->failure('Error of saving the note!');
		return $this->success();
	}

}

return 'elementNoteSaveProcessor';
