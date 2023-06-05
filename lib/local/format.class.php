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
      $return         = '';
      $lineBreak      = ($web) ? "<br>\n" : "\n";
      $effectDesc     = $this->constants->effectDesc();
      $effectDepth    = $this->arrays->arrayDepth($effectList);
      $elementAttribs = $this->constants->elementAttribs();

      foreach ($effectList as $affects => $effectAttribList) {
         foreach ($effectAttribList as $attribName => $attribList) {
            $attribDesc = $effectDesc[$affects][$attribName];

            if ($web && $attribDesc['web-format'] && $attribDesc['web-vars']) {
               $format = preg_replace_callback('~{{(\S+?)}}~',function($matches) use ($attribDesc) { return $attribDesc[$matches[1]]; },$attribDesc['web-format']);
               $vars   = $attribDesc['web-vars'];
            }
            else {
               $format = $attribDesc['format'];
               $vars   = $attribDesc['vars'];
            }

            if (!$format) { continue; }

            // Single effect per attribute won't be in array form, we'll fix that here
            // Player aggregate effects will often stack, but things like runes will only have one attribute per
            if ($effectDepth == 3) { $attribList = array($attribList); } 

            foreach ($attribList as $effectInfo) {
               // We don't need the negative numbers here, the descriptions will say slower or faster
               $effectInfo['percent.adjust'] = abs($effectInfo['percent.adjust']);

               $effectInfo['color'] = $elementAttribs[$attribName]['color'];
               $effectInfo['icon']  = $elementAttribs[$attribName]['icon'];

               $formatFill = array();
               foreach ($vars as $var) { $formatFill[$var] = $effectInfo[$var]; }

               $return .= vsprintf($format.$lineBreak,$formatFill);
            }
         }
      }

      return $return;
   }

   public function numericReducer($value, $format = null)
   {
      if (is_null($format)) { $format = '%d'; }

      if ($value >= 1000) { 
         return preg_replace('/\.?0+$/','',sprintf("%1.2f",$value / 1000)).'K';
      }
      else { return sprintf($format,$value); }
   }
}
?>
