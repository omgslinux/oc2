<?php

/**
OCAX -- Citizen driven Observatory software
Copyright (C) 2014 OCAX Contributors. See AUTHORS.

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

class ViewLog extends CWidget
{
    public function run()
    {
		?>
<!-- log widget start -->

<script src="<?php echo Yii::app()->request->baseUrl;?>/scripts/jquery.bpopup-0.9.4.min.js"></script>

<script>
function viewLog(prefixes,id){
	id = (typeof id === "undefined") ? 0 : id;
	$.ajax({
		url: '<?php echo Yii::app()->request->baseUrl; ?>/log/modalIndex',
		type: 'GET',
		data: { 'prefixes' : prefixes, 'id' : id },
		beforeSend: function(){ },
		complete: function(){ },
		success: function(data){
			if(data != 0){
				$("#log_popup_content").html(data);
				$('#log_popup').bPopup({
                    modalClose: false
					, follow: ([false,false])
					, positionStyle: 'absolute'
					, modelColor: '#ae34d5'
					, speed: 10
                });
			}
		},
		error: function() {
			alert("Error on show log/index");
		}
	});
}
</script>

<div id="log_popup" class="modal" style="width:800px;">
	<i class='icon-cancel-circled modalWindowButton bClose'></i>
	<i class='icon-popup modalWindowButton bModal2Page' onclick="js:logModal2Page();"></i>
	<div id="log_popup_content"></div>
</div>
<!-- log widget stop -->

<?php
    }
}
