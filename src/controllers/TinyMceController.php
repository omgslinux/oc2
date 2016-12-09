<?php

//http://www.yiiframework.com/extension/newtinymce/

Yii::import('ext.tinymce.*');
 
class TinyMceController extends CController
{
     public function actions()
     {
		return array(
			'compressor' => array(
				'class' => 'TinyMceCompressorAction',
				'settings' => array(
					'compress' => true,
					'disk_cache' => true,
				),
			),
			'spellchecker' => array(
				'class' => 'TinyMceSpellcheckerAction',
			),
		);
	}
}
