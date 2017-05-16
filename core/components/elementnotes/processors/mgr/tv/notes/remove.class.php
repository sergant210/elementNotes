<?php

class NotesRemove extends modProcessor
{
	public function initialize()
	{
		$this->cmp=&$this->modx->filials;
		$this->properties = $this->getProperties();
		if(isset($this->properties['localData']))$this->properties['localData']=$this->modx->fromJSON($this->properties['localData']);
		return parent::initialize($this);
	}
	
	public function process()
	{
		$localmode = !$this->properties['resource'];

		if(!$localmode)
		{
			$notes = $this->modx->getCollection('elementNote',array(
				'id'=>$this->properties['resource'].'_'.$this->properties['tv'],
				'type'=>'resource',
				'createdon:IN'=>$this->properties['ids']
			),false);
			foreach($notes as $note){$note->remove();}
		}
		else
		{
			foreach($this->properties['ids'] as $id){unset($this->properties['localData'][$id]);}
		}
		
		$result = array('success'=>true);
		if($localmode)$result['object']=$this->properties['localData'];
		return $result;
	}
}

return 'NotesRemove';
