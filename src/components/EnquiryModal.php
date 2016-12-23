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

class EnquiryModal extends CWidget
{
    public function run()
    {
		?>
<!-- enquiryModal widget start -->

<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/enquiry.css" />
<script src="<?php echo Yii::app()->request->baseUrl;?>/scripts/jquery.bpopup-0.9.4.min.js"></script>
<?php echo $this->getController()->renderPartial('//enquiry/subscribeScript',array(),false,false); ?>


<script>
function showEnquiry(enquiry_id){
	$.ajax({
		url: '<?php echo Yii::app()->request->baseUrl; ?>/enquiry/getEnquiry/'+enquiry_id,
		type: 'GET',
		dataType: 'json',
		beforeSend: function(){
						$('#preview_'+enquiry_id).find('.loading').show();
					},
		complete: function(){ 
						$('#preview_'+enquiry_id).find('.loading').hide();
					},
		success: function(data){
			if(data != 0){
				$("#enquiry_body").html(data.html);
				$('#enquiry_popup').bPopup({
                    modalClose: false
					, follow: ([false,false])
					, positionStyle: 'absolute'
					, modelColor: '#ae34d5'
					, speed: 10
                });
			}
		},
		error: function() {
			alert("Error on show enquiry");
		}
	});
}
</script>

<div id="enquiry_popup" class="modal" style="width:870px;">
	<i class='icon-cancel-circled modalWindowButton bClose'></i>
	<i class='icon-popup modalWindowButton bModal2Page' onclick="js:enquiryModal2Page();"></i>
<div id="enquiry_body"></div>
</div>
<!-- enquiryModal widget stop -->

<?php
    }
}
