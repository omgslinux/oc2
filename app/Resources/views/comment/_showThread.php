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

/* @var $this ¿¿ CommentController ?? */
/* @var $model Comment */
?>

<?php
$modelName = get_class($model);

echo '<div class="comments">';

$comments = Comment::model()->findAllByAttributes(array('model'=>get_class($model), 'model_id'=>$model->id));
$visible='';
if(!$comments){
	$visible='style="display:none;"';
}

echo '<div class="show_comments_link link" '.$visible.' onClick="js:toggleComments(\''.$modelName.$model->id.'_comments\')">';
echo __('Comments').' (<span class="comment_count">'.count($comments).'</span>)';
echo '</div>';

if(!$comments){
	echo '<div class="add_comment">';
	echo '<span class="link add_comment_link" onClick=\'js:getCommentForm("'.$modelName.'",'.$model->id.',this)\'>'.__('Add comment').'</span>';
	echo '</div>';	
}

echo '<div id="'.$modelName.$model->id.'_comments" style="display:none">';
	echo '<div class="comments_block">';
	foreach($comments as $comment){
		$this->renderPartial('//comment/_view',array('data'=>$comment),false,false);
	}
	echo '</div>';
	if($comments){
		echo '<div class="add_comment">';
		echo '<span class="link add_comment_link" onClick=\'js:getCommentForm("'.$modelName.'",'.$model->id.',this)\'>'.__('Add comment').'</span>';
		echo '</div>';
	}
	
echo '</div>';
echo '</div>';
?>
