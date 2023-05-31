<?php

//    Copyright 2023 - Ryan Honeyman

include_once 'common/base.class.php';
include_once 'local/constants.class.php';
include_once 'local/rune.class.php';

class Item extends Base
{
   public $constants = null;

   public function __construct($debug = null, $options = null)
   {
      parent::__construct($debug,$options);

      $this->constants = new Constants($debug);
   }

   public function id()          { return $this->var('id'); }
   public function name()        { return $this->var('name'); }
   public function label()       { return $this->var('label'); }
   public function type()        { return $this->var('type'); }
   public function level()       { return $this->var('level'); }
   public function description() { return $this->var('description'); }
   public function image()       { return $this->var('image'); }

   public function health()  { return $this->var('health'); }
   public function attack()  { return $this->var('attack'); }
   public function defense() { return $this->var('defense'); }
   public function speed()   { return $this->var('speed'); }

   public function damageFire()      { return $this->var('fire-damage'); }
   public function damageEarth()     { return $this->var('earth-damage'); }
   public function damageLightning() { return $this->var('lightning-damage'); }
   public function damageWind()      { return $this->var('wind-damage'); }
   public function damageWater()     { return $this->var('water-damage'); }

   public function resistFire()      { return $this->var('fire-resist'); }
   public function resistEarth()     { return $this->var('earth-resist'); }
   public function resistLightning() { return $this->var('lightning-resist'); }
   public function resistWind()      { return $this->var('wind-resist'); }
   public function resistWater()     { return $this->var('water-resist'); }

   public function healthMax()  { return $this->var('health.max'); }
   public function attackMax()  { return $this->var('attack.max'); }
   public function defenseMax() { return $this->var('defense.max'); }
   public function speedMax()   { return $this->var('speed.max'); }

   public function damageFireMax()      { return $this->var('fire-damage.max'); }
   public function damageEarthMax()     { return $this->var('earth-damage.max'); }
   public function damageLightningMax() { return $this->var('lightning-damage.max'); }
   public function damageWindMax()      { return $this->var('wind-damage.max'); }
   public function damageWaterMax()     { return $this->var('water-damage.max'); }

   public function resistFireMax()      { return $this->var('fire-resist.max'); }
   public function resistEarthMax()     { return $this->var('earth-resist.max'); }
   public function resistLightningMax() { return $this->var('lightning-resist.max'); }
   public function resistWindMax()      { return $this->var('wind-resist.max'); }
   public function resistWaterMax()     { return $this->var('water-resist.max'); }

   public function healthMin()  { return $this->var('health.min'); }
   public function attackMin()  { return $this->var('attack.min'); }
   public function defenseMin() { return $this->var('defense.min'); }
   public function speedMin()   { return $this->var('speed.min'); }

   public function damageFireMin()      { return $this->var('fire-damage.min'); }
   public function damageEarthMin()     { return $this->var('earth-damage.min'); }
   public function damageLightningMin() { return $this->var('lightning-damage.min'); }
   public function damageWindMin()      { return $this->var('wind-damage.min'); }
   public function damageWaterMin()     { return $this->var('water-damage.min'); }

   public function resistFireMin()      { return $this->var('fire-resist.min'); }
   public function resistEarthMin()     { return $this->var('earth-resist.min'); }
   public function resistLightningMin() { return $this->var('lightning-resist.min'); }
   public function resistWindMin()      { return $this->var('wind-resist.min'); }
   public function resistWaterMin()     { return $this->var('water-resist.min'); }

   public function runes() { return $this->var('runes'); }

   public function enhance($level)
   {
      $this->debug(8,"called");

      $maxEnhanceLevel = $this->constants->maxEnhanceLevel();

      if ($level < 0 || $level > $maxEnhanceLevel) { $this->debug(9,"enhance level out of range [0-$maxEnhanceLevel]"); return false; }

      $currentLevel = $this->level();

      if (!$currentLevel) { $currentLevel = 0; }

      if ($currentLevel == $level) { return null; }

      $operation = ($currentLevel > $level) ? 'downgrade' : 'upgrade';
      $levels    = abs($currentLevel - $level);

      $this->debug(9,"Enhance level from $currentLevel -> $level");

      foreach ($this->constants->attribs() as $attribName => $attribInfo) {
         if (!$attribInfo['enhance']['percent']) { continue; }

         $currentValue = $this->var($attribName);

         // we don't adjust attributes that are not present on the item
         if (!$currentValue) { continue; }

         $enhancePercent = $attribInfo['enhance']['percent'];
         $enhanceRound   = $attribInfo['enhance']['round'];

         $adjustedValue = $currentValue / ((100 + ($currentLevel*$enhancePercent))/100) * ((100 + ($level*$enhancePercent))/100);
         
         if ($enhanceRound) { $adjustedValue = round($adjustedValue,0,PHP_ROUND_HALF_DOWN); }

         $this->debug(9,"  $attribName: $currentValue -> $adjustedValue");

         $this->var($attribName,$adjustedValue);
      }

      $this->var('level',$level);

      return true;
   }

   public function generate($values = null, $options = null)
   {
      $this->debug(8,"called");

      if (!is_array($values)) { $values = array(); }

      $godRoll   = ($options['godroll']) ? true : false;
      $adjust    = ($options['adjust']) ?: null;
      $adjValues = array();
      $level     = $options['level'] ?: null;
      $enhance   = $options['enhance'] ?: null;

      if (!$values || $godRoll || $adjust) {
         if ($level && !$enhance) { $enhance = $level; }
         $level = null;
      }

      if (is_null($level)) { $level = 0; }

      if ($adjust && !is_array($adjust)) { $adjValues['base'] = $adjust; }
      else { $adjValues = $adjust; }

      $this->debug(9,'Building item ['.$this->name().'] options:'.json_encode($options));

      foreach ($this->constants->attribs() as $attribName => $attribInfo) {
         if (array_key_exists("$attribName.min",$this->vars) && array_key_exists("$attribName.max",$this->vars)) {
            $minValue    = $this->var("$attribName.min");
            $maxValue    = $this->var("$attribName.max");
            $deltaValue  = ($attribName == 'speed') ? $minValue - $maxValue : $maxValue - $minValue;
            $maxEValue   = $maxValue * (1 + ($this->constants->maxEnhanceLevel() / 10));  

            $adjustAttrib = ($adjValues) ? ((isset($adjValues[$attribName])) ? $adjValues[$attribName] : $adjValues['base']) : null;

            if ($godRoll)                     { $values[$attribName] = $maxValue; }
            else if (!is_null($adjustAttrib)) { $values[$attribName] = $maxValue - (($deltaValue * ((100-$adjustAttrib)/100)) * (($attribName == 'speed') ? -1 : 1)); }

            $givenValue  = (array_key_exists($attribName,$values)) ? $values[$attribName] : null;
            $givenValid  = ($maxValue < $minValue) ? ($givenValue <= $minValue && $givenValue >= $maxValue) : ($givenValue >= $minValue && $givenValue <= $maxEValue);
            $attribValue = (!is_null($givenValue) && $givenValid) ? $givenValue : $this->random($minValue,$maxValue,$attribInfo['format']);

            $this->debug(9,"Generate: $attribName = $attribValue ".((is_null($givenValue)) ? "(random $minValue ~ $maxValue)" : ''));
            
            $this->var($attribName,$attribValue);
         }
      }

      $this->var('level',"$level");

      if ($enhance) { $this->enhance($enhance); }
   }

   public function random($minValue, $maxValue, $format = null)
   {
      //$this->debug(8,"called");

      $this->debug(9,"min: $minValue, max: $maxValue, format: $format");

      if (is_null($format) || preg_match('/^int$/',$format)) { return sprintf("%d",mt_rand($minValue,$maxValue)); }

      if (preg_match('/^float$/',$format)) {
         $multiplier = pow(10,2);
         $reversed   = ($maxValue < $minValue) ? true : false;

         $result = ($reversed) ? mt_rand($maxValue*$multiplier,$minValue*$multiplier) : mt_rand($minValue*$multiplier,$maxValue*$multiplier);

         return sprintf("%1.2f",$result / $multiplier);
      }

      return null;
   }
  
   public function export($encoding = null)
   {
      $data = array(
         'name'        => $this->name(),
         'type'        => $this->type(),
         'level'       => $this->level(),
         'description' => $this->description(),
         'image'       => $this->image(),
      );

      foreach ($this->constants->primaryAttribs() as $attribName) {
         $data[$attribName] = $this->var($attribName);
      }

      foreach ($this->constants->elements() as $element) {
         if ($this->var("$element-damage")) { $data["$element-damage"] = $this->var("$element-damage"); }
         if ($this->var("$element-resist")) { $data["$element-resist"] = $this->var("$element-resist"); }
      }

      return (($encoding == 'json') ? json_encode($data,JSON_UNESCAPED_SLASHES) : $data);
   }

   public function display()
   {
      $itemLevel = $this->level();

      $display = sprintf("%s%s (%s)\nHP:%s ATK:%s DEF:%s SPD:%s\n",
                         (($itemLevel) ? "+$itemLevel " : ''),$this->name(),$this->type(),
                         $this->health() ?: 0,$this->attack() ?: 0,$this->defense() ?: 0,$this->speed() ?: 0);

      $elementList = array();

      foreach ($this->constants->elements() as $element) {
         if ($this->data["$element-damage"]) { $elementList['damage'][] = sprintf("%s:%s",strtoupper($element),$this->data["$element-damage"]); }
         if ($this->data["$element-resist"]) { $elementList['resist'][] = sprintf("%s:%s",strtoupper($element),$this->data["$element-resist"]); }
      }

      if ($elementList['damage']) { $display .= "DMG - ".implode(' ',$elementList['damage'])."\n"; } 
      if ($elementList['resist']) { $display .= "RES - ".implode(' ',$elementList['resist'])."\n"; } 
      
      return $display;
   }

   public function load($itemInfo)
   {
      $this->debug(9,"called");

      return $this->import($itemInfo);
   }

   public function import($itemInfo)
   {
      $this->debug(8,"called");

      if ($this->is_json($itemInfo)) { $itemInfo = json_decode($itemInfo,true); }

      if (!is_array($itemInfo)) { $this->debug(7,"invalid info provided"); return false; }

      foreach ($itemInfo as $name => $value) { $this->var($name,$value); } 

      return true;
   }
}
?>
