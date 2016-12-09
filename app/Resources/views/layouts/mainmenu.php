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



	<!-- <div id="mainmenu"> -->
	<div id="mainMbMenu">
		<?php
			$items=array(
				array('label'=>__('Budgets'), 'url'=>array('/budget'),'active'=> (strcasecmp(Yii::app()->controller->id, 'budget') === 0)  ? true : false),
				array('label'=>__('Enquiries'), 'url'=>array('/enquiry'),'active'=> (strcasecmp(Yii::app()->controller->id, 'enquiry') === 0)  ? true : false),
			);
			if(Config::model()->findByPk('schemaVersion')->value > 0){
				$criteria=new CDbCriteria;
				$criteria->condition = 'weight = 0 AND published = 1';
				$criteria->order = 'block DESC';
				$cms_pages=SitePage::model()->findAll($criteria);
				foreach($cms_pages as $page){
					$page_content = $page->getContentForModel(Yii::app()->language);
					
					//find sub menu items
					$criteria=new CDbCriteria;
					$criteria->condition = 'block = :block AND weight != 0 AND published = 1 AND weight IS NOT NULL';
					$criteria->params[':block'] = $page->block;
					$criteria->order = 'weight ASC';				
					$subpages = $page->findAll($criteria);
					if($subpages){
						$onclick = 'js:toggleSubMenu(this);return false';
						$url = '';
					}else{
						$onclick = '';
						$url = array('/p/'.$page_content->pageURL);
					}	
					$item = array(	'label'=>$page_content->pageTitle,
									'linkOptions'=>array(	'class'=>'',
															'onClick'=>$onclick,
													),
									'url'=>$url,
									'active'=> ($page->isMenuItemHighlighted()) ? true : false,
								);
	
					if($subpages){
						$subitems=array();
						foreach($subpages as $subpage){
							$subpage_content = $subpage->getContentForModel(Yii::app()->language);
							$subitems[] = array('label'=>$subpage_content->pageTitle,
												'url'=>array('/p/'.$subpage_content->pageURL),
											);
						}
						$item['items'] = $subitems;
					}
					array_splice( $items, 0, 0, array($item) );
				}
			}
			/*
			if(!Yii::app()->user->isGuest){
				$item = array(	'label'=>__('My page'),
								'url'=>array('/user/panel'),
						);
				array_splice( $items, 0, 0, array($item) );
			}
			*/
			$this->widget('application.extensions.mbmenu.MbMenu',array(
				'items'=>$items,
			));
		?>
	</div>
	<!-- mainmenu -->
	
<script>
function toggleSubMenu(item) {
	if($(item).parent().hasClass("parent")){
		if($(item).parent().hasClass("over")){
			$(item).parent().removeClass("over");
		}else
			$(item).parent().addClass("over");
	}
}

$('li').mouseleave(function() {
	$(this).removeClass("over");
});
</script>
