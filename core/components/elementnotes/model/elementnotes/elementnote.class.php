<?php
class elementNote extends xPDOObject {
    public function save($cacheFlag)
	{
		if($this->isNew()&&empty($this->createdon))$this->set('createdon',time());
		return parent::save($cacheFlag);
	}
}
