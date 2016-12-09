<?php

/**
 * OCAX -- Citizen driven Observatory software
 * Copyright (C) 2014 OCAX Contributors. See AUTHORS.

 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.

 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.

 * You should have received a copy of the GNU Affero General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

?>
<?php $this->beginContent('//layouts/main'); ?>

<div class="span-19">
	<div id="content" style="margin:-15px -10px 0 -10px;">
		<?php
		echo $content;
		if($this->inlineHelp){
			$this->widget('InlineHelp');
		}
		if($this->viewLog){
			$this->widget('ViewLog');
		}		
		?>
	</div><!-- content -->
</div>

<div class="span-5 last">
	<div id="sidebar">
	<?php
		$mypage = array( array('label'=>__('My page'), 'url'=>array('/user/panel')) );
		$this->menu = array_merge( $this->menu, $mypage );

		$title=__('Options');
		$myPage = CHtml::link(
				'<i style="float:right;font-size:23px;color:#f5f1ed;" class="icon-home"></i>', 
				'#',
				array('onClick'=>'js:window.location.href = "'.$this->createUrl('/user/panel').'";','title'=>__('My page'))
			);
		$title=$title.$myPage;		
		if($this->viewLog){
			$params = explode('|',$this->viewLog);
			$param = '"'.$params[0].'"';
			if(isset($params[1]))
				$param = $param.','.$params[1];
			$log = CHtml::link(
				'<i style="float:right;font-size:23px;color:#f5f1ed;" class="icon-book"></i>', 
				'#',
				array('onClick'=>'js:viewLog('.$param.');','title'=>__('Log'))
			);			
			$title=$title.$log;
		}
		if($this->inlineHelp){
			$help = CHtml::link(
				'<i style="float:right;font-size:23px;color:#f5f1ed;" class="icon-help-circled"></i>', 
				'#',
				array('onClick'=>'js:showHelp("'.getInlineHelpURL($this->inlineHelp).'");','title'=>__('Help'))
			);
			$title=$title.$help;
		}		
		//http://www.yiiframework.com/doc/blog/1.1/en/portlet.menu
		$this->beginWidget('zii.widgets.CPortlet', array(
			'title'=>$title,
		));
		$this->widget('zii.widgets.CMenu', array(
			'items'=>$this->menu,
			'htmlOptions'=>array('class'=>'operations'),
			'encodeLabel'=>false,
		));
		if($this->extraText)
			echo '<div id="extraText">'.$this->extraText.'</div>';		
		$this->endWidget();
	?>
	</div><!-- sidebar -->
</div>
<?php $this->endContent(); ?>

