<?php
switch ($modx->event->name) {
	// add the "Note" tab
	case 'OnTempFormPrerender':
		$enTabs = 'modx-template-tabs';
		$name = $template->get('templatename');
	case 'OnChunkFormPrerender':
		if (!isset($enTabs)) {
			$enTabs = 'modx-chunk-tabs';
			$name = $chunk->get('name');
		}
	case 'OnSnipFormPrerender':
		if (!isset($enTabs)) {
			$enTabs = 'modx-snippet-tabs';
			$name = $snippet->get('name');
		}
	case 'OnPluginFormPrerender':
		if (!isset($enTabs)) {
			$enTabs = 'modx-plugin-tabs';
			$name = $plugin->get('name');
		}
		if ($mode == modSystemEvent::MODE_UPD) {
			$modx->controller->addLexiconTopic('elementnotes:default');
			$modx->controller->addJavascript($modx->getOption('assets_url') . 'components/elementnotes/js/mgr/elementnotes.js');
			$modx->controller->addLastJavascript($modx->getOption('assets_url') . 'components/elementnotes/js/mgr/widgets/elementnotes.panel.js');
			$_html = '<script>
				var elemNotes = {},
					elemName = "'.$name.'";
				elemNotes.config = {"connector_url" : "'.$modx->getOption('assets_url').'components/elementnotes/connector.php"};
				Ext.onReady(function() {
					MODx.addTab("'.$enTabs.'",{
						id: "elementnotes-tab",
						title: _("Notes"),
						items: [{
	                            xtype: "elementnotes-page",
	                            width: "100%"
	                        }]
			        });
				});
</script>';
			$modx->controller->addHtml($_html);
		}
		break;
	// Remove the element note
	case 'OnChunkRemove':
		$type = 'chunk';
		$id = $chunk->id;
	case 'OnPluginRemove':
		if (!isset($type)) {
			$type = 'plugin';
			$id = $plugin->id;
		}
	case 'OnSnippetRemove':
		if (!isset($type)) {
			$type = 'snippet';
			$id = $snippet->id;
		}
	case 'OnTemplateRemove':
		if (!isset($type)) {
			$type = 'template';
			$id = $template->id;
		}

		/** @var elementNotes $elementNotes */
		$elementNotes = $modx->getService('elementnotes', 'elementNotes', $modx->getOption('core_path') . 'components/elementnotes/model/elementnotes/');
		if (isset($type) && isset($id))	$elementNotes->removeNote($type,$id);
		break;
}