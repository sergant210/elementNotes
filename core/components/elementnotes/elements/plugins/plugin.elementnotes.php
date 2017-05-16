<?php
$elementNotes = $modx->getService('elementnotes', 'elementNotes', $modx->getOption('core_path') . 'components/elementnotes/model/elementnotes/');
$prerenderData=array(
	'OnTempFormPrerender'=>['type'=>'template','id'=>function($scriptProperties){return $scriptProperties['id'];}]
	,'OnChunkFormPrerender'=>['type'=>'chunk','id'=>function($scriptProperties){return $scriptProperties['id'];}]
	,'OnSnipFormPrerender'=>['type'=>'snippet','id'=>function($scriptProperties){return $scriptProperties['id'];}]
	,'OnPluginFormPrerender'=>['type'=>'plugin','id'=>function($scriptProperties){return $scriptProperties['id'];}]
	,'OnFileEditFormPrerender'=>['type'=>'file','id'=>function($scriptProperties){return $scriptProperties['fa']['path'];}]
);
$removeData=array(
	'OnTemplateRemove'=>['type'=>'template','criteria'=>function($scriptProperties){return array('id'=>$scriptProperties['template']->id);}]
	,'OnChunkRemove'=>['type'=>'chunk','criteria'=>function($scriptProperties){return array('id'=>$scriptProperties['chunk']->id);}]
	,'OnSnippetRemove'=>['type'=>'snippet','criteria'=>function($scriptProperties){return array('id'=>$scriptProperties['snippet']->id);}]
	,'OnPluginRemove'=>['type'=>'plugin','criteria'=>function($scriptProperties){return array('id'=>$scriptProperties['plugin']->id);}]
	,'OnFileManagerFileRemove'=>['type'=>'file','criteria'=>function($scriptProperties){return array('id'=>$scriptProperties['path']);}]
	,'OnFileManagerDirRemove'=>['type'=>'file','criteria'=>function($scriptProperties){return array('id:LIKE'=>$scriptProperties['directory'].'%');}]
);
switch ($modx->event->name){
	///////////////////////////////////////////////////////////////////////////////////////////
	/////////////////////////////////////////// add the "Note" tab
	///////////////////////////////////////////////////////////////////////////////////////////
	case 'OnTempFormPrerender':
	case 'OnChunkFormPrerender':
	case 'OnSnipFormPrerender':
	case 'OnPluginFormPrerender':
	case 'OnFileEditFormPrerender':
		if ($mode == modSystemEvent::MODE_UPD) {
			$modx->controller->addLexiconTopic('elementnotes:default');
			$modx->controller->addJavascript($elementNotes->config['jsUrl'].'mgr/elementnotes.js');
			$modx->controller->addLastJavascript($elementNotes->config['jsUrl'].'mgr/widgets/elementnotes.panel.js');
			$_html = '<script>
			elementNotes.config = ' . $modx->toJSON($elementNotes->config) . ';
			elementNotes.getPageStructure=MODx.getPageStructure;
			MODx.getPageStructure=function(v,c) {
				v.push({
					id: "elementnotes-tab",
					title: _("Notes"),
					items: [{
						xtype: "elementnotes-panel"
						,width: "100%"
						,note:{
							type: "'.$prerenderData[$modx->event->name]['type'].'"
							,id: "'.$prerenderData[$modx->event->name]['id']($scriptProperties).'"
						}
					}]
				});
				return elementNotes.getPageStructure(v,c);
			};
			</script>';
			$modx->controller->addHtml($_html);
		}
		break;
	///////////////////////////////////////////////////////////////////////////////////////////
	/////////////////////////////////////////// remove Note
	///////////////////////////////////////////////////////////////////////////////////////////
	case 'OnChunkRemove':
	case 'OnPluginRemove':
	case 'OnSnippetRemove':
	case 'OnTemplateRemove':
	case 'OnFileManagerFileRemove':
	case 'OnFileManagerDirRemove':
		$notes = $modx->getCollection('elementNote',array_merge(array('type'=>$removeData[$modx->event->name]['type']),$removeData[$modx->event->name]['criteria']($scriptProperties)));
		foreach($notes as $note)$note->remove();
		break;
	///////////////////////////////////////////////////////////////////////////////////////////
	/////////////////////////////////////////// file events
	///////////////////////////////////////////////////////////////////////////////////////////
	case 'OnFileManagerFileRename':
	case 'OnFileManagerDirRename':
		//Need oldpath to work, create pullrequest for modx or set oldpath youself in model/modx/sources/modfilemediasource.class.php
		$oldPath = $scriptProperties['oldPath'];
		$newPath = $scriptProperties['path']?:$scriptProperties['directory'];
		if(!$oldPath)break;
		$notes = $modx->getCollection('elementNote',array('type'=>'file','id:LIKE'=>$oldPath.'%'));
		foreach($notes as $note)
		{
			//create new note, because id is PRIMARY and we dont change it
			$newnote = $modx->newObject('elementNote');
			$newnote->set('type','file');
			$newnote->set('id',str_replace($oldPath,$newPath,$note->id));
			$newnote->set('createdon',$note->createdon);
			$newnote->set('text',$note->text);
			$newnote->save();
			$note->remove();
		}
		break;
	case 'OnFileManagerMoveObject':
		$newPath = rtrim($to,'/').'/'.basename($from);
		if(substr($from,-1)=='/')$newPath.='/';
		$notes = $modx->getCollection('elementNote',array('type'=>'file','id:LIKE'=>$from.'%'));
		foreach($notes as $note)
		{
			//create new note, because id is PRIMARY and we dont change it
			$newnote = $modx->newObject('elementNote');
			$newnote->set('type','file');
			$newnote->set('id',str_replace($from,$newPath,$note->id));
			$newnote->set('createdon',$note->createdon);
			$newnote->set('text',$note->text);
			$newnote->save();
			$note->remove();
		}
		break;
	///////////////////////////////////////////////////////////////////////////////////////////
	/////////////////////////////////////////// Notes-TV
	///////////////////////////////////////////////////////////////////////////////////////////
	case 'OnTVInputRenderList':
	{
		$modx->getService('elementnotes', 'elementNotes', $modx->getOption('core_path') . 'components/elementnotes/model/elementnotes/');
		$modx->event->output($modx->elementnotes->config['corePath'].'elements/tv/input/');
		break;
	}
	case 'OnTVInputPropertiesList':
	{
		$modx->getService('elementnotes', 'elementNotes', $modx->getOption('core_path') . 'components/elementnotes/model/elementnotes/');
		$modx->event->output($modx->elementnotes->config['corePath'].'elements/tv/inputoptions/');
		break;
	}
	case 'OnTVFormPrerender':
	case 'OnDocFormPrerender':
	{
		$modx->getService('elementnotes', 'elementNotes', $modx->getOption('core_path') . 'components/elementnotes/model/elementnotes/');
		$modx->controller->addLexiconTopic('elementnotes:default');
		$modx->controller->addJavascript($modx->elementnotes->config['jsUrl'] . 'mgr/elementnotes.js');
		$modx->controller->addHtml('<script type="text/javascript">
			elementNotes.config = ' . $modx->toJSON($modx->elementnotes->config) . ';
		</script>');
		break;
	}
	case 'OnTemplateVarRemove':
	{
		$modx->getService('elementnotes', 'elementNotes', $modx->getOption('core_path') . 'components/elementnotes/model/elementnotes/');
		switch($templateVar->type)
		{
			case 'notes':{$modx->removeCollection('elementNote', array('id:LIKE'=>'%_'.$templateVar->id,'type'=>'resource'));break;}
		}
		break;
	}
	case 'OnEmptyTrash':
	{
		$ids_where=array();
		foreach($ids as $i=>$id)
		{
			$ids_where[]=array(($i>0?'OR:id:LIKE':'id:LIKE')=>$id.'_%');
		}
		$modx->removeCollection('elementNote', array($ids_where,'type'=>'resource'));
		break;
	}
	case 'OnDocFormSave':
	{
		if($mode == modSystemEvent::MODE_NEW)
		{
			$modx->getService('elementnotes', 'elementNotes', $modx->getOption('core_path') . 'components/elementnotes/model/elementnotes/');
			$n_query = $modx->newQuery('modTemplateVarTemplate');
			$n_query->leftJoin('modTemplateVar','TemplateVar');
			$n_query->where(array('TemplateVar.type'=>'notes','modTemplateVarTemplate.templateid'=>$resource->template));
			$n_query->select(array('modTemplateVarTemplate'=>"GROUP_CONCAT(tmplvarid SEPARATOR ',')"));
			$n_query->prepare();
			$n_query->stmt->execute();
			$n_tvs = explode(',',$modx->getValue($n_query->stmt));
			foreach($n_tvs as $n_tv_id)
			{
				$n_data = json_decode($processor->getProperty('tv'.$n_tv_id.'_local'),true);
				if(!$n_data||count($n_data)===0)continue;
				foreach($n_data as $ar_note)
				{
					$note=$modx->newObject('elementNote');
					$note->set('id',$resource->id.'_'.$n_tv_id);
					$note->set('type','resource');
					$note->set('text',$ar_note['text']);
					$note->set('createdon',$ar_note['createdon']);
					$note->save(false);
				}
			}
		}
		break;
	}
}
