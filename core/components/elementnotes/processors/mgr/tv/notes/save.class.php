<?php

class NoteSave extends modProcessor
{
	public function initialize()
	{
		$this->cmp=&$this->modx->filials;
		$this->properties = $this->getProperties();
		if(isset($this->properties['data']))$this->properties=array_merge($this->properties,$this->modx->fromJSON($this->properties['data']));
		if(isset($this->properties['localData']))$this->properties['localData']=$this->modx->fromJSON($this->properties['localData']);
		return parent::initialize($this);
	}
	
	public function process()
	{
		$localmode = !$this->properties['resource'];
		$isnew = empty($this->properties['id']);
		if($isnew||$localmode)
		{
			$note = $this->modx->newObject('elementNote');
			$note->set('id',$this->properties['resource'].'_'.$this->properties['tv']);
			$note->set('type','resource');
		}
		else
		{
			$note = $this->modx->getObject('elementNote',array(
				'id'=>$this->properties['resource'].'_'.$this->properties['tv'],
				'type'=>'resource',
				'createdon'=>intval($this->properties['createdon'])
			),false);
		}
		$note->set('text',$this->properties['text']);
		
		if(!$localmode){$note->save(false);return array('success'=>true);}
		else
		{
			if($isnew)$this->properties['createdon']=time();
			$note->set('createdon',$this->properties['createdon']);
			$this->properties['localData'][$note->get('createdon')]=$note->toArray();
			$this->properties['localData'][$note->get('createdon')]['id']=$note->get('createdon');
			return array('success'=>true,'object'=>$this->properties['localData']);
		}
	}
}

return 'NoteSave';
