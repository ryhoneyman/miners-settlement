<?php

//    Copyright 2023 - Ryan Honeyman

include_once 'common/base.class.php';
include_once 'local/item.class.php';
include_once 'local/constants.class.php';

class Entity extends Base
{
   public $constants   = null;
   public $baseAttribs = array();

   public function __construct($debug = null, $options = null)
   {
      parent::__construct($debug,$options);

      if ($options['role']) { $this->role($options['role']); }

      $this->constants = new Constants($debug);

      $this->baseAttribs = $this->constants->baseAttribs();
   }

   public function baseValue($attribName)
   {
      $value = $this->var($attribName);

      if (!$value && is_null($this->baseAttribs[$attribName]['innateGear'])) { $value = $this->baseAttribs[$attribName]['innateValue']; }

      foreach ($this->constants->gearTypes() as $gearType) {
         $gearItem = $this->var($gearType);

         if (is_a($gearItem,'Item')) { 
            $gearValue = $gearItem->var($attribName); 

            if (!$gearValue && $this->baseAttribs[$attribName]['innateGear'] == $gearType) { $gearValue = $this->baseAttribs[$attribName]['innateValue']; }

            if (!$gearValue) { continue; }

            $this->debug(9,"Item ".$gearItem->name()." contributed $gearValue $attribName");

            $value += $gearValue;
         }
      }

      return $value;
   }

   public function healthMax() 
   { 
      //$this->debug(8,"called");

      if (!$this->var('healthMax')) { $this->var('healthMax',$this->baseValue('health')); }

      return $this->var('healthMax'); 
   }

   public function health($value = null, $set = false)
   {
      //$this->debug(8,"called");

      $currentHealth = $this->var('current.health');

      $maxHealth = $this->healthMax();

      if (is_null($currentHealth)) { $currentHealth = $maxHealth; }

      if (is_null($value)) { return $currentHealth; }

      $value = ceil($value);

      if ($set) { $currentHealth = $value; }
      else      { $currentHealth += $value; }

      $this->debug(9,$this->name()." health adjusted by $value (new value $currentHealth)");

      if ($currentHealth > $maxHealth) { $currentHealth = $maxHealth; }

      $this->debug(9,$this->name()." current health: $currentHealth");

      $this->var('current.health',$currentHealth);

      return $currentHealth;
   }
   
   public function id()   { return $this->var('id'); }
   public function name() { return $this->var('name'); }
   public function type() { return $this->var('type'); }
   public function description() { return $this->var('description'); }

   public function isMonster() { return (($this->type() == 'monster') ? true : false); }

   public function role($role = null) 
   { 
      if (!is_null($role)) { $this->var('role',$role); }

      return $this->var('role'); 
   }

   public function revivable() { return (($this->isMonster() || $this->var('revived')) ? false : true); }

   public function revived() { 
      $this->health(floor($this->healthMax() / 2),true);
      $this->var('revived',true); 
   }

   public function dead() 
   { 
      //$this->debug(8,"called");      

      return (($this->health() <= 0) ? true : false); 
   }

   public function removeRunes($runeIds)
   {
      $currentRunes = $this->var('runes');

      if (!$currentRunes)      { return null; }
      if (!is_array($runeIds)) { $runeIds = array($runeIds); }

      $runeList = $currentRunes;

      foreach (array_unique($runeIds) as $runeId) {
         unset($runeList[$runeId]);
      }

      $this->var('runes',$runeList);
   }

   public function addRunes($runeIds)
   {
      $currentRunes = $this->var('runes');
  
      if (!$currentRunes)      { $currentRunes = array(); }
      if (!is_array($runeIds)) { $runeIds = array($runeIds); }

      $runeList = array();

      foreach (array_unique($runeIds) as $runeId) {
         if (in_array($runeId,array_keys($currentRunes))) { $this->debug(9,"rune already added"); continue; }

         $rune = new Rune($this->debug);

         if (!$rune->load($runeId)) { $this->debug(9,"could not load $runeId"); continue; }

         $runeList[$runeId] = $rune;
      }

      $this->var('runes',$runeList); 
   }

   public function unequipItem($itemType)
   {
      $itemType = strtolower($itemType);

      if (!in_array($itemType,$this->constants->gearTypes())) { $this->debug(9,"unknown gear type"); return false; }

      $this->var($itemType,null);
    
      return true;
   }

   public function equipItem($itemId, $itemData = null, $itemOptions = null)
   {
      $item = new Item($this->debug);

      if ($item->load($itemId) === false) { return false; }

      $item->generate($itemData,$itemOptions);

      $this->debug(9,"equipping ".$item->export('json'));

      $this->var($item->type(),$item);

      return true;
   }

   public function getItemByType($itemType)
   {
      $itemType = strtolower($itemType);

      if (!in_array($itemType,$this->constants->gearTypes())) { $this->debug(9,"unknown gear type"); return false; }

      return ((is_a($this->var($itemType),'Item')) ? $this->var($itemType) : null);
   }

   public function items($key = 'id')   
   { 
      $itemList = array();

      foreach ($this->constants->gearTypes() as $gearType) {
         $gearItem = $this->var($gearType);

         if (is_a($gearItem,'Item')) { $itemList[$gearItem->var($key)] = $gearItem; }
      }

      return $itemList;
   }

   public function weapon()  { return $this->var('weapon'); }
   public function shield()  { return $this->var('shield'); }
   public function amulet()  { return $this->var('amulet'); }
   public function ring()    { return $this->var('ring'); }
   public function runes()   { return $this->var('runes'); }
   public function effects() { return $this->var('effects'); }

   public function export()
   {
      //var_dump($this->data);

      return json_encode($this->data,JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT);
   }

   public function load($entityId, $options = null)
   {
      $this->debug(9,"called");

      $entityId = strtolower($entityId);

      $fileName = sprintf(APP_CONFIGDIR.'/entity/%s.json',$entityId);

      if (!file_exists($fileName)) { $this->debug(7,"could not find file for $entityId"); return false; }

      $entityInfo = json_decode(file_get_contents($fileName),true);

      $entityInfo['id'] = $entityId;

      // Monsters don't get option adjustments
      if ($entityInfo['type'] == 'monster') { $options = array(); }

      if ($options['name']) { $entityInfo['name'] = $options['name']; }
      else {
         if ($options['godroll'])      { $entityInfo['name'] .= " [GODROLL]"; }
         if ($options['enhance'])      { $entityInfo['name'] .= " [ENHANCE=".$options['enhance']."]"; }
      }

      return $this->import($entityInfo,$options);
   }

   public function import($entityInfo, $options = null)
   {
      $this->debug(9,"called");

      if ($this->is_json($entityInfo)) { $entityInfo = json_decode($entityInfo,true); }

      if (!is_array($entityInfo)) { $this->debug(7,"invalid entity info provided"); return false; }

      // clear out any existing data
      $this->data = array();

      foreach ($entityInfo as $name => $value) { 
         if (preg_match('/^equip$/i',$name)) {
            if (!is_array($value)) { $this->debug(7,"bad value for equip directive"); return false; }

            foreach ($value as $itemId => $itemInfo) {
               $itemData    = $itemInfo['data'] ?: null;
               $itemOptions = $itemInfo['options'] ?: array();

               if ($options['godroll']) { $itemOptions['godroll'] = true; } 
               if ($options['enhance']) { $itemOptions['enhance'] = $options['enhance']; } 

               $this->equipItem($itemId,$itemData,$itemOptions);
            }
         }
         else if (preg_match('/^(weapon|amulet|ring|shield)$/i',$name,$match)) {
            $itemType     = $name;
            $itemId       = $value['id'];
            $itemData     = $value['data'] ?: null;
            $itemOptions  = $value['options'] ?: array();

            if ($options['godroll']) { $itemOptions['godroll'] = true; } 
            if ($options['enhance']) { $itemOptions['enhance'] = $options['enhance']; } 

            $this->equipItem($itemId,$itemData,$itemOptions);
         }
         else if (preg_match('/^runes$/i',$name)) {
            $this->addRunes($value);
         }
         else { $this->var($name,$value); }
      }

      // heal to max on load
      $this->health($this->healthMax(),true); 

      return true;
   }
}
?>
