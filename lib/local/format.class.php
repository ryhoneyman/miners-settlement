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

   public function monsterStatsDisplay($monsterData)
   {
      $monsterName    = strtoupper($monsterData['label']);
      $monsterAttribs = json_decode($monsterData['attributes'],true);
      $monsterHealth  = $monsterAttribs['health'];
      $monsterAttack  = $monsterAttribs['attack'];
      $monsterDefense = $monsterAttribs['defense'];
      $monsterSpeed   = $monsterAttribs['speed'];
      $elementalList  = array();

      foreach ($this->constants->elementAttribs() as $elementName => $elementInfo) {
         if (!$monsterAttribs[$elementName]) { continue; }

         $elementalList[] = sprintf("<span class='%s'>%s: %s <i class='fa %s'></i></span>",
                                    $elementInfo['color'],$elementInfo['text'],$monsterAttribs[$elementName],$elementInfo['icon']);
      }

      $return = "<table border=0 class='mr-4 mb-4' style='border-collapse:separate;'>".
                "<tr><td colspan=3 class='text-bold monster-name-td'>$monsterName</td></tr>".
                "<tr><td colspan=3 class='text-bold test-white miners-health-bg monster-health-td'>$monsterHealth / $monsterHealth</td></tr>".
                "<tr>".
                "<td class='miners-attack-border text-center monster-primary-td'>$monsterAttack<i class='fas fa-sword float-right miners-attack align-middle monster-primary-icon'></i></td>".
                "<td class='miners-defense-border text-center monster-primary-td'>$monsterDefense<i class='fas fa-shield-alt float-right miners-defense align-middle monster-primary-icon'></i></td>".
                "<td class='miners-speed-border text-center monster-primary-td'>$monsterSpeed<i class='fas fa-clock float-right miners-speed align-middle monster-primary-icon'></i></td>".
                "</tr>";

      if ($elementalList) {
         $return .= "<tr><td colspan=3 class='monster-element-td'>".implode('<br>',$elementalList)."</td></tr>";
      }

      $return .= "<tr><td colspan=3 class='monster-effects-td text-center' style='height:20px; width:300px; border:3px solid dimgray; border-radius:15px;'>".
                 $this->effects($monsterAttribs['effects'],true)."</td></tr>".
                 "<tr><td colspan=3><span class='float-right mr-2 monster-effects-label'>EFFECTS</span></td></tr>".
                 "</table>";

      return $return;
   }

   public function effects($effectList, $web = false)
   {
      $return      = '';
      $lineBreak   = ($web) ? "<br>\n" : "\n";
      $effectDesc  = $this->constants->effectDesc();
      $effectDepth = $this->arrays->arrayDepth($effectList);
      $attribs     = $this->constants->attribs();

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

               $effectInfo['color'] = $attribs[$attribName]['color'];
               $effectInfo['icon']  = $attribs[$attribName]['icon'];

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
