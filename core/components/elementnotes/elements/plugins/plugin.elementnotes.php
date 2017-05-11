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
}
