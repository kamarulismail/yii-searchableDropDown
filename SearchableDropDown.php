<?php
/*
 * searchableDropDown jQuery Extension - jQuery plugin
 * @yiiVersion 1.1.6
 */

/**
 * Description of searchableDropDown 
 * URL: http://jsearchdropdown.sourceforge.net/ 
 * @author Kamarul Ariffin Ismail <kamarul.ismail@gmail.com>
 * @version 1.0 
 */

class SearchableDropDown extends CWidget
{
  public $items       = array();
  public $htmlOptions = array();
  
  public $maxListSize   = 50; // if list size are less than maxListSize, show them all
  public $maxMultiMatch = 25; // how many matching entries should be displayed
  public $exactMatch    = false; // Exact matching on search
  public $wildcards     = true;  // Support for wildcard characters (*, ?)
  public $ignoreCase    = true;  // Ignore case sensitivity
  public $latency       = 200;   // how many millis (ms) to wait until starting search
  public $warnMultiMatch = 'top {0} matches ...';	// string to append to a list of entries cut short by maxMultiMatch 
  public $warnNoMatch    = 'no matches ...';			// string to show in the list when no entries match
  public $zIndex         = 'auto';                // zIndex for elements generated by this plugin
  
  private $_baseUrl;
  
  public function init()
  {    
    
    // GET RESOURCE PATH
    $extensionPath = Yii::getPathOfAlias('ext.searchableDropDown');
		$resources     = $extensionPath.'/resources';
    
		// PUBLISH FILES
    $this->_baseUrl = Yii::app()->assetManager->publish($resources, false, -1, YII_DEBUG);
  }
  
  public function run()
  {
    // REGISTER CLIENT SIDE SCRIPT
    $cs = Yii::app()->clientScript;
    $cs->registerCoreScript('jquery');
    
    // REGISTER JS SCRIPT
    $cs->registerScriptFile($this->_baseUrl.'/jquery.searchabledropdown.js');
    
    // REGISTER CSS
    #$cs->registerCssFile($this->_baseUrl.'/css/tipsy.css');
    
    // LOOP THROUGH ITEMS
    $items      = $this->items;
    $scriptList = array();    
    foreach($items as $item)
    {
      if(empty($item))
      {
        continue;
      }
      
      $params      = array();
      $htmlOptions = (isset($item['htmlOptions'])) ? $item['htmlOptions'] : array();
      
      //GENERATE ID FROM MODEL
      if(is_array($item['id']))
      {
        $model     = $item['id']['model'];
        $attribute = $item['id']['attribute'];
        CHtml::resolveNameID($model, $attribute, $htmlOptions);        
        $dropDownID = '[name="'.$htmlOptions['name'].'"]';       
      }
      else
      {
        $dropDownID = '#'.$item['id'];
      }
      
      //OPTIONS
      $defaultOption = array(
                'maxListSize'    => 100,
                'maxMultiMatch'  => 50,
                'exactMatch'     => 'false',
                'wildcards'      => 'true',
                'ignoreCase'     => 'true',
                'latency'        => 200,
                'warnMultiMatch' => 'top {0} matches ...',
                'warnNoMatch'    => 'no matches ...',
                'zIndex'         => 'auto',                                                         
            );
      
      $options = array();
      if(array_key_exists('options', $item))
      {
        if(is_array($item['options']))
        {
          $options = array_merge($defaultOption, $item['options']);
        }
      }
      
      if(empty($options))
      {
        $options = $defaultOption;
      }
      
      $parameterList = '';
      $optionCount   = count($options);
      $optionIndex   = 1;
      foreach($options as $parameterName => $parameterValue)
      {
        $excludeList = array('true', 'false');
        if(is_string($parameterValue) && !in_array($parameterValue, $excludeList))
        {
          $parameterValue = "'{$parameterValue}'"; 
        }
        
        $parameterList .= "{$parameterName} : {$parameterValue} ";
        $parameterList .= ($optionIndex < $optionCount) ? ', ' : '';      
        $optionIndex++;
      }
      
      if(!empty($dropDownID))
      {
        $scriptList[] = "$('{$dropDownID}').searchable({ {$parameterList} });";
      }
      
    }
    
    if(!empty($scriptList))
    {
      $searchableDropDownID = $this->getId();
      
      // GENERATE INIT FUNCTION
      $jsCode = "\nfunction initSearchableDropDown(){\n".
                implode(" \n", $scriptList).
                "\n}\n";
      $cs->registerScript(__CLASS__.'#'.$searchableDropDownID, $jsCode, CClientScript::POS_END);
    
      $jsCode = implode(' ', $scriptList);
      $cs->registerScript(__CLASS__.'#'.$searchableDropDownID, "initSearchableDropDown();", CClientScript::POS_READY);
    }
    
  }
  
}
?>
