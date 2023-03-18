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

   public function id()          { return $this->get('id'); }
   public function name()        { return $this->get('name'); }
   public function type()        { return $this->get('type'); }
   public function level()       { return $this->get('level'); }
   public function description() { return $this->get('description'); }
   public function image()       { return $this->get('image'); }

   public function health()  { return $this->get('health'); }
   public function attack()  { return $this->get('attack'); }
   public function defense() { return $this->get('defense'); }
   public function speed()   { return $this->get('speed'); }

   public function damageFire()      { return $this->get('fire.damage'); }
   public function damageEarth()     { return $this->get('earth.damage'); }
   public function damageLightning() { return $this->get('lightning.damage'); }
   public function damageWind()      { return $this->get('wind.damage'); }
   public function damageWater()     { return $this->get('water.damage'); }

   public function resistFire()      { return $this->get('fire.resist'); }
   public function resistEarth()     { return $this->get('earth.resist'); }
   public function resistLightning() { return $this->get('lightning.resist'); }
   public function resistWind()      { return $this->get('wind.resist'); }
   public function resistWater()     { return $this->get('water.resist'); }

   public function healthMax()  { return $this->get('health.max'); }
   public function attackMax()  { return $this->get('attack.max'); }
   public function defenseMax() { return $this->get('defense.max'); }
   public function speedMax()   { return $this->get('speed.max'); }

   public function damageFireMax()      { return $this->get('fire.damage.max'); }
   public function damageEarthMax()     { return $this->get('earth.damage.max'); }
   public function damageLightningMax() { return $this->get('lightning.damage.max'); }
   public function damageWindMax()      { return $this->get('wind.damage.max'); }
   public function damageWaterMax()     { return $this->get('water.damage.max'); }

   public function resistFireMax()      { return $this->get('fire.resist.max'); }
   public function resistEarthMax()     { return $this->get('earth.resist.max'); }
   public function resistLightningMax() { return $this->get('lightning.resist.max'); }
   public function resistWindMax()      { return $this->get('wind.resist.max'); }
   public function resistWaterMax()     { return $this->get('water.resist.max'); }

   public function healthMin()  { return $this->get('health.min'); }
   public function attackMin()  { return $this->get('attack.min'); }
   public function defenseMin() { return $this->get('defense.min'); }
   public function speedMin()   { return $this->get('speed.min'); }

   public function damageFireMin()      { return $this->get('fire.damage.min'); }
   public function damageEarthMin()     { return $this->get('earth.damage.min'); }
   public function damageLightningMin() { return $this->get('lightning.damage.min'); }
   public function damageWindMin()      { return $this->get('wind.damage.min'); }
   public function damageWaterMin()     { return $this->get('water.damage.min'); }

   public function resistFireMin()      { return $this->get('fire.resist.min'); }
   public function resistEarthMin()     { return $this->get('earth.resist.min'); }
   public function resistLightningMin() { return $this->get('lightning.resist.min'); }
   public function resistWindMin()      { return $this->get('wind.resist.min'); }
   public function resistWaterMin()     { return $this->get('water.resist.min'); }

   public function runes() { return $this->get('runes'); }

   public function enhance($level)
   {
      $this->debug(8,"called");

      if ($level < 0 || $level > 10) { $this->debug(9,"enhance level out of range [0-10]"); return false; }

      $currentLevel = $this->level();

      if (!$currentLevel) { $currentLevel = 0; }

      if ($currentLevel == $level) { return null; }

      $operation = ($currentLevel > $level) ? 'downgrade' : 'upgrade';
      $levels    = abs($currentLevel - $level);

      $this->debug(9,"Enhance level from $currentLevel -> $level");

      foreach ($this->constants->attribs() as $attribName => $attribInfo) {
         if (!$attribInfo['enhance']['percent']) { continue; }

         $currentValue = $this->get($attribName);

         // we don't adjust attributes that are not present on the item
         if (!$currentValue) { continue; }

         $enhancePercent = $attribInfo['enhance']['percent'];
         $enhanceRound   = $attribInfo['enhance']['round'];

         $adjustedValue = $currentValue / ((100 + ($currentLevel*$enhancePercent))/100) * ((100 + ($level*$enhancePercent))/100);
         
         if ($enhanceRound) { $adjustedValue = round($adjustedValue); }

         $this->debug(9,"  $attribName: $currentValue -> $adjustedValue");

         $this->set($attribName,$adjustedValue);
      }

      $this->set('level',$level);

      return true;
   }

   public function generate($values = null, $options = null)
   {
      $this->debug(8,"called");

      if (!is_array($values)) { $values = array(); }

      $godRoll = ($options['godroll']) ? true : false;
      $level   = $options['level'] ?: null;
      $enhance = $options['enhance'] ?: null;

      if (!$values || $godRoll) {
         if ($level && !$enhance) { $enhance = $level; }
         $level = null;
      }

      if (is_null($level)) { $level = 0; }

      $this->debug(9,'Building item ['.$this->name().'] options:'.json_encode($options));

      foreach ($this->constants->attribs() as $attribName => $attribInfo) {
         if (array_key_exists("$attribName.min",$this->data) && array_key_exists("$attribName.max",$this->data)) {
            $minValue    = $this->get("$attribName.min");
            $maxValue    = $this->get("$attribName.max");
            $maxEValue   = $maxValue * 2;  

            if ($godRoll) { $values[$attribName] = $maxValue; }

            $givenValue  = (array_key_exists($attribName,$values)) ? $values[$attribName] : null;
            $givenValid  = ($maxValue < $minValue) ? ($givenValue <= $minValue && $givenValue >= $maxValue) : ($givenValue >= $minValue && $givenValue <= $maxEValue);
            $attribValue = (!is_null($givenValue) && $givenValid) ? $givenValue : $this->random($minValue,$maxValue,$attribInfo['format']);

            $this->debug(9,"Generate: $attribName = $attribValue ".((is_null($givenValue)) ? "(random $minValue ~ $maxValue)" : ''));
            
            $this->set($attribName,$attribValue);
         }
      }

      $this->set('level',$level);

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
         $data[$attribName] = $this->get($attribName);
      }

      foreach ($this->constants->elements() as $element) {
         if ($this->data["$element.damage"]) { $data["$element.damage"] = $this->get("$element.damage"); }
         if ($this->data["$element.resist"]) { $data["$element.resist"] = $this->get("$element.resist"); }
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
         if ($this->data["$element.damage"]) { $elementList['damage'][] = sprintf("%s:%s",strtoupper($element),$this->data["$element.damage"]); }
         if ($this->data["$element.resist"]) { $elementList['resist'][] = sprintf("%s:%s",strtoupper($element),$this->data["$element.resist"]); }
      }

      if ($elementList['damage']) { $display .= "DMG - ".implode(' ',$elementList['damage'])."\n"; } 
      if ($elementList['resist']) { $display .= "RES - ".implode(' ',$elementList['resist'])."\n"; } 
      
      return $display;
   }

   public function load($itemId)
   {
      $this->debug(9,"called");

      $itemId = strtolower($itemId);

      $fileName = sprintf(APP_CONFIGDIR.'/item/%s.json',$itemId);

      if (!file_exists($fileName)) { $this->debug(7,"could not find file for $itemId"); return false; }

      $itemInfo = file_get_contents($fileName);

      return $this->import($itemInfo,$itemId);
   }

   public function import($itemInfo, $itemId)
   {
      $this->debug(8,"called");

      if ($this->is_json($itemInfo)) { $itemInfo = json_decode($itemInfo,true); }

      if (!is_array($itemInfo)) { $this->debug(7,"invalid info info provided"); return false; }

      $info['id'] = $itemId;

      foreach ($itemInfo as $name => $value) { $this->set($name,$value); } 

      return true;
   }
}
?>
