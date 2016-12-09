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

/* @var $this ReplyController */
/* @var $model Reply */
?>

<style>
#attachments {
	float:left;
	max-width: <?php echo (Yii::app()->request->isAjaxRequest) ? '395px' : '445px' ?>;
}
.attachment {
	float:left;
	margin-left:-10px;
	max-width: <?php echo (Yii::app()->request->isAjaxRequest) ? '365px' : '415px' ?>;
	height: 24px;
	overflow:hidden;
	font-size: 16px;
}
.attachment:hover {
	cursor: pointer;
	text-decoration: underline;
}
.attachmentDelete {
	float:left;
	padding-top: 6px;
	cursor: pointer;
	font-size: 16px;
}
</style>

<div class="reply">
<?php
	if($user_id = Yii::app()->user->getUserID()){
		if($userVote = Vote::model()->findByAttributes(array('reply'=>$model->id, 'user'=>$user_id)))
			$userVote = $userVote->vote;
	}else
		$userVote = Null;

	echo '<div class="title">';
		echo '<div class="sub_title" style="font-size:28px; float:left; margin:0 15px 0 0;">'.__('Reply').': '.format_date($model->created).'</div>';

			$attachments = File::model()->findAllByAttributes(array('model'=>'Reply','model_id'=>$model->id));
			echo '<div id="attachments">';
			foreach($attachments as $attachment){
				echo '<span	id="attachment_'.$attachment->id.'">';
					echo '<span	class="attachment" onClick="js:viewFile(\''.$attachment->getWebPath().'\');">';
					echo '<i class="icon-attach"></i>'.$attachment->name;
					echo '</span>';	
				if( $model->team_member == $user_id ){
					echo '<i class="icon-cancel-circle red attachmentDelete" onclick="js:deleteFile('.$attachment->id.');"></i>';
				}
			
				echo '</span><br />';
			}
			echo '</div>';
		echo '<span class="voteBlock">';

			$userVote === '1' ? $voted = 'active' : $voted = '';
			echo '<span style="margin-left:15x"></span>';
			echo '<span id="voteUp_'.$model->id.'" class="ocaxButton '.$voted.'" onClick="js:vote('.$model->id.', 1, this);">'.
				 __('Vote').'<i class="icon-thumbs-up"></i>';
			echo '<span class="ocaxButtonCount" id="voteLikeTotal_'.$model->id.'">'.Vote::model()->getTotal($model->id, 1);
			echo '</span></span>';
			
			$userVote === '0' ? $voted = 'active' : $voted = '';
			echo '<span style="margin-left:15px"></span>';
			echo '<span id="voteDown_'.$model->id.'" class="ocaxButton '.$voted.'" onClick="js:vote('.$model->id.', 0, this);">'.
				 __('Vote').'<i class="icon-thumbs-down"></i>';
			echo '<span class="ocaxButtonCount" id="voteDislikeTotal_'.$model->id.'">'.Vote::model()->getTotal($model->id, 0);
			echo '</span></span>';

		echo '</span><div class="clear"></div>';
	echo '</div>';
	if($model->team_member == Yii::app()->user->getUserID()){
		echo '<div class="link" style="margin-top:-10px;float:right;" onClick=\'js:uploadFile("Reply",'.$model->id.');\'>'.__('Add attachment').'</div>';
		echo '<div class="clear"></div>';
	}
	echo '<div class="clear"></div>';
	// reply body
	echo '<p style="padding-top:30px;">'.$model->body.'</p>';

	$this->renderPartial('//comment/_showThread', array('model'=>$model));
?>
</div>
