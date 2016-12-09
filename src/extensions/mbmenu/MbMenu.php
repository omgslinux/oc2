<?php

/**
 * MbMenu class file.
 *
 * @author Mark van den Broek (mark@heyhoo.nl)
 * @copyright Copyright &copy; 2010-2012 HeyHoo
 *
 * http://www.yiiframework.com/extension/mbmenu/
 * License: New BSD License
 */


Yii::import('zii.widgets.CMenu');

class MbMenu extends CMenu
{
    //private $baseUrl;
    //public $cssFile;
    private $nljs;
    public $activateParents=true;

    /**
    * Give the last items css 'last' style
    */
	  protected function cssLastItems($items)
	  {
      $i = max(array_keys($items));
      $item = $items[$i];

		  if(isset($item['itemOptions']['class']))
			  $items[$i]['itemOptions']['class'].=' last';
		  else
			  $items[$i]['itemOptions']['class']='last';

			foreach($items as $i=>$item)
			{
			  if(isset($item['items']))
			  {
          $items[$i]['items']=$this->cssLastItems($item['items']);
        }
      }

      return array_values($items);
    }

	protected function cssParentItems($items)
	{
		foreach($items as $i=>$item)
		{
			if(isset($item['items']))
			{
				if(isset($item['itemOptions']['class']))
					$items[$i]['itemOptions']['class'].=' parent';
				else
					$items[$i]['itemOptions']['class']='parent';
				$items[$i]['items']=$this->cssParentItems($item['items']);
			}
		}
		return array_values($items);
	}

	public function init()
	{
		if(!$this->getId(false))
			$this->setId('nav');

		$this->nljs = "\n";
		$this->items=$this->cssParentItems($this->items);
		$this->items=$this->cssLastItems($this->items);

		parent::init();
	}


	protected function renderMenuRecursive($items)
	  {
	  	  foreach($items as $item)
	  	  {
	  	  	echo CHtml::openTag('li', isset($item['itemOptions']) ? $item['itemOptions'] : array());
	  	  	if(isset($item['url']))
	  	  		echo CHtml::link('<span>'.$item['label'].'</span>',$item['url'],isset($item['linkOptions']) ? $item['linkOptions'] : array());
	  	  	else
	  	  		echo CHtml::link('<span>'.$item['label'].'</span>',"javascript:void(0);",isset($item['linkOptions']) ? $item['linkOptions'] : array());
	  	  	if(isset($item['items']) && count($item['items']))
	  	  	{
	  	  		echo "\n".CHtml::openTag('ul',$this->submenuHtmlOptions)."\n";
	  	  		$this->renderMenuRecursive($item['items']);
	  	  		echo CHtml::closeTag('ul')."\n";
	  	  	}
	  	  	echo CHtml::closeTag('li')."\n";
	  	  }
	  }

	  protected function normalizeItems($items,$route,&$active, $ischild=0)
	  {
	  	foreach($items as $i=>$item)
	  	{
	  		if(isset($item['visible']) && !$item['visible'])
	  		{
	  			unset($items[$i]);
	  			continue;
	  		}
	  		if($this->encodeLabel)
	  			$items[$i]['label']=CHtml::encode($item['label']);
	  		$hasActiveChild=false;
	  		if(isset($item['items']))
	  		{
	  			$items[$i]['items']=$this->normalizeItems($item['items'],$route,$hasActiveChild, 1);
	  			if(empty($items[$i]['items']) && $this->hideEmptyItems)
	  			{
	  				unset($items[$i]['items']);
					if(!isset($item['url']))
					{
						unset($items[$i]);
						continue;
					}
	  			}
	  		}
	  		if(!isset($item['active']))
	  		{
	  			if(($this->activateParents && $hasActiveChild) || $this->isItemActive($item,$route))
	  				$active=$items[$i]['active']=true;
	  			else
	  				$items[$i]['active']=false;
	  		}
	  		else if($item['active'])
	  			$active=true;
	  		if($items[$i]['active'] && $this->activeCssClass!='' && !$ischild)
	  		{
	  			if(isset($item['itemOptions']['class']))
	  				$items[$i]['itemOptions']['class'].=' '.$this->activeCssClass;
	  			else
	  				$items[$i]['itemOptions']['class']=$this->activeCssClass;
	  		}
	  	}
	  	return array_values($items);
	  }

    /**
    * Run the widget
    */
    public function run()
    {
		$htmlOptions['id']='nav-container';
		echo CHtml::openTag('div',$htmlOptions)."\n";
		$htmlOptions['id']='nav-bar';
		echo CHtml::openTag('div',$htmlOptions)."\n";
		parent::run();
		echo CHtml::closeTag('div');
		echo CHtml::closeTag('div');
		echo '<div class="clear"></div>';
    }

}
