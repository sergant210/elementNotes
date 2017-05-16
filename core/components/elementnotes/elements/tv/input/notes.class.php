<?php

if(!class_exists('NotesInputRender'))
{
	class NotesInputRender extends modTemplateVarInputRender
	{
		public function getTemplate(){return $this->modx->elementnotes->config['corePath'].'elements/tv/templates/notes.tpl';}
		public function process($value,array $params = array())
		{
			$this->modx->controller->addLastJavascript($this->modx->elementnotes->config['jsUrl'].'mgr/widgets/notes.tv.js');
			$this->setPlaceholder('auth',$_SESSION["modx.{$this->modx->context->get('key')}.user.token"]);
		}
	}
}
return 'NotesInputRender';
