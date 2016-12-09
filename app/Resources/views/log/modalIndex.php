<?php
/* @var $this LogController */
/* @var $model Log */

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
<style>
.log_register{
	margin: -1px -10px 1px -10px;
	border-top: 1px solid #cdcbc9;
	font-size: 16px;
	clear: both;
}
.log_created{
	width: 130px;
	padding: 2px 30px 2px 5px;
	white-space: nowrap;
	float: left;
}
.log_message{
	width: 650px;
	padding: 2px 5px 2px 0;
	float: right;
	text-align: left;
}
</style>

<script>
function logModal2Page(){
	$('#log_popup').bPopup().close();
	window.open('<?php echo $this->createAbsoluteUrl('/log/index'); ?>',  '_blank');
}
</script>

<div class="modalTitle"><?php echo __('Log').': '.$title;?></div>

<?php
foreach($logs as $log){
	echo '<div class="log_register">';
	echo '<div class="log_created">'.$log->created.'</div>';
	echo '<div class="log_message">'.$log->message.'</div>';
	echo '</div>';
}
?>
