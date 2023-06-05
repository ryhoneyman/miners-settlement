<?php

//    Copyright 2023 - Ryan Honeyman

include_once 'common/base.class.php';

class Constants extends Base
{
   public $list = null;

   public function __construct($debug = null, $options = null)
   {
      parent::__construct($debug,$options);

      $this->list = json_decode(file_get_contents(APP_CONFIGDIR.'/constants.json'),true);
   }

   public function buildAttribIconClass($attribList)
   {
      $return = array();

      // we can take an array or scalar, but will convert to array in case of the latter
      if (!is_array($attribList)) { $attribList = array($attribList); }

      foreach ($attribList as $attribName) {
         $attribInfo = ($this->attribs())[$attribName];
         $return[]   = sprintf("%s %s",$attribInfo['color'],$attribInfo['icon']);
      }

      return implode(';',$return);
   }

   public function isAttribElemental($attribName)
   {
      return (in_array(strtolower($attribName,$this->elements()))) ? true : false;
   }

   public function primaryAttribs()
   {
      $return = array();
     
      foreach ($this->attribs() as $attribName => $attribInfo) {
         if ($attribInfo['type'] == 'primary') { $return[$attribName] = $attribInfo; }
      }

      return $return;
   }

   public function elementAttribs()
   {
      $return = array();

      foreach ($this->attribs() as $attribName => $attribInfo) {
         if (preg_match('/^elemental/',$attribInfo['type'])) { $return[$attribName] = $attribInfo; }
      }

      return $return;
   }

   public function elements()
   {
      $return = array();

      foreach ($this->elementAttribs() as $attribName => $attribInfo) { $return[$attribInfo['element']]++; }

      return array_keys($return);
   }

   public function maxEnhanceLevel() { return $this->fetch('maxEnhanceLevel'); }
   public function percentColors()   { return $this->fetch('percentColors'); }
   public function effectDesc()      { return $this->fetch('effectDesc'); }
   public function baseAttribs()     { return $this->fetch('baseAttribs'); }
   public function gearTypes()       { return $this->fetch('gearTypes'); }
   public function attribs()         { return $this->fetch('attribs'); }

   public function fetch($section) { return $this->list[$section]; }

   public function list() { return $this->list; }
}
?>
