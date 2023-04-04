<?php

//    Copyright 2023 - Ryan Honeyman

include_once 'common/base.class.php';

class Rune extends Base
{
   public function __construct($debug = null, $options = null)
   {
      parent::__construct($debug,$options);
   }

   public function name()     { return $this->var('name'); }
   public function requires() { return $this->var('requires'); }
   public function attribs()  { return $this->var('attribs'); }

   public function load($runeName)
   {
      $this->debug(8,"called");

      $fileName = sprintf(APP_CONFIGDIR.'/rune/%s.json',strtolower($runeName));

      if (!file_exists($fileName)) { $this->debug(7,"could not find file for $runeName"); return false; }

      $info = file_get_contents($fileName);


      if ($this->is_json($info)) { $info = json_decode($info,true); }

      if (is_array($info)) { foreach ($info as $name => $value) { $this->var($name,$value); } }

      return true;
   }
}
?>
