<?php
$user = Auth::getinstance()->getIdentity();
if ($user->role == 'root') {

}

$settings = array();

$settings['text'] = array(
    'levels' => 2,
    'title' => 'Some title',
    'categories' => array(
	'actions' => array('add' => true, 'edit' => true, 'delete' => true),
	'max' => 3,
	'sorting' => true,
	'fields' => array('name')
    ),
    'items' => array(
	'actions' => array('add' => true, 'edit' => true, 'delete' => true),
	'fields' => array('name', 'note1'=>'Note', 'select1' => 'Some label', 'text1' => 'Text'),
	'editor' => array('text1'),
	'editorModules' => array('images', 'attachments'),
	'select1' => array(
	    'source' => array(
		'multiple' => true,
		'before' => array('' => ' '),
		'query' => 'SELECT * FROM `directoryItems` WHERE `isCategory` = 0 AND `section` = "category"',
		'value' => 'id',
		'name' => 'name',
		'message' => 'Error'
	    )
	),
	'sorting' => true,
	'title' => 'name',
	'images' => array(
	    'actions' => array('add' => true, 'edit' => true, 'delete' => true),
	    'fields' => array('name' => 'Image title', 'checkbox1' => 'Checkbox', 'text1'),
//	    'fields' => array(),
	    'editor' => array('text1'),
	    'sizes' => array('text', 'content'),
	    'sorting' => true,
	    'max' => 10,
	    'extension' => array('jpg', 'jpeg', 'png', 'gif'),
//	    'maxSize' => 571520,
	    'required' => array('name'),
	    'keepOriginal' => true // true|false
	),
	'attachments' => array(
	    'actions' => array('add' => true, 'edit' => true, 'delete' => true),
	    'fields' => array('name' => 'Attach title', 'text1'),
	    'sorting' => true,
	    'editor' => array('text1'),
	    'max' => 2,
	    'maxSize' => 10000000,
	    'extension' => array('doc', 'xls', 'pdf'),
	    'required' => array('name')
	),
	'videos' => array(
	    'actions' => array('add' => true, 'edit' => true, 'delete' => true),
	    'fields' => array('name' => 'Video title', 'text1'),
	    'sorting' => true,
	    'preview' => 'thumbs',
	    'editor' => array('text1')
	),
	'audios' => array(
	    'actions' => array('add' => true, 'edit' => true, 'delete' => true),
	    'fields' => array('name' => 'Audio title'),
	    'extension' => array('mp3'),
	    'max' => 2,
	    'maxSize' => 20971520,
	    'sorting' => true
	),
    )
);

$settings['text']['levelRules'][1]['items']['fields'] = array('date');

$settings['shop'] = array(
    'levels' => 0,
    'title' => 'Shop online',
    'items' => array(
	'actions' => array('add' => true, 'edit' => true, 'delete' => true),
	'fields' => array('name', 'select1' => 'Some label', 'text1' => 'Text'),
	'select1' => array(
	    'source' => array(
		'multiple' => true,
		'before' => array('' => ' '),
		'query' => 'SELECT * FROM `directoryItems` WHERE `isCategory` = 0 AND `section` = "category"',
		'value' => 'id',
		'name' => 'name',
		'message' => 'Error'
	    )
	),
	'sorting' => true,
	'images' => array(
	    'actions' => array('add' => true, 'edit' => true, 'delete' => true),
	    'fields' => array('name' => 'Image title'),
	    'sizes' => array('text'),
	    'sorting' => true,
	    'max' => 1,
	    'required' => array('name')
	)
    )
);


$settings['glossary']['levels'] = 0;
$settings['glossary']['items']['required'] = array('name', 'note1');
$settings['glossary']['items']['fields'] = array('name' => 'Title (En)', 'note1'=>'Title', 'select1' => 'Category', 'text1' => 'Description', 'text2' => 'Text');
$settings['glossary']['items']['actions'] = array('add' => true, 'edit' => true, 'delete' => true);
$settings['glossary']['items']['editor'] = array('text1', 'text2');

$settings['glossary']['items']['select1']['source'] = array (
	'before' => array('' => ' '),
	'message' => 'Add Categories at first',
	'query' => 'SELECT * FROM `directoryItems` WHERE `isCategory` = 0 AND `section` = "category"'
);

$settings['category']['levels'] = 4;
$settings['category']['title'] = 'Glossarry';
$settings['category']['categories']['actions'] = array('add' => true, 'edit' => true, 'delete' => true);
$settings['category']['categories']['fields'] = array('name' => 'Title', 'note1'=>'Title');
$settings['category']['categories']['sorting'] = true;

$settings['category']['categories']['images']['fields'] = array('name' => 'Image title', 'checkbox1' => 'Checkbox');
$settings['category']['categories']['images']['actions'] = array('add' => true, 'edit' => true, 'delete' => true);
$settings['category']['categories']['images']['sorting'] = true;

$settings['category']['categories']['attachments']['actions'] = array('add' => true, 'edit' => true, 'delete' => false);
$settings['category']['categories']['attachments']['fields'] = array('name' => 'Image title', 'checkbox1' => 'Checkbox');
$settings['category']['categories']['attachments']['sorting'] = true;

$settings['category']['items']['images']['fields'] = array('name' => 'Image title', 'checkbox1' => 'Checkbox');
$settings['category']['items']['images']['actions'] = array('add' => true, 'edit' => true, 'delete' => true);
$settings['category']['items']['images']['sorting'] = true;

$settings['category']['items']['actions'] = array('add' => true, 'edit' => true, 'delete' => true);
$settings['category']['items']['fields'] = array('name' => 'Title', 'note1'=>'Title', 'note2' => 'Title', 'text1' => 'Description');
$settings['category']['items']['editor'] = array('text1', 'text2');
$settings['category']['items']['sorting'] = true;


$settings['category']['levelRules'][0]['categories']['actions'] = array('add' => true, 'edit' => false, 'delete' => true);
$settings['category']['levelRules'][1]['categories']['actions'] = array('add' => true, 'edit' => true, 'delete' => false);
$settings['category']['levelRules'][2]['categories']['actions'] = array('add' => true, 'edit' => true, 'delete' => true);
$settings['category']['levelRules'][2]['items']['actions'] = array('add' => true, 'edit' => true, 'delete' => false);
$settings['category']['levelRules'][1]['items']['actions'] = array('add' => false, 'edit' => true, 'delete' => true);
$settings['category']['levelRules'][1]['categories']['images']['actions'] = array('add' => true, 'edit' => true, 'delete' => false);
$settings['category']['levelRules'][1]['categories']['sorting'] = true;
//$settings['category']['levelRules'][1]['categories']['title'] = 'note1';
$settings['category']['levelRules'][1]['categories']['images']['sorting'] = true;
$settings['category']['levelRules'][1]['items']['images']['sorting'] = true;
$settings['category']['levelRules'][0]['items']['images']['sorting'] = false;



if ($user->role != 'root') {
	$settings['text']['items']['actions'] = array('add' => false, 'edit' => true, 'delete' => false);
}

// custom sizes
$sizes = array(
	'thumbs' => array(
		'size'    => array(
			'width'  => '140',
			'height' => '140',
		),
		'options' => array(
			'method' => 'crop'
		),
	),
	'text' => array(
		'size'    => array(
			'width'  => '460',
		),
		'options' => array(
			'method' => 'inscribe',
			'enlarge' => false,
		),
	),
	'preview' => array(
		'size'    => array(
			'width'  => '500',
		),
		'options' => array(
			'method' => 'scale',
			'output'
		),
	)
);




/* SAMPLE 
============================================================================= */
//$settings['text'] = array(
//    'levels' => 1,
//    'title' => 'Some title',
//    'categories' => array(
//	'actions' => array('add' => true, 'edit' => true, 'delete' => false),
//	'max' => 3,
//	'fields' => array('name')
//    ),
//    'items' => array(
//	'actions' => array('add' => true, 'edit' => true, 'delete' => false),
//	'fields' => array('name' => 'Title', 'date', 'note1'=>'Note', 'select1' => 'Some label', 'text1' => 'Text'),
//	'select1' => array(
//	    'source' => array(
//		'multiple' => true,
//		'before' => array('' => ' '),
//		'query' => 'SELECT * FROM `directoryItems` WHERE `isCategory` = 0 AND `section` = "category"',
//		'value' => 'id',
//		'name' => 'name',
//		'message' => 'Error'
//	    )
//	),
//	'title' => 'name',
//	'editor' => array('text1'),
//	'editorModules' => array('images', 'attachments'),
//	'images' => array(
//	    'actions' => array('add' => true, 'edit' => true, 'delete' => true),
//	    'fields' => array('name' => 'Image title', 'checkbox1' => 'Checkbox'),
//	    'sizes' => array('text'),
//	    'sorting' => true,
//	    'max' => 10,
//	    'required' => array('name'),
//	    'keepOriginal' => false // true|false
//	),
//	'attachments' => array(
//	    'actions' => array('add' => true, 'edit' => true, 'delete' => true),
//	    'fields' => array('name' => 'Attach title', 'text1'),
//	    'sorting' => true,
//	    'editor' => array('name'),
//	    'max' => 10,
//	    'extension' => array('mp3', 'doc', 'xls'),
//	    'required' => array('name')
//	),
//	'videos' => array(
//	    'actions' => array('add' => true, 'edit' => true, 'delete' => false),
//	    'fields' => array('name' => 'Video title'),
//	    'sorting' => true,
//	    'preview' => 'thumbs'
//	),
//	'audios' => array(
//	    'actions' => array('add' => true, 'edit' => true, 'delete' => false),
//	    'fields' => array('name' => 'Audio title'),
//	    'extension' => array('mp3'),
//	    'sorting' => true
//	),
//    )
//);
//
//$sizes = array(
//	'thumbs' => array(
//		'size'    => array(
//			'width'  => '140',
//			'height' => '140',
//		),
//		'options' => array(
//			'method' => 'crop',
//			'outputExtension' => 'jpg'
//		),
//	),
//	'text' => array(
//		'size'    => array(
//			'width'  => '460',
//		        'height' => '340'
//		),
//		'options' => array(
//			'method' => 'inscribe',
//			'enlarge' => false,
//		),
//	),
//	'preview' => array(
//		'size'    => array(
//			'width'  => '500',
//		),
//		'options' => array(
//			'method' => 'scale',
//		),
//	)
//);