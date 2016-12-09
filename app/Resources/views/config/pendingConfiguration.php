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


$cnt =1;
$config = Config::model();
$config->updateSiteConfigurationStatus();

?>

<div class="sub_title"><?php echo __('Admin tasks');?>
<i class="icon-attention red"></i>
</div>

<p>
<?php
if(!$config->findByPk('siteConfigStatusUptodate')->value){
	echo $cnt.'. '.'<a href="'.getInlineHelpURL(':upgrade').'">'.__('New version available').'. '.__('Upgrade now').'</a>';
	$cnt +=1;
	echo '<br />';
}
if(!$config->findByPk('siteConfigStatusBudgetDescriptionsImport')->value){
	echo $cnt.'. <span style="color:red">'.
				__('Installation is incomplete').'.</span> '.
				__('Budget descriptions table is empty. Please read INSTALL');
	$cnt +=1;
	echo '<br />';
}

if(!$config->findByPk('siteConfigStatusInitials')->value){
	echo $cnt.'. '.CHtml::link(__("The Observatory's initials have not been configured"),array('config/observatory'));
	$cnt +=1;
	echo '<br />';
}

if(!$config->findByPk('siteConfigStatusEmail')->value){
	echo $cnt.'. '.CHtml::link(__("Email has not been configured"),array('config/email'));
	$cnt +=1;
	echo '<br />';
}

if(!$config->findByPk('siteConfigStatusObservatoryName')->value){
	echo $cnt.'. '.CHtml::link(__("The Observatory's name has not been configured"),array('config/observatory'));
	$cnt +=1;
	echo '<br />';
}

if(!$config->findByPk('siteConfigStatusLanguage')->value){
	echo $cnt.'. '.CHtml::link(__('Language(s) have not been configured'),array('config/locale'));
	$cnt +=1;
	echo '<br />';
}

if(!$config->findByPk('siteConfigStatusAdministrationName')->value){
	echo $cnt.'. '.CHtml::link(__('Administration name has not been configured'),array('config/observatory'));
	$cnt +=1;
	echo '<br />';
}

if(!$config->findByPk('siteConfigStatusEmailTemplates')->value){
	$configuredTemplatesTotal = count(EmailTemplate::model()->findAllByAttributes(array('updated'=>1)));
	$totalTemplates = count( EmailTemplate::model()->findAll() );
	if( $configuredTemplatesTotal < $totalTemplates){
		$text = __('%s email templates need to be defined');
		$text = str_replace('%s', $totalTemplates-$configuredTemplatesTotal, $text);
		echo $cnt.'. '.CHtml::link($text,array('emailTemplate/admin'));
		$cnt +=1;
		echo '<br />';
	}
}
?>
</p>
