<?php

//    Copyright 2023 - Ryan Honeyman

include_once 'common/base.class.php';
include_once 'local/constants.class.php';
include_once 'common/arrays.class.php';

class Format extends Base
{
   public  $constants = null;
   private $arrays    = null;

   public function __construct($debug = null, $options = null)
   {
      parent::__construct($debug,$options);

      $this->constants = new Constants($debug);
      $this->arrays    = new Arrays($debug);
   }

   public function effects($effectList, $web = false)
   {
      $return      = '';
      $lineBreak   = ($web) ? "<br>\n" : "\n";
      $effectDesc  = $this->constants->effectDesc();
      $effectDepth = $this->arrays->arrayDepth($effectList);

      foreach ($effectList as $affects => $effectAttribList) {
         foreach ($effectAttribList as $attribName => $attribList) {
            $desc   = $effectDesc[$affects][$attribName];
            $format = $desc['format'];
            $vars   = $desc['vars'];

            if (!$format) { continue; }

            // Single effect per attribute won't be in array form, we'll fix that here
            // Player aggregate effects will often stack, but things like runes will only have one attribute per
            if ($effectDepth == 3) { $attribList = array($attribList); } 

            foreach ($attribList as $effectInfo) {
               // We don't need the negative numbers here, the descriptions will say slower or faster
               $effectInfo['percent.adjust'] = abs($effectInfo['percent.adjust']);

               $return .= vsprintf($format.$lineBreak,array_intersect_key($effectInfo,array_fill_keys($vars,true)));
            }
         }
      }

      return $return;
   }
}
?>
