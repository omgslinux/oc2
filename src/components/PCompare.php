<?php

/**
OCAX -- Citizen driven Municipal Observatory software
Copyright (C) 2013 OCAX Contributors. See AUTHORS.

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

//http://www.yiiframework.com/forum/index.php/topic/10285-how-to-compare-two-active-record-models/

class PCompare extends CActiveRecordBehavior
{
  public function compare($other) {
    if(!is_object($other))
      return false;

    // does the objects have the same type?
    if(get_class($this->owner) !== get_class($other))
      return false;

    $differences = array();

    foreach($this->owner->attributes as $key => $value) {
      if($this->owner->$key != $other->$key)
        $differences[$key] = array(
            'old' => $this->owner->$key,
            'new' => $other->$key);
    }

    return $differences;
  }
}

?>
