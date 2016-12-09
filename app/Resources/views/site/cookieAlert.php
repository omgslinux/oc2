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

<style>
#cookie_ok{
	border-radius: 4px;
	background-color: #FF4500;
	color: white;
	padding: 0 7px 1px 7px;
	cursor: pointer;
}
</style>
<script>
function acceptCookies(){
	$.ajax({
		url: '<?php echo Yii::app()->request->baseUrl; ?>/site/acceptCookies',
		type: 'GET',
		complete: function(){ $('.cookieAlert').hide(); },
	});
}
</script>
<div class="cookieAlert">
<?php
echo	Config::model()->getObservatoryName().' '.
		__('uses Twitter and Facebook cookies that collect statistics. By using this web site you accept this.').' ';
?>
<span id="cookie_ok" onclick="js:acceptCookies()"><?php echo __('Ok'); ?></span>
</div>
