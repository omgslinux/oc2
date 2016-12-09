<?php

/**
 * OCAX -- Citizen driven Observatory software
 * Copyright (C) 2015 OCAX Contributors. See AUTHORS.

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
.addressedToAlert{
	display: none;
}
</style>

<script>
function showAddressedToDialog(){
		$('#alert_popup').bPopup({
							modalClose: false
						, follow: ([false,false])
						, speed: 10
						, positionStyle: 'absolute'
						, modelColor: '#ae34d5'
					});
}
function changeAddressedTo(){
		$('#Enquiry_addressed_to_1').prop("checked",true);
		$('#Enquiry_addressed_to_0').prop("checked",false);
		$('#alert_popup').bPopup().close();
}
function changeAddressedToCanceled(){
	$('#alert_popup').bPopup().close();
	$('#Enquiry_addressed_to_1').prop("checked",false);
	$('#Enquiry_addressed_to_0').prop("checked",true);
}
function reject(){
	$('#Enquiry_state').val('<?php echo ENQUIRY_REJECTED;?>');
	$('#enquiry-form').submit();
}
$(function() {
	$("#Enquiry_addressed_to_1").on('click', function() {
		showAddressedToDialog();
	});
})
</script>

<div id="alert_popup" class="modal" style="width:500px;">
	<div id="alert_popup_content">
	<div class="modalTitle"><?php echo __('Are you sure?');?></div>
	<p style="font-size:18px; padding:20px 0 0 0;">		
		<?php echo '<span>'.__('Are you sure that this enquiry should be replied to by the Observatory and not the Administration?').'</span>';?>
	</p>
	<p>
		<span	class="link" style="font-size:18px;"
				onClick="<?php echo 'js:showHelp(\''.getInlineHelpURL(":manual:enquiry:who-replies").'\')';?>">	
				<?php echo __('Find information about this at the wiki'.'.');?><i class="icon-popup-1"></i>
		</span>
	</p>
	<input type="button" value="&nbsp;<?php echo __('Yes');?>&nbsp;" onClick="js:changeAddressedTo();return false;" />
	&nbsp;&nbsp;&nbsp;
	<input type="button" value="&nbsp;<?php echo __('No');?>&nbsp;" onClick="js:changeAddressedToCanceled();return false;" />
	</div>
</div>
