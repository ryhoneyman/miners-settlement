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

   public function effectDesc()     { return $this->fetch('effectDesc'); }
   public function baseAttribs()    { return $this->fetch('baseAttribs'); }
   public function primaryAttribs() { return $this->fetch('primaryAttribs'); }
   public function gearTypes()      { return $this->fetch('gearTypes'); }
   public function elements()       { return $this->fetch('elements'); }
   public function attribs()        { return $this->fetch('attribs'); }

   public function fetch($section) { return $this->list[$section]; }

   public function list() { return $this->list; }
}
?>
