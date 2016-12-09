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

<script>
function showSubscriptionNotice(el, enquiry_id){
	notice = $(el).parent().find('.subscription_notice');
	$('.subscription_notice').not(notice).each(function(){
		$(this).hide();
	});
	if($(notice).is(':visible')){
		$(notice).fadeOut('fast');
	}else{
		text = $('<div></div>');
		$(text).append('<?php echo CHtml::encode(__('We can notify you by email when this enquiry gets updated'));?>.');
		<?php if(Yii::app()->user->isGuest){ ?>
			$(text).append('<?php echo '<br />'.__('Please login to subscribe');?>.');
		<?php } else { ?>
		if (! $("#subscribe-icon_"+enquiry_id).hasClass('active') ){
			$(text).append('<div style="text-align:center">'+
							'<span class="link" onclick="js:subscribe('+enquiry_id+',true);">'+
							'<?php echo __("Yes please");?>'+
							'</span>'+
							'&nbsp;&nbsp;&nbsp;&nbsp;'+
							'<span class="link" onclick=$(".subscription_notice").fadeOut("fast");>'+
							'<?php echo __("Not right now");?>'+
							'</span>'+
							'</div>'
						);
		}else{
			$(text).append('<div style="text-align:center">'+
							'<span class="link" onclick="js:subscribe('+enquiry_id+',false);">'+
							'<?php echo __("Cancel my subscription");?>'+
							'</span>'+	
							'</div>'					
						);
		}
		<?php } ?>
		$(notice).html(	text );
		$(notice).show();
	}
}
function subscribe(enquiry_id, subscribe){
	$.ajax({
		url: '<?php echo Yii::app()->request->baseUrl; ?>/enquiry/subscribe',
		type: 'POST',
		dataType: 'json',
		data: { 'enquiry': enquiry_id,
				'subscribe': subscribe,
				'YII_CSRF_TOKEN' : '<?php echo Yii::app()->request->csrfToken; ?>',
			  },
		//beforeSend: function(){ },
		//complete: function(){ },
		success: function(data){
				if($('#subscriptionTotal').length>0){
					updateSubscriptionTotal(data);
				}
				$('.subscription_notice').fadeOut('fast', function(){
					icon = $('#subscribe-icon_'+enquiry_id);
					if(subscribe == 1)
						$('.subscribe-icon_'+enquiry_id).addClass('active');
					else
						$('.subscribe-icon_'+enquiry_id).removeClass('active');
				});

		},
		error: function() { alert("error on subscribe"); },
	});
}
$(function() {
	$('body').on('mouseleave', '.enquiryPreview', function() {
		$('.subscription_notice').fadeOut('fast');
	});
});
$(function() {
	$('body').on('mouseleave', '.subscription_notice', function() {
		$('.subscription_notice').fadeOut('fast');
	});
});
</script>

