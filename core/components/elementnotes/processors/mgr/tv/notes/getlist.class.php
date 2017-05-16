<?php

class NotesGetList extends modObjectGetListProcessor
{
	public $cmp=null;
	public $properties=array();
	public $classKey = 'elementNote';
	public $languageTopics = array('elementnotes:default');
	public $defaultSortField = 'createdon';
	public $defaultSortDirection = 'DESC';
	public $objectType = 'elementNote';
	
	public function initialize()
	{
		$this->cmp=&$this->modx->elementnotes;
		$this->properties = $this->getProperties();
		return parent::initialize($this);
	}
	
	public function process()
	{
		$localmode = empty($this->properties['resource']);
		if(!$localmode)return parent::process();
		$data = json_decode($this->properties['localData'],true);
		uasort($data,function($a,$b){
			if($a['createdon']==$b['createdon'])return 0;
			return ($a['createdon'] < $b['createdon'])?1:-1;
		});
		return $this->outputArray(array_values($data));
	}
	
	public function prepareQueryBeforeCount(xPDOQuery $c)
	{
		$c->where(array('type'=>'resource'));
		$c->where(array('id'=>$this->properties['resource'].'_'.$this->properties['tv']));
		return parent::prepareQueryBeforeCount($c);
	}
	
	public function prepareRow(xPDOObject $object) {
		$objectArray = parent::prepareRow($object);
		$objectArray['id'] = $objectArray['createdon'];
		return $objectArray;
	}
}

return 'NotesGetList';
