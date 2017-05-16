<?php
$xpdo_meta_map['elementNote']= array (
  'package' => 'elementnotes',
  'version' => '1.1',
  'table' => 'element_notes',
  'extends' => 'xPDOObject',
  'fields' => 
  array (
    'id' => NULL,
    'type' => NULL,
    'text' => NULL,
    'createdon' => NULL,
  ),
  'fieldMeta' => 
  array (
    'id' => 
    array (
      'dbtype' => 'mediumtext',
      'phptype' => 'string',
      'null' => false,
      'index' => 'pk',
    ),
    'type' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '20',
      'phptype' => 'string',
      'null' => false,
      'index' => 'pk',
    ),
    'text' => 
    array (
      'dbtype' => 'mediumtext',
      'phptype' => 'string',
      'null' => false,
    ),
    'createdon' => 
    array (
      'dbtype' => 'int',
      'precision' => '20',
      'phptype' => 'integer',
      'attributes' => 'unsigned',
      'null' => false,
      'index' => 'pk',
    ),
  ),
  'indexes' => 
  array (
    'PRIMARY' => 
    array (
      'alias' => 'PRIMARY',
      'primary' => true,
      'unique' => true,
      'type' => 'BTREE',
      'columns' => 
      array (
        'id' => 
        array (
          'length' => '255',
          'collation' => 'A',
          'null' => false,
        ),
        'type' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
        'createdon' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
  ),
);
