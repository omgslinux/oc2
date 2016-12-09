<?php

/**
OCAX -- Citizen driven Observatory software
Copyright (C) 2015 OCAX Contributors. See AUTHORS.

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU Affero General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU Affero General Public License for more details.

You should have received a copy of the GNU Affero General Public License
along with this program. If not, see <http://www.gnu.org/licenses/>.
*/

?>

<div id="footer">
	<div id="observatoryFooter">
		<?php echo '<img id="logo" style="float:left;" src="'.Yii::app()->request->baseUrl.'/files/logo.png" />';?>
		<div id="observatoryFooterDetails">
		<?php
			echo '<span id="observatoryFooterName">'.Config::model()->getObservatoryName().'</span><br />';
			echo '<span>'.__('Email').': '.Config::model()->findByPk('emailContactAddress')->value.'</span><br />';
			if($telf = Config::model()->findByPk('telephone')->value)
				echo '<span>'.__('Telephone').': '.$telf.'</span><br />';
			if($blog = Config::model()->findByPk('observatoryBlog')->value)
				echo '<a href="'.$blog.'">'.$blog.'</a><br />';
		?>
		</div>
	</div>

	<div id="PACDFooter">
		<div id="PACDdetails">
			<?php $lang=Yii::app()->language; ?>
			<a href="http://ocmunicipal.net/<?php echo $lang;?>">http://ocmunicipal.net</a><br />
			<a href="http://ocax.net/<?php echo $lang;?>">http://ocax.net</a><br />
			<a href="http://www.gnu.org/licenses/agpl-3.0.html">AGPLv3</a> Copyright &copy; <?php echo date('Y'); ?><br />
			<a href="https://savannah.nongnu.org/projects/ocax">Source hosted on Savannah</a>
		</div>
		<div style="float:right;margin-left:20px">
			<a href="http://auditoriaciudadana.net"><div id="pacd_logo"></div></a>
		</div>
	</div>

<div style="clear:both;"></div>
</div><!-- footer -->

<div style="width:980px;margin:0 auto;margin-top:5px;">
	<div id="postFooterRSSLink">
	<?php
		echo Config::model()->getObservatoryName().' RSS feed ';
		echo CHtml::link('<img src="'.Yii::app()->baseUrl.'/images/rss-16x16.png"/>',array('/site/feed'));
	?>
	</div>
</div>
</div>
