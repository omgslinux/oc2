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

Yii::app()->clientScript->scriptMap['jquery.js'] = false;
Yii::app()->clientScript->scriptMap['jquery.min.js'] = false;

?>
<div class="modalTitle"><?php echo __('Delete enquiry');?> <i class="icon-attention red"></i></div>

<div id="enquiry_body" >
	<?php echo $this->renderPartial('//enquiry/_teamView', array('model'=>$model)); ?>
</div>

<div style="padding:5px;margin:-10px;margin-top:5px;margin-bottom:-10px;">
	<div class="horizontalRule"></div>
	<h1 style="text-align:center;color:black">
		<?php echo __('Are you sure you want to delete it all?');?><i class="icon-attention red"></i>
	</h1>
	<div>
		<div style="float:left;width:60%;color:black;font-size:16px;">
			<ul>
			<?php
			echo '<li>'.__('Reformulated enquires').' ('.$object_count['reforumulated'].')</li>';
			echo '<li>'.__('Replies').' ('.$object_count['replys'].')</li>';
			echo '<li>'.__('Files').' ('.$object_count['files'].')</li>';
			echo '<li>'.__('Record of sent emails').' ('.$object_count['emails'].')</li>';
			echo '<li>'.__('Comments').' ('.$object_count['comments'].')</li>';
			echo '<li>'.__('Votes').' ('.$object_count['votes'].')</li>';
			echo '<li>'.__('User email subscriptions').' ('.$object_count['subscriptions'].')</li>';
			?>
			</ul>
		</div>
	</div>
	<div style="float:left;margin-top:35px;">
		<input	type="button" id="mega_delete_button" enquiry_id=""
				onClick="js:megaDelete(this)"
				value="<?php echo __('Yes, delete it all')?>" />
	</div>
	<div style="clear:both"></div>
</div>





