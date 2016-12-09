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

/* @var $this UserController */
/* @var $model User */
/*
 * @property integer $is_team_member
 * @property integer $is_editor
 * @property integer $is_manager
 * @property integer $is_admin
*/

$column=0;
function changeColumn()
{
	global $column;
	if($column==0){
		echo '<div class="clear"></div>';
		echo '<div class="panel_left" style="width:49%">';
		$column=1;
	}else{
		echo '<div class="panel_right" style="width:49%">';
		$column=0;
	}
}
$privilegedUser = Yii::app()->user->isPrivileged();
if($privilegedUser)
	$this->widget('InlineHelp');
?>

<style>
	.outer{ position:relative; width:100%; padding: 0px; float: left;}
	.clear{clear:both;}
</style>

<?php echo $this->renderPartial('//includes/socialWidgetsScript', array());?>

<?php if($privilegedUser){ ?>
<script>
function toggleMoreOptions(){
	if ($("#moreOptions").is(":visible")){
		$("#moreOptionsToggle").html("<i class='icon-plus-circled'></i>");
		$("#moreOptions").slideUp();
	}else{
		$("#moreOptionsToggle").html("<i class='icon-minus-circled'></i>");
		$("#moreOptions").slideDown();
	}
}
function toggleLegend(){
	if ($("#legend").is(":visible")){
		$("#legend").slideUp();
	}else{
		$("#legend").slideDown();
	}
}
$(function() {
	$("#ocmMemberPanel").mouseleave(function() {
		$("#legend").fadeOut();
	});
});
</script>
<?php } ?>

<?php if($privilegedUser && $model->is_active){ ?>
<div style="position:relative;">
	<div	id="moreOptionsToggle"
			onCLick="js:toggleMoreOptions();return false;">
			<?php echo 	'<i class="icon-plus-circled"></i>';?>
	</div>
</div>
<div style="position:relative; right:40px" >
	<div class="teamMenu" onCLick="js:showHelp('<?php echo getInlineHelpURL(":manual:user:panel");?>');return false;">
		<i class="icon-help-circled"></i>
	</div>
</div>
<?php } ?>

<?php if(!$model->is_active){
	echo '<div class="sub_title">'.__('Welcome').'</div>';
	$this->renderPartial('_notActiveInfo', array('model'=>$model));
	echo '<div class="horizontalRule"></div>';
}?>



<div id="moreOptions" class="outer" style="<?php echo ($privilegedUser && $model->is_active) ? 'display:none' : ''; ?>"> <!-- outer starts -->

<div class="panel_left">
	<?php
		echo '<span style="cursor:pointer" onclick="location.href=\''.$this->createUrl('enquiry/create/').'\'">';
		include(svgDir().'newenquiry.svg');
		echo '</span>';
	?>
	<div class="clear"></div>
	<div class="sub_title"><?php echo CHtml::link(__('New enquiry'),array('enquiry/create/'));?></div>
	<p>
	<?php
		$str = __('ENQUIRY_NEW_MSG');
		echo str_replace('%s', CHtml::link(__('Budgets'),array('/budget')), $str);
	?>
	</p>
</div>
<div class="panel_right">
	<?php
		echo '<div style="cursor:pointer;" onclick="location.href=\''.$this->createUrl('user/update/').'\'">';
		include(svgDir().'userprofile.svg');
		echo '</div>';
	?>
	<div class="clear"></div>
	<div class="sub_title" style="margin-top:8px;"><?php echo CHtml::link(__('My user information'),array('user/update/'));?></div>
	<p>
		<?php echo __('Change your profile');?><br />
		<?php echo __('Configure your email');?><br />
		<?php echo __('Change your password');?>
	</p>
</div>

<div class="horizontalRule" style="float:right;padding-top:10px;"></div>
</div> <!-- outer ends -->

<div class="clear"></div>

<?php

$panel_separator_added=0;
function addPanelSeparator(){	// OCM Member Panel
	global $panel_separator_added;
	if(!$panel_separator_added){
		echo '<div id="ocmMemberPanel" style="">';	// OCM Member Panel starts
		
		// OCM Member Panel menu and legend
		echo '<div id="legendToggle" style="float:left;position:relative;">';	// left starts
		echo '<span style="cursor:pointer;" onclick="$(\'#legend\').toggle();">';
		include(svgDir().'controlpanel.svg');
		echo '</span>';
		// legend
		?>
			<div id="legend">
				<span><i class="icon-attention green"></i><?php echo __('For your information');?></span>
				<span><i class="icon-attention amber"></i><?php echo __('You have a task');?></span>
				<span><i class="icon-attention red"></i><?php echo __('OCAx needs attention');?></span>
				<br />
				<span><i class="icon-circle green"></i> <?php echo __('Complete');?></span>
				<span><i class="icon-dot-circled green"></i> <?php echo __('Partial');?></span>
				<span><i class="icon-circle-empty green"></i> <?php echo __('Empty');?></span>
				<span><i class="icon-circle red"></i> <?php echo __('Missing');?></span>
			</div>
		<?php
		echo '</div>';	// left ends

		echo '<div id="ocmMemberMenu" style="float:left">';	// right starts

		//echo '<div class="sub_title" style="float:right;font-size: 16pt;margin-left:50px;">';
		//echo CHtml::link('social',array('site/chat'),array('target'=>'_chat'));
		//echo '</div>';
		echo '<div class="sub_title" style="font-size: 16pt;margin-left:50px; float:left; ">';
		echo '<a href="'.Yii::app()->createAbsoluteUrl('/log/index').'">'.__('Log').'</a>';
		echo '</div>';		
		echo '<div class="sub_title" style="font-size: 16pt;margin-left:50px; float:left; ">';
		echo '<a href="http://agora.ocax.net/" target="_agora">'.__('Agora').'</a>';
		echo '</div>';
		echo '<div class="sub_title" style="font-size: 16pt;margin-left:50px; float:left; ">';
		echo '<a href="http://wiki.ocax.net/'.Yii::app()->user->getState('applicationLanguage').':" target="_wiki">Wiki</a>';
		echo '</div>';
		/*
		echo '<div class="sub_title" style="font-size: 16pt;margin-left:50px; float:left; ">';
		echo '<a href="http://ocax.net/pipermail/lista/" target="_list">defunct mailing list</a>';
		echo '</div>';
		*/

		echo '</div>';	// right ends

		//echo '<div class="clear"></div>';
		$panel_separator_added=1;
	}
}
if($model->is_admin ){
	/* Site has just been installed/updated */
	if(!Config::model()->findByPk('siteConfigStatusPostInstallChecked')->value){
		echo '<div style="float:left;">';
		echo '<div style="font-size:1.3em">'.__('OCAx installation').'</div>';
		$this->renderPartial('//config/versionSummary');
		echo '</div>';
		
		echo '<div style="float:left;margin-left:60px;">';
		echo '<div style="font-size:1.3em">'.__('Check server requirements').'</div>';
		$this->renderPartial('//config/postInstallCheck');
		echo '</div>';
		
		echo '<div class="clear"></div>';
		echo '<div class="horizontalRule"></div>';
	}
	/* Configuration not complete */
	if(!Config::model()->findByPk('siteConfigStatus')->value){
		addPanelSeparator();
		echo '<div class="clear"></div>';
		echo '<p></p>';
		$this->renderPartial('//config/pendingConfiguration');
	}
}
if($model->is_team_member){
	addPanelSeparator();
	changeColumn();
	echo '<div class="sub_title">'.CHtml::link(__('Entrusted enquiries'),array('enquiry/assigned'));
	if(	Enquiry::model()->findByAttributes(array('team_member'=>$model->id, 'state'=>ENQUIRY_ASSIGNED)) ||
		Enquiry::model()->findByAttributes(array('team_member'=>$model->id, 'state'=>ENQUIRY_AWAITING_REPLY, 'addressed_to'=>OBSERVATORY)) )
		echo '<i class="icon-attention amber"></i>';
	echo '</div>';
	echo '<p><u>Team member</u><br />'.__('Manage the enquiries you are responsable for').'</p>';
	echo '</div>';
}

if($model->is_editor){
	addPanelSeparator();
	changeColumn();
	echo '<div class="sub_title">'.__('Page editor').'</div>';
	echo '<div style="float:left"><p>';
		echo CHtml::link(__('Introduction pages'), array('/introPage/admin')).'<br />';
		echo CHtml::link(__('Site pages'), array('/sitePage/admin'));
	echo '</p></div>';
	echo '<div style="float:left;margin-left:50px;"><p>';
		echo CHtml::link(__('Wallpaper'), array('/file/wallpaper')).'<br />';
	echo '</p></div>';
	echo '</div>';
}

if($model->is_manager){
	addPanelSeparator();
	changeColumn();
	echo '<div class="sub_title">'.CHtml::link(__('Manage enquiries'),array('enquiry/admin'));
	if(Enquiry::model()->alertTeamManager())
		echo '<i class="icon-attention amber"></i>';
	echo '</div>';
	echo 	'<p><u>Team manager</u><br />'.__('Assign enquiries to team members and check status').'</p>';
	echo '</div>';
}

if($model->is_admin){
	addPanelSeparator();
	changeColumn();
	echo '<div class="sub_title">'.__('Administator\'s options').'</div>';
	echo '<div style="float:left"><p>';
		if(Config::model()->findByPk('siteConfigStatusBudgetDescriptionsImport')->value){
			echo CHtml::link(__('Years and budgets'),array('budget/admin')).'<br />';
			echo CHtml::link(__('Budget descriptions'),array('budgetDescription/admin')).'<br />';
		}
		echo CHtml::link(__('Zip file'),array('file/databaseDownload'));
		if(!Config::model()->isZipFileUpdated())
			echo ' <i class="icon-attention amber"></i>';
		echo '<br />';
		if(Config::model()->findByPk('siteAutoBackup')->value)
			echo CHtml::link(__('Backups'),array('vault/admin')).'<br />';
		else
			echo CHtml::link(__('Manual Backup'),array('backup/manualCreate')).'<br />';
	echo '</p></div>';
	echo '<div style="float:right"><p>';
		echo CHtml::link(__('Users and roles'),array('user/admin')).'<br />';
		echo CHtml::link(__('Newsletters'),array('newsletter/admin')).'<br />';
		echo CHtml::link(__('Email text templates'),array('emailTemplate/admin')).'<br />';
		echo CHtml::link(__('Global parameters'),array('/config')).'<br />';
 	echo '</p></div>';
	echo '</div>';
	echo '</div>';
}
?>

<div class="clear"></div>
<?php if($privilegedUser)
	echo '<div class="horizontalRule" style="padding-top:20px;margin-top:20px;"></div>';
?>

<?php
	echo '<span>';
	include(svgDir().'myenquiries.svg');
	echo '</span>';
?>


<?php
$showEnquiryGrid=0;
if($enquirys->getData() || $subscribed->getData()){
	$this->widget('EnquiryModal');
	$showEnquiryGrid=1;
	echo '<div>';	// open outer
	echo '<div style="float:left;margin-right:30px;width:550px">';	// open left
}

if($enquirys->getData()){
echo '<div class="sub_title">'.__('My enquiries').'</div>';
$this->widget('PGridView', array(
//	'htmlOptions'=>array('class'=>'pgrid-view pgrid-cursor-pointer'),
//	'cssFile'=>Yii::app()->request->baseUrl.'/css/pgridview.css',
//	'loadingCssClass'=>'pgrid-view-loading',
	'id'=>'enquiry-grid',
	'selectableRows'=>1,
//	'selectionChanged'=>'function(id){ location.href = "'.$this->createUrl('enquiry/view').'/"+$.fn.yiiGridView.getSelection(id);}',
	'template' => '{items}{pager}',
	'dataProvider'=>$enquirys,
    'onClick'=>array(
        'type'=>'javascript',
        'call'=>'showEnquiry',
    ),
	'ajaxUpdate'=>true,
	'columns'=>array(
			array(
				'header'=>__('Enquiries'),
				'name'=>'title',
				'value'=>'$data[\'title\']',
			),
			array(
				'header'=>__('Formulated'),
				'name'=>'created',
				'value'=>'format_date($data[\'created\'])',
			),
			array(
				'header'=>__('State'),
				'name'=>'state',
				'type' => 'raw',
				'value'=>'$data->getHumanStates($data[\'state\'])',
			),
			array('class'=>'PHiddenColumn','value'=>'"$data[id]"'),
)));
echo '<p></p>';
}

if($subscribed->getData()){
echo '<div class="sub_title">'.__('I am subscribed to these enquirytions').'</div>';
echo '<span class="hint">'.__('You will be sent an email when these enquiries are updated').'</span>';
$this->widget('PGridView', array(
	'id'=>'subscribed-grid',
	'template' => '{items}{pager}',
	'dataProvider'=>$subscribed,
    'onClick'=>array(
        'type'=>'javascript',
        'call'=>'showEnquiry',
    ),
	'ajaxUpdate'=>true,
	'columns'=>array(
			array(
				'header'=>__('Enquiries'),
				'name'=>'title',
				'value'=>'$data[\'title\']',
			),
			array(
				'header'=>__('Formulated'),
				'name'=>'created',
				'value'=>'format_date($data[\'created\'])',
			),
			array(
				'header'=>__('State'),
				'name'=>'state',
				'type' => 'raw',
				'value'=>'$data->getHumanStates($data[\'state\'])',
			),
            array('class'=>'PHiddenColumn','value'=>'"$data[id]"'),
)));

}
if($showEnquiryGrid){
	echo '</div>'; // close left
	echo '<div style="float:left;width:350px;">';
	$this->renderPartial('//enquiry/workflow-vertical');
	echo '</div>'; // close right
	echo '</div>'; // close outer
	echo '<div class="clear"></div>';
}
if(!$showEnquiryGrid){
	echo '<div class="sub_title">';
	echo __('Enquiries of your interest will be displayed here');
	echo '</div>';
}

?>


</div>
</div>


<?php if(Yii::app()->user->hasFlash('success')):?>
	<script>
		$(function() {
			$(".flash-success").slideDown('fast');
			setTimeout(function() {
				$('.flash-success').slideUp('fast');
    		}, 5500);
		});
	</script>
    <div class="flash-success" style="display:none">
		<?php echo Yii::app()->user->getFlash('success');?>
    </div>
<?php endif; ?>


<?php if(Yii::app()->user->hasFlash('error')):?>
    <div class="flash-error">
		<?php echo Yii::app()->user->getFlash('error');?>
    </div>
<?php endif; ?>


<?php if(Yii::app()->user->hasFlash('newActivationCodeError')):?>
	<script>
		$(function() { setTimeout(function() {
			$('.flash-error').slideUp('fast');
    	}, 4500);
		});
	</script>
    <div class="flash-error">
		<?php echo Yii::app()->user->getFlash('newActivationCodeError');?>
    </div>
<?php endif; ?>

<?php if(Yii::app()->user->hasFlash('prompt_blockuser')){
	list($name, $user_id) = explode("|", Yii::app()->user->getFlash('prompt_blockuser'));
    echo '<div class="flash-notice">';
		echo __('Do you want to block').' '.$name.'?';
		$url=Yii::app()->request->baseUrl.'/user/block/'.$user_id.'?confirmed=1';
		echo '<button onclick="js:window.location=\''.$url.'\'" style="margin-left:20px;margin-right:20px">'.__('Yes').'</button>';
		echo '<button onclick="$(\'.flash-notice\').slideUp(\'fast\')">'.__('No').'</button>';
	echo '</div>';
} ?>

