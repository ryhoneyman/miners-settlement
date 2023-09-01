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

   public function craftEntry($craftList = null)
   {
      $tdItemFormat  = "<td class='crafting-slot' style='background-image:url(\"/images/craft-border.png\"); background-size:contain; background-repeat:no-repeat; ".
                        "text-align:center; vertical-align:middle;'><a href='%s'><img src='%s' class='crafting-image' data-toggle='tooltip' title='%s'></a></td>";
      $tdCountFormat  = "<td width=50px style='text-align:center; font-weight:bold;'>%s</a>";
      $itemLinkFormat = "/item/analytics/?item=%s";
   
      $return = '';
   
      $maxRequires = null;
   
      foreach ($craftList as $craftData) {
         $requiredCount = count($craftData['input']);
         if ($requiredCount > $maxRequires) { $maxRequires = $requiredCount; }
      }
   
      foreach ($craftList as $craftData) {
         $return .= "<tr><td colspan=7 style='background-color:#222222; text-align:left;'><b>".implode(", ",array_column($craftData['output'],'label'))."</b> (".$craftData['limit'].")</td></tr>";
         $return .= "<tr>";
   
         $itemCount = count($craftData['input']);
   
         for ($gap = 0; $gap < ($maxRequires - $itemCount); $gap++) { $return .= "<td></td>"; }
   
   
         foreach ($craftData['input'] as $itemData) {
            $itemLabel = $itemData['label'];
            $itemName  = $itemData['name'] ?: preg_replace('/\W/','-',str_replace("'",'',strtolower(trim($itemLabel))));
            $itemImage = $itemData['image'];
            $itemLink  = ($itemData['link']) ? sprintf($itemLinkFormat,$itemName) : '#0';
            $return .= sprintf($tdItemFormat,$itemLink,$itemImage,str_replace("'","&apos;",$itemLabel));
         }
   
         $return .= "<td style='font-weight:bold; font-size:30px; color:#e1c675; text-align:center;'>&#10142;</td>";
   
         foreach ($craftData['output'] as $itemData) {
            $itemLabel = $itemData['label'];
            $itemName  = $itemData['name'] ?: preg_replace('/\W/','-',str_replace("'",'',strtolower(trim($itemLabel))));
            $itemImage = $itemData['image'];
            $itemLink  = ($itemData['link']) ? sprintf($itemLinkFormat,$itemName) : '#0';
            $return .= sprintf($tdItemFormat,$itemLink,$itemImage,str_replace("'","&apos;",$itemLabel));
         }
   
         $return .= "</tr>".
                    "<tr>";
   
         for ($gap = 0; $gap < ($maxRequires - $itemCount); $gap++) { $return .= "<td></td>"; }
   
         foreach ($craftData['input'] as $itemData) {
            $itemCount    = $itemData['count'];
            $countDisplay = $this->numericReducer($itemCount);
            $return .= sprintf($tdCountFormat,$countDisplay ?: '');
         }
   
         foreach ($craftData['output'] as $itemData) {
            $itemCount = $itemData['count'];
            $return .= sprintf($tdCountFormat,$itemCount ?: '');
         }
   
   
         $return .= "</tr>".
                    "<tr><td colspan=7 height=25px></td></tr>";
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
