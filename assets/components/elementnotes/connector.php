<?php
/** @noinspection PhpIncludeInspection */
require_once dirname(dirname(dirname(dirname(__FILE__)))) . '/config.core.php';
/** @noinspection PhpIncludeInspection */
require_once MODX_CORE_PATH . 'config/' . MODX_CONFIG_KEY . '.inc.php';
/** @noinspection PhpIncludeInspection */
require_once MODX_CONNECTORS_PATH . 'index.php';
/** @var elementNotes $elementNotes */
$elementNotes = $modx->getService('elementnotes', 'elementNotes', $modx->getOption('core_path') . 'components/elementnotes/model/elementnotes/');
//$modx->lexicon->load('elementnotes:default');

// handle request
$corePath = $modx->getOption('elementnotes_core_path', null, $modx->getOption('core_path') . 'components/elementnotes/');
$path = $modx->getOption('processorsPath', $elementNotes->config, $corePath . 'processors/');

$modx->request->handleRequest(array(
	'processors_path' => $path,
	'location' => '',
));