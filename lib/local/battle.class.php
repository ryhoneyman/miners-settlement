<?php

//    Copyright 2009,2010 - Ryan Honeyman

include_once 'common/base.class.php';
include_once 'local/constants.class.php';

class Battle extends Base
{
   public $constants  = null;
   public $attribList = array();
   public $elements   = array();
   public $entityList = array();
   public $battleInfo = array();

   public function __construct($debug = null, $options = null)
   {
      parent::__construct($debug,$options);

      $this->constants = new Constants($debug);

      $this->attribList = $this->constants->attribs();
      $this->elements   = $this->constants->elements();
   }

   public function start($attacker, $defender, $options = null)
   {
      $this->debug(8,"called");

      $this->entityList = array(
         'attacker' => $attacker,
         'defender' => $defender,
      );

      $this->battleInfo['timer']['max']      = (float)($this->entityList['defender']->data['max.timer'] ?: 300);
      $this->battleInfo['revive']['allow']   = isset($this->entityList['defender']->data['allow.revive']) ? $this->entityList['defender']->data['allow.revive'] : true;
      $this->battleInfo['timer']['battle']   = 0;
      $this->battleInfo['timer']['attacker'] = 0;
      $this->battleInfo['timer']['defender'] = 0;
      $this->battleInfo['action']            = true;
      $this->battleInfo['stats']             = array();

      if (isset($options['revive'])) { $this->battleInfo['revive']['allow'] = $options['revive']; }

      $this->debug(7,"MAIN BATTLEINFO: ".json_encode($this->battleInfo,JSON_UNESCAPED_SLASHES));

      $this->calculateEffects();
      $this->initializeBattleInfo();

      while ($this->battleInfo['timer']['battle'] < $this->battleInfo['timer']['max']) {
         if ($this->battleInfo['action']) { 
            $this->calculateBattleInfo(); 

            if ($this->battleInfo['timer']['battle'] == 0) { 
               foreach ($this->constants->gearTypes() as $gearType) {
                  if (is_a($this->entityList['attacker']->var($gearType),'Item')) {
                     $this->debug(9,$this->entityList['attacker']->var($gearType)->display());
                  }
               }
               $this->debug(7,"ATTACKER BATTLEINFO: ".json_encode($this->battleInfo['attacker'],JSON_UNESCAPED_SLASHES));
               $this->debug(7,"DEFENDER BATTLEINFO: ".json_encode($this->battleInfo['defender'],JSON_UNESCAPED_SLASHES));

               $this->battleInfo['effective']['attacker']  = $this->battleInfo['attacker'];
               $this->battleInfo['effective']['defender'] = $this->battleInfo['defender'];
            }
         }

         $this->battleInfo['action'] = false;

         // Player turn
         if ($this->battleInfo['timer']['attacker'] >= $this->battleInfo['attacker']['speed']) {
            $this->battleInfo['timer']['attacker'] = 0;
             
            if ($this->entityTurn('attacker')) { $this->battleInfo['action'] = true; }
         }

         // Did the Defender die?
         if ($this->entityList['defender']->dead()) {
            if (!$this->battleInfo['revive']['allow'] || !$this->entityList['defender']->revivable()) {
               return array('info' => $this->battleInfo,
                            'results' => array('attacker' => array('final' => 'won', 'reason' => 'victory'),
                                               'defender' => array('final' => 'lost', 'reason' => 'died')));
            }

            // Defender revived
            $this->debug(7,$this->entityList['defender']->name()." revived!");

            $this->entityList['attacker']->revived();
            $this->battleInfo['stats']['revived']++;
         }

         // Monster turn
         if ($this->battleInfo['timer']['defender'] >= $this->battleInfo['defender']['speed']) {
            $this->battleInfo['timer']['defender'] = 0;

            if ($this->entityTurn('defender')) { $this->battleInfo['action'] = true; }
         }

         // Did the Attacker die?
         if ($this->entityList['attacker']->dead()) {
            if (!$this->battleInfo['revive']['allow'] || !$this->entityList['attacker']->revivable()) {
               return array('info' => $this->battleInfo,
                            'results' => array('attacker' => array('final' => 'lost', 'reason' => 'died'),
                                               'defender' => array('final' => 'won', 'reason' => 'victory')));
            }
 
            // Attacker revived
            $this->debug(7,$this->entityList['attacker']->name()." revived!");

            $this->entityList['attacker']->revived();
            $this->battleInfo['stats']['revived']++;
         }

         $this->battleInfo['timer']['battle']  = (float)sprintf("%1.2f",$this->battleInfo['timer']['battle'] + 0.01);
         $this->battleInfo['timer']['attacker']  = (float)sprintf("%1.2f",$this->battleInfo['timer']['attacker'] + 0.01);
         $this->battleInfo['timer']['defender'] = (float)sprintf("%1.2f",$this->battleInfo['timer']['defender'] + 0.01);

         $this->battleInfo['stats']['duration'] = $this->battleInfo['timer']['battle'];
      }

      // Monsters win if players cannot defeat them, otherwise it's a timeout on both sides
      $defenderResult = ($this->entityList['defender']->isMonster()) ? array('final' => 'won', 'reason' => 'victory') 
                                                                     : array('final' => 'lost', 'reason' => 'timeout');

      return array('info' => $this->battleInfo,
                   'results' => array('attacker' => array('final' => 'lost', 'reason' => 'timeout'),
                                      'defender' => $defenderResult));
   }

   public function entityTurn($role)
   {
      $damageList = $this->determineDamage($role);

      if (!$damageList) { return null; }

      $enemyRole  = ($role == 'attacker') ? 'defender' : 'attacker';
      $entityName = ucfirst($this->entityList[$role]->name());
      $enemyName  = ucfirst($this->entityList[$enemyRole]->name());

      $this->battleInfo['stats'][$role]['hits']++;

      // The enemy role has the stun attribute applied if a stun roll is made
      if ($this->battleInfo[$enemyRole]['stun'] && !$this->battleInfo[$enemyRole]['stun.resist']) {
         $this->debug(7,$this->battleInfo['stats']['duration'].": $entityName stunned $enemyName for ".$this->battleInfo[$enemyRole]['stun']."s");
         $this->battleInfo['timer'][$enemyRole] = -($this->battleInfo[$enemyRole]['stun']); 
      }

      foreach ($damageList as $damageType => $damageAmount) {
         $this->battleInfo['stats'][$role]['damage'][$damageType] += $damageAmount;
         $this->battleInfo['stats'][$role]['damage']['total'] += $damageAmount;

         $this->entityList[$enemyRole]->health(-$damageAmount);

         $this->debug(7,$this->battleInfo['stats']['duration'].": $entityName hit with $damageType damage for $damageAmount ($enemyName at ".$this->entityList[$enemyRole]->health()." health)");

         if ($this->battleInfo[$role]['lifesteal']) {
            $lifestealAmount = $damageAmount * $this->battleInfo[$role]['lifesteal'];

            $this->entityList[$role]->health($lifestealAmount);

            $this->debug(7,$this->battleInfo['stats']['duration'].": $entityName healed for $lifestealAmount lifesteal (now at ".$this->entityList[$role]->health()." health)");
         }
      }

      $this->battleInfo['stats'][$role]['dps'] = (float)sprintf("%1.1f",$this->battleInfo['stats'][$role]['damage']['total'] / $this->battleInfo['stats']['duration']);

      return true;
   }

   public function determineDamage($attacker)
   {
      $this->debug(8,"called");

      $damage = array();

      $attackRole = $attacker;
      $defendRole = ($attacker == 'attacker') ? 'defender' : 'attacker';

      $attacker = $this->battleInfo[$attackRole];
      $defender = $this->battleInfo[$defendRole];

      $this->debug(9,"determine damage $attackRole -> $defendRole");

      $extraDefense    = ($defender['extra.defense']) ? $defender['extra.defense'] : 1;
      $criticalHit     = ($attacker['critical.hit']) ? true : false;
      $defenderDefense = $defender['defense'] * $extraDefense;
      $normalAttack    = ($attacker['attack'] * (($criticalHit) ? $attacker['critical.hit'] : 1)) - $defenderDefense;

      if ($extraDefense > 1) { $this->debug(7,$this->battleInfo['stats']['duration'].": ".ucfirst($defendRole)." extra defense! (now at $defenderDefense defense)"); }

      if ($normalAttack > 0) { $damage[(($criticalHit) ? 'critical' : 'normal')] = $normalAttack; }

      foreach ($this->elements as $element) {
         $elementAttack = $attacker["$element.damage"] - $defender["$element.resist"];

         if ($elementAttack > 0) { $damage[$element] = $elementAttack; }
      }

      return $damage;
   }

   public function initializeBattleInfo()
   {
      $this->debug(8,"called");

      foreach ($this->entityList as $role => $entity) {
         // initialize role's battle info
         $this->debug(9,"initialize battle info for $role");

         $baseHealth  = (int)$entity->baseValue('health');
         $baseAttack  = (int)$entity->baseValue('attack');
         $baseDefense = (int)$entity->baseValue('defense');
         $baseSpeed   = (float)sprintf("%1.2f",$entity->baseValue('speed'));

         $this->battleInfo['base'][$role] = array(
            'health'  => $baseHealth,
            'attack'  => $baseAttack,
            'defense' => $baseDefense,
            'speed'   => $baseSpeed,
         ); 

         foreach ($this->attribList as $attribName => $attribInfo) {
            if (preg_match('/^(fire|earth|wind|water|lightning)\.(damage|resist)$/i',$attribName)) {
               $this->battleInfo['base'][$role][$attribName] = (int)$entity->baseValue($attribName);
            }
         }

         $roleEffects   = $this->var("$role.effects");
         $entityEffects = ($roleEffects) ? $roleEffects : array();

         if (!array_key_exists('myself',$entityEffects)) { $entityEffects['myself'] = array(); }
         if (!array_key_exists('enemy',$entityEffects))  { $entityEffects['enemy'] = array(); }

         foreach ($entityEffects as $affects => $effectAttribList) {
            foreach ($effectAttribList as $attribName => $effectInfo) {
               if (preg_match('/^critical.hit$/i',$attribName)) {
                  $critPChance = 0;
                  $critPAdjust = 0;
                  $critFAdjust = 0;
                  $critMaxPChance = 0;

                  foreach ($effectInfo as $effectName => $effectValues) {
                     $critPChance += $effectValues['pChance'];
                     $critPAdjust += $effectValues['pAdjust'];
                     $critFAdjust += $effectValues['fAdjust'];

                     if ($effectValues['pChance'] > $critMaxPChance) { $critMaxPChance = $effectValues['pChance']; }
                  }

                  $newEffectValues = array('pChance' => $critPChance, 'pAdjust' => $critPAdjust, 'fAdjust' => $critFAdjust);

                  $entityEffects[$affects][$attribName] = array('CRITICAL HIT' => $newEffectValues);
               }
            }
         }

         $this->battleInfo['base'][$role]["effects"] = $entityEffects;
      } 
   }

   public function calculateBattleInfo()
   {
      $this->debug(8,"called");

      foreach ($this->entityList as $role => $entity) {
         // initialize role's battle info
         $this->debug(9,"calculate battle info for $role");

         $this->battleInfo[$role] = array();

         foreach ($this->constants->primaryAttribs() as $primaryAttribs) { 
            $updateValue = ($primaryAttribs == 'health') ? $entity->health() : $this->battleInfo['base'][$role][$primaryAttribs];

            $this->debug(9,"updating $primaryAttribs to value $updateValue");

            $this->battleInfo[$role][$primaryAttribs] = $updateValue;
         }

         foreach ($this->attribList as $attribName => $attribInfo) {
            if (preg_match('/^(fire|earth|wind|water|lightning)\.(damage|resist)$/i',$attribName)) {
               $this->battleInfo[$role][$attribName] = $this->battleInfo['base'][$role][$attribName];
            }
         }
      }

      foreach ($this->entityList as $role => $entity) {
         $this->debug(9,"$role has ".count($this->battleInfo['base'][$role]['effects']['myself'])." self and ".
                                     count($this->battleInfo['base'][$role]['effects']['enemy'])." enemy effects");


         foreach ($this->battleInfo['base'][$role]['effects'] as $affects => $effectAttribList) {
            foreach ($effectAttribList as $attribName => $effectInfo) {
               // stun resist and extra defense are rolled on-demand, not every turn
               if (preg_match('/^(stun.resist|extra.defense)$/i',$effectName)) { continue; }

               $attribInfo = $this->attribList[$attribName];

               foreach ($effectInfo as $effectName => $effectValues) { 
                  $this->debug(9,"EFFECT: $role/$affects/$effectName/$attribName ".json_encode($effectValues));
                  $pChance = $effectValues['pChance'];
                  $pAdjust = $effectValues['pAdjust'];
                  $fAdjust = $effectValues['fAdjust'];

                  if ($this->rollChance($pChance)) { 
                     if ($pChance != 1) { $this->debug(9,"made a successful roll for $attribName"); }
   
                     if ($attribInfo['only.once'] && $this->battleInfo[$role]['effects'][$affects][$attribName]) { $this->debug(9,"we already have a $attribName loaded"); continue; }

                     $this->debug(9,"BEFORE - $affects/$attribName: ".json_encode($this->battleInfo[$role]['effects'][$affects][$attribName]));

                     if ($pAdjust && !$this->battleInfo[$role]['effects'][$affects][$attribName]['pAdjust']) {
                        $this->battleInfo[$role]['effects'][$affects][$attribName]['pAdjust'] = 1;
                     }
   
                     $this->battleInfo[$role]['effects'][$affects][$attribName]['pAdjust'] *= $pAdjust;
                     $this->battleInfo[$role]['effects'][$affects][$attribName]['fAdjust'] += $fAdjust;

                     $this->debug(9,"AFTER - $affects/$attribName: ".json_encode($this->battleInfo[$role]['effects'][$affects][$attribName]));
                  }
               }
            }
         }

         if ($this->battleInfo[$role]['effects']['myself']) {
            foreach ($this->battleInfo[$role]['effects']['myself'] as $attribName => $effects) {
               $this->debug(9,"$role self effects: $attribName");

               $attribInfo = $this->attribList[$attribName];

               if ($attribInfo['initialize'] && !$this->battleInfo[$role][$attribName]) { $this->battleInfo[$role][$attribName] = $attribInfo['initialize']; }

               $pAdjust = $effects['pAdjust'];
               $fAdjust = $effects['fAdjust'];

               $beforeValue = $this->battleInfo[$role][$attribName];

               if ($pAdjust) { $this->battleInfo[$role][$attribName] = $this->valueCalculate($attribInfo,$this->battleInfo[$role][$attribName] * $pAdjust); }
               if ($fAdjust) { $this->battleInfo[$role][$attribName] = $this->valueCalculate($attribInfo,$this->battleInfo[$role][$attribName] + $fAdjust); }

               $afterValue = $this->battleInfo[$role][$attribName];

               $this->debug(9,"$role/$attribName: ".json_encode($beforeValue)." -> ".json_encode($afterValue)." pAdjust($pAdjust) fAdjust($fAdjust)");
            }
         }
      }

      // once all positive adjustments are done for both attacker and defender, we evaluate negative adjustments for each
      foreach ($this->entityList as $role => $entity) {
         if ($this->battleInfo[$role]['effects']['enemy']) {
            $impactRole = ($role == 'attacker') ? 'defender' : 'attacker';

            foreach ($this->battleInfo[$role]['effects']['enemy'] as $attribName => $effects) {
               $this->debug(9,"$role enemy impacts: $attribName");

               $attribInfo = $this->attribList[$attribName];

               $pAdjust = $effects['pAdjust'];
               $fAdjust = $effects['fAdjust'];

               $beforeValue = $this->battleInfo[$impactRole][$attribName];

               if ($pAdjust) { $this->battleInfo[$impactRole][$attribName] = $this->valueCalculate($attribInfo,$this->battleInfo[$impactRole][$attribName] * $pAdjust); }
               if ($fAdjust) { $this->battleInfo[$impactRole][$attribName] = $this->valueCalculate($attribInfo,$this->battleInfo[$impactRole][$attribName] + $fAdjust); }

               $afterValue = $this->battleInfo[$impactRole][$attribName];

               $this->debug(9,"$impactRole/$attribName: ".json_encode($beforeValue)." -> ".json_encode($afterValue));
            }
         }
      } 
   }

   public function valueCalculate($attribInfo, $value)
   {
      $format = $attribInfo['format'];
      $min    = (array_key_exists('min',$attribInfo)) ? $attribInfo['min'] : null;

      // for floats we need hundredths place precision without rounding, so we use substr to floor the decimals
      if ($format == 'float') { 
         //$calculated = substr(sprintf("%1.3f",$value),0,-1); 
         $calculated = (float)sprintf("%1.2f",$value); 

         if (!is_null($min) && $calculated < $min) { $calculated = $min; }

         return $calculated;
      }
 
      // otherwise we floor an integer
      $calculated = (int)floor($value);

      if (!is_null($min) && $calculated < $min) { $calculated = $min; }

      return $calculated;
   }

   // Load any runes and decode them to their effects + load any innate effects
   public function calculateEffects()
   {
      $this->debug(8,"called");

      foreach ($this->entityList as $role => $entity) { 
         if (!is_a($entity,'Entity')) { $this->debug(9,"valid entity not provided"); return false; }

         $entityName  = $entity->name();
         $entityItems = $entity->items('name');
         $entityRunes = $entity->runes();
         $runeList    = array();

         if (!$entityRunes) { $entityRunes = array(); }

         $this->debug(9,"found ".count($entityRunes)." runes for $entityName");

         // decode rune attribs 
         foreach ($entityRunes as $runeName => $rune) {
            $itemRequired = $rune->requires();

            if ($itemRequired) {
               $this->debug(9,"processing $runeName for $entityName (requires $itemRequired)");

               if (!array_key_exists($rune->requires(),$entityItems)) {
                  $this->debug(9,"required item $itemRequired not equipped, will not use rune");
                  print "REQUIRED ITEM NOT EQUIPED FOR $runeName (needs $itemRequired)\n";
                  exit;
               }
            }
 
            $runeList[$runeName] = $rune->attribs();
         }

         // add any extra innate effects (monsters don't use runes, so this is typically used with monsters)
         if ($entity->effects()) {
            $this->debug(9,"found INNATE effects for $entityName");
            $runeList['INNATE'] = $entity->effects();
         }

         $currentEffects   = array();
         $processedEffects = array();

         foreach ($runeList as $runeName => $runeAttribs) {
            foreach ($runeAttribs as $affects => $runeAttribList) {
               foreach ($runeAttribList as $runeAttribName => $runeAttribValue) {
                  $this->debug(9,"$runeName has $runeAttribName properties ($affects)");

                  $runePChance = $runeAttribValue['percent.chance'];
                  $runePAdjust = $runeAttribValue['percent.adjust'];
                  $runeFAdjust = $runeAttribValue['flat.adjust'];

                  $currentEffects[$affects][$runeAttribName][] = $runeAttribValue;

                  if ($runePAdjust) {
                     // speed is inversely applied (more = slower, less = faster)
                     $percentAdjustBase = (preg_match('/^(speed)$/i',$runeAttribName)) ? (100 - $runePAdjust) : (($runePAdjust < 0) ? (100 + $runePAdjust) : $runePAdjust);
                  }

                  $percentChance = ($runePChance) ? (float)sprintf("%1.2f",$runePChance/100) : 1;
                  $percentAdjust = ($runePAdjust) ? (float)sprintf("%1.2f",$percentAdjustBase/100) : 0;
                  $flatAdjust    = ($runeFAdjust) ? (int)$runeFAdjust : 0;

                  $processedEffects[$affects][$runeAttribName][$runeName] = array('pChance' => $percentChance, 'pAdjust' => $percentAdjust, 'fAdjust' => $flatAdjust);

                  $this->debug(9,"Added effect: $affects/$runeAttribName($runeName) = ".json_encode($processedEffects[$affects][$runeAttribName][$runeName]));
               }
            }
         }

         $entity->var('effects',$currentEffects);

         $this->var("$role.effects",$processedEffects);
      }

      return true;
   }

   // percentChange .01 (1%) to 1 (100%)
   public function rollChance($percentChance)
   {
      if ($percentChance == 1) { return true; }

      $roll    = mt_rand(1,100);
      $chance  = ($percentChance * 100);
      $outcome = ($roll <= $chance) ? true : false;

      $this->debug(8,"rolled a $roll (chance $chance) [".json_encode($outcome)."]");

      return $outcome;
   }
}

?>
