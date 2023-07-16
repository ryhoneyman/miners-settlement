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
   public function attribs()  { return $this->var('attributes'); }

   public function load($runeInfo)
   {
      $this->debug(8,"called");

      if ($this->isJson($runeInfo)) { $runeInfo = json_decode($runeInfo,true); }

      if (is_array($runeInfo)) { foreach ($runeInfo as $name => $value) { $this->var($name,$value); } }

      return true;
   }
}
?>
