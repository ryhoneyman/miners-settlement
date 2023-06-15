<?php

//    Copyright 2009,2010 - Ryan Honeyman

include_once 'common/base.class.php';
include_once 'local/constants.class.php';

class Battle extends Base
{
   public $constants  = null;
   public $attribList = array();
   public $elements   = array();
   public $entityData = array();
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

      if ($options['fast.start']) { 
         $this->battleInfo = $options['fast.start'];
         $this->debug(9,"Detected fast start, loading pre-calculated data from previous run!");
      }
      else {
         $this->initBattleInfo(array(
            'attacker' => $attacker,
            'defender' => $defender,
         ),$options);
      }

      $this->debug(7,"MAIN BATTLEINFO: ".json_encode($this->battleInfo,JSON_UNESCAPED_SLASHES));

      while ($this->battleInfo['timer']['battle'] < $this->battleInfo['timer']['max']) {
         if ($this->battleInfo['action']) { 
            $this->calculateBattleInfo(); 
         }

         $this->battleInfo['action'] = false;

         // Player turn
         if ($this->battleInfo['timer']['attacker'] >= $this->battleInfo['attacker']['speed']) {
            $this->battleInfo['timer']['attacker'] = 0;
             
            if ($this->entityTurn('attacker')) { $this->battleInfo['action'] = true; }
         }

         // Did the Defender die?
         if ($this->battleInfo['current']['defender']['health'] <= 0) {
            if (!$this->battleInfo['settings']['allow.revive'] || !$this->battleInfo['info']['defender']['revivable'] || $this->battleInfo['current']['defender']['revived']) {
               return array('info' => $this->battleInfo,
                            'results' => array('attacker' => array('final' => 'won', 'reason' => 'victory'),
                                               'defender' => array('final' => 'lost', 'reason' => 'died')));
            }

            // Defender revived
            $this->revive('defender');
         }

         // Monster turn
         if ($this->battleInfo['timer']['defender'] >= $this->battleInfo['defender']['speed']) {
            $this->battleInfo['timer']['defender'] = 0;

            if ($this->entityTurn('defender')) { $this->battleInfo['action'] = true; }
         }

         // Did the Attacker die?
         if ($this->battleInfo['current']['attacker']['health'] <= 0) {
            if (!$this->battleInfo['settings']['allow.revive'] || !$this->battleInfo['info']['attacker']['revivable'] || $this->battleInfo['current']['attacker']['revived']) {
               return array('info' => $this->battleInfo,
                            'results' => array('attacker' => array('final' => 'lost', 'reason' => 'died'),
                                               'defender' => array('final' => 'won', 'reason' => 'victory')));
            }
 
            // Attacker revived
            $this->revive('attacker');
         }

         $this->battleInfo['timer']['battle']   = (float)sprintf("%1.2f",$this->battleInfo['timer']['battle'] + 0.01);
         $this->battleInfo['timer']['attacker'] = (float)sprintf("%1.2f",$this->battleInfo['timer']['attacker'] + 0.01);
         $this->battleInfo['timer']['defender'] = (float)sprintf("%1.2f",$this->battleInfo['timer']['defender'] + 0.01);
         $this->battleInfo['stats']['duration'] = $this->battleInfo['timer']['battle'];
      }

      // Monsters win if players cannot defeat them, otherwise it's a timeout on both sides
      $defenderResult = ($this->battleInfo['info']['defender']['is.monster']) ? array('final' => 'won', 'reason' => 'victory') 
                                                                              : array('final' => 'lost', 'reason' => 'timeout');

      return array('info' => $this->battleInfo,
                   'results' => array('attacker' => array('final' => 'lost', 'reason' => 'timeout'),
                                      'defender' => $defenderResult));
   }

   public function revive($role)
   {
      $this->logEvent($this->battleInfo['info'][$role]['name']." revived!");

      $this->battleInfo['current'][$role]['revived'] = true;
      $this->battleInfo['current'][$role]['health']  = round($this->battleInfo['base'][$role]['health']/2,0,PHP_ROUND_HALF_DOWN);
      $this->battleInfo['stats'][$role]['revived']++;
   }

   public function initBattleInfo($entityList, $options)
   {
      $this->battleInfo = array();

      $this->battleInfo['roles'] = array_keys($entityList);

      foreach ($entityList as $role => $entity) {
         $this->debug(9,"initialize battle info for $role");

         if (!is_a($entity,'Entity')) { $this->debug(9,"valid entity not provided"); return false; }

         $entityName  = $entity->name();
         $entityItems = $entity->items('name');
         $entityRunes = $entity->runes();
         $runeList    = array();

         if (!$entityRunes) { $entityRunes = array(); }

         $this->debug(9,"found ".count($entityRunes)." runes for $role");

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

         $entityEffectList = array();
         $roleEffects      = array();
         $aggregateEffects = array();

         // process the entity effects
         foreach ($runeList as $runeName => $runeAttribs) {
            foreach ($runeAttribs as $affects => $runeAttribList) {
               foreach ($runeAttribList as $runeAttribName => $runeAttribValue) {
                  $this->debug(9,"$runeName has $runeAttribName properties ($affects)");

                  $entityEffectList[$affects][$runeAttribName][] = $runeAttribValue;

                  $aggregateEffects[$affects][$runeAttribName]['pChances'][] = $runeAttribValue['percent.chance'];
                  $aggregateEffects[$affects][$runeAttribName]['pAdjusts'][] = $runeAttribValue['percent.adjust'];
                  $aggregateEffects[$affects][$runeAttribName]['fAdjusts'][] = $runeAttribValue['flat.adjust'];

                  $this->debug(9,"Added effect: $affects/$runeAttribName($runeName) = ".json_encode($runeAttribValue));
               }
            }
         }

         // store the effect list in order to display them later
         $entity->var('effects',$entityEffectList);

         $attribData = $this->constants->attribs();

         // post-process the aggregated effects
         foreach ($aggregateEffects as $affects => $attribList) {
            foreach ($attribList as $attribName => $attribValue) {
               // we need to resolve stacking on a per attribute basis as some are additive and others multiplicative 
               $stacking = $attribData[$attribName]['stacking'] ?: 'add';

               $attribValue['pChance'] = $this->stackPercentage($stacking,$attribValue['pChances']);
               $attribValue['pAdjust'] = $this->stackPercentage($stacking,$attribValue['pAdjusts']);
               $attribValue['fAdjust'] = array_sum($attribValue['fAdjusts']);  // flat adjusts are always additive

               $pChance = ($attribValue['pChance'] > 1) ? 1 : $attribValue['pChance'];  // cap at 100%
               $pAdjust = $attribValue['pAdjust'];
               $fAdjust = $attribValue['fAdjust'];

               if ($pAdjust) {
                  $pAdjustBase = ($attribData[$attribName]['inverse']) ? (($pAdjust >= 1) ? $pAdjust : (1 - $pAdjust)) : (($pAdjust < 0) ? (1 + $pAdjust) : $pAdjust);
               }

               $percentChance = ($pChance) ? (float)sprintf("%1.2f",$pChance) : 1;
               $percentAdjust = ($pAdjust) ? (float)sprintf("%1.2f",$pAdjustBase) : 0;
               $flatAdjust    = ($fAdjust) ? (int)$fAdjust : 0;

               $roleEffects[$affects][$attribName] = array('pChance' => $percentChance, 'pAdjust' => $percentAdjust, 'fAdjust' => $flatAdjust);
            }
         }

         // dragon's double the percentage to speed adjust against the player
         //if (preg_match('/^(Red|Wind)\s+Dragon\s+L/i',$entityName)) {
         //   $roleEffects['enemy']['speed']['pAdjust'] *= 2;
         //}

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
            if (preg_match('/^(fire|earth|wind|water|lightning)\-(damage|resist)$/i',$attribName)) {
               $this->battleInfo['base'][$role][$attribName] = (int)$entity->baseValue($attribName);
            }
         }

         $entityEffects = ($roleEffects) ? $roleEffects : array();

         if (!array_key_exists('myself',$entityEffects)) { $entityEffects['myself'] = array(); }
         if (!array_key_exists('enemy',$entityEffects))  { $entityEffects['enemy'] = array(); }

         $this->battleInfo['base'][$role]["effects"] = $entityEffects;

         $this->battleInfo['current'][$role]["health"]  = $this->battleInfo['base'][$role]['health'];
         $this->battleInfo['current'][$role]['revived'] = false;

         $this->battleInfo['info'][$role]['name']       = ucfirst($entity->name());
         $this->battleInfo['info'][$role]['is.monster'] = $entity->isMonster();
         $this->battleInfo['info'][$role]['revivable']  = ($this->battleInfo['info'][$role]['is.monster']) ? false : true;
      } 

      $playerRevivable = $entityList['defender']->var('revivable');

      $this->battleInfo['settings']['allow.revive'] = (!is_null($playerRevivable)) ? $playerRevivable : true;

      $this->battleInfo['timer']['max']      = (float)($entityList['defender']->var('battle_timer') ?: 300);
      $this->battleInfo['timer']['battle']   = 0;
      $this->battleInfo['timer']['attacker'] = 0;
      $this->battleInfo['timer']['defender'] = 0;
      $this->battleInfo['action']            = true;
      $this->battleInfo['stats']             = array();

      if (isset($options['revive'])) { $this->battleInfo['revive']['allow'] = $options['revive']; }

      $this->battleInfo['fast.start'] = $this->battleInfo;

      return $this->battleInfo;
   }

   public function entityTurn($role)
   {
      $damageList = $this->determineDamage($role);

      if (!$damageList) { return null; }

      $enemyRole  = ($role == 'attacker') ? 'defender' : 'attacker';
      $entityName = $this->battleInfo['info'][$role]['name'];
      $enemyName  = $this->battleInfo['info'][$enemyRole]['name'];

      $this->battleInfo['stats'][$role]['hits']++;

      // The enemy role has the stun attribute applied if a stun roll is made against it
      if ($this->battleInfo[$enemyRole]['stun']) {
         $stunDuration = sprintf("%1.2f",$this->battleInfo[$enemyRole]['stun'] * $this->battleInfo[$enemyRole]['stun-resist']); 

         $this->logEvent("$entityName stunned $enemyName for {$stunDuration}s");

         $this->battleInfo['timer'][$enemyRole] = -($stunDuration); 
      }

      foreach ($damageList as $damageType => $damageAmount) {
         $this->battleInfo['stats'][$role]['damage'][$damageType] += $damageAmount;
         $this->battleInfo['stats'][$role]['damage']['total'] += $damageAmount;

         $this->battleInfo['current'][$enemyRole]['health'] -= $damageAmount;

         $this->logEvent("$entityName hit with $damageType damage for $damageAmount ($enemyName at ".$this->battleInfo['current'][$enemyRole]['health']." health)");

         if ($this->battleInfo[$role]['lifesteal']) {
            $lifestealAmount = round($damageAmount * $this->battleInfo[$role]['lifesteal'],0,PHP_ROUND_HALF_DOWN);

            $this->battleInfo['current'][$role]['health'] += $lifestealAmount; 

            if ($this->battleInfo['current'][$role]['health'] > $this->battleInfo['base'][$role]['health']) { 
               $this->battleInfo['current'][$role]['health'] = $this->battleInfo['base'][$role]['health']; 
            }

            $this->logEvent("$entityName healed for $lifestealAmount lifesteal (now at ".$this->battleInfo['current'][$role]['health']." health)");
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

      $extraDefense    = ($defender['extra-defense']) ? $defender['extra-defense'] : 1;
      $criticalHit     = ($attacker['critical-hit']) ? true : false;
      $defenderDefense = $defender['defense'] * $extraDefense;
      $normalAttack    = ($attacker['attack'] * (($criticalHit) ? $attacker['critical-hit'] : 1)) - $defenderDefense;

      if ($extraDefense > 1) { $this->logEvent($this->battleInfo['info'][$defendRole]['name']." has extra defense! (now at $defenderDefense defense)"); }
      if ($normalAttack > 0) { $damage[(($criticalHit) ? 'critical' : 'normal')] = $normalAttack; }

      foreach ($this->elements as $element) {
         $elementAttack = $attacker["$element-damage"] - $defender["$element-resist"];

         if ($elementAttack > 0) { $damage[$element] = $elementAttack; }
      }

      return $damage;
   }

   public function calculateBattleInfo()
   {
      $this->debug(8,"called");

      foreach ($this->battleInfo['roles'] as $role) {
         // initialize role's battle info
         $this->debug(9,"update battle info for $role");

         $this->battleInfo[$role] = $this->battleInfo['base'][$role];
         $this->battleInfo[$role]['effects'] = array();
      }

      foreach ($this->battleInfo['roles'] as $role) {
         $this->debug(9,"$role has ".count($this->battleInfo['base'][$role]['effects']['myself'])." self and ".
                                     count($this->battleInfo['base'][$role]['effects']['enemy'])." enemy effects");

         foreach ($this->battleInfo['base'][$role]['effects'] as $affects => $effectAttribList) {
            foreach ($effectAttribList as $attribName => $attribValues) {
               $attribInfo = $this->attribList[$attribName];

               $this->debug(9,"EFFECT: $role/$affects/$attribName ".json_encode($attribValues));
               $pChance = $attribValues['pChance'];
               $pAdjust = $attribValues['pAdjust'];
               $fAdjust = $attribValues['fAdjust'];

               if ($this->rollChance($pChance)) { 
                  if ($pChance != 1) { $this->debug(9,"made a successful roll for $attribName"); }
  
                  //if ($attribInfo['only.once'] && $this->battleInfo[$role]['effects'][$affects][$attribName]) { $this->debug(9,"we already have a $attribName loaded"); continue; }

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

         if ($this->battleInfo[$role]['effects']['myself']) {
            foreach ($this->battleInfo[$role]['effects']['myself'] as $attribName => $effects) {
               $this->debug(9,"$role self effects: $attribName");

               $attribInfo = $this->attribList[$attribName];

               if ($attribInfo['initialize'] && !$this->battleInfo[$role][$attribName]) { $this->battleInfo[$role][$attribName] = $attribInfo['initialize']; }

               $pAdjust = $effects['pAdjust'];
               $fAdjust = $effects['fAdjust'];

               $beforeValue = $this->battleInfo[$role][$attribName];

               if ($pAdjust) { $this->battleInfo[$role][$attribName] = $this->valueCalculate($attribInfo,($this->battleInfo[$role][$attribName] ?: 1) * $pAdjust); }
               if ($fAdjust) { $this->battleInfo[$role][$attribName] = $this->valueCalculate($attribInfo,$this->battleInfo[$role][$attribName] + $fAdjust); }

               $afterValue = $this->battleInfo[$role][$attribName];

               $this->debug(9,"$role/$attribName: ".json_encode($beforeValue)." -> ".json_encode($afterValue)." pAdjust($pAdjust) fAdjust($fAdjust)");
            }
         }

         foreach ($this->attribList as $attribName => $attribInfo) {
            if (!array_key_exists($attribName,$this->battleInfo[$role])) { continue; }
            $this->battleInfo[$role]['power']['base'][$attribName] = $this->battleInfo[$role][$attribName];
         }
      }

      // once all positive adjustments are done for both attacker and defender, we evaluate negative adjustments for each
      foreach ($this->battleInfo['roles'] as $role) {
         $impactRole = ($role == 'attacker') ? 'defender' : 'attacker';

         if ($this->battleInfo[$role]['effects']['enemy']) {
            foreach ($this->battleInfo[$role]['effects']['enemy'] as $attribName => $effects) {
               $this->debug(9,"$role enemy impacts: $attribName");

               $attribInfo = $this->attribList[$attribName];

               $pAdjust = $effects['pAdjust'];
               $fAdjust = $effects['fAdjust'];

               $beforeValue = $this->battleInfo[$impactRole][$attribName];

               if ($pAdjust) { $this->battleInfo[$impactRole][$attribName] = $this->valueCalculate($attribInfo,($this->battleInfo[$impactRole][$attribName] ?: 1) * $pAdjust); }
               if ($fAdjust) { $this->battleInfo[$impactRole][$attribName] = $this->valueCalculate($attribInfo,$this->battleInfo[$impactRole][$attribName] + $fAdjust); }

               $afterValue = $this->battleInfo[$impactRole][$attribName];

               $this->debug(9,"$impactRole/$attribName: ".json_encode($beforeValue)." -> ".json_encode($afterValue));
            }

         }

         foreach ($this->attribList as $attribName => $attribInfo) {
            if (!array_key_exists($attribName,$this->battleInfo[$impactRole])) { continue; }
            $this->battleInfo[$impactRole]['power']['effective'][$attribName] = $this->battleInfo[$impactRole][$attribName];
         }
      } 
   }

   public function valueCalculate($attribInfo, $value)
   {
      $dataType = $attribInfo['datatype'];
      $format   = $attribInfo['format'];
      $min      = (array_key_exists('min',$attribInfo)) ? $attribInfo['min'] : null;

      // for floats we need hundredths place precision without rounding
      if ($dataType == 'float') { 
         $calculated = (float)sprintf($format,$value); 

         if (!is_null($min) && $calculated < $min) { $calculated = $min; }

         return $calculated;
      }
 
      // otherwise we floor an integer
      $calculated = (int)floor($value);

      if (!is_null($min) && $calculated < $min) { $calculated = $min; }

      return $calculated;
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

   public function stackPercentage($stackType, $stackList)
   {
      $return = null;

      if ($stackType == 'add') { $return = array_sum($stackList)/100; }
      else if ($stackType == 'multiply') {
         $return = 1;
         foreach ($stackList as $stackItem) { $return *= ((100+$stackItem)/100); }
      }

      return $return;
   }

   public function logEvent($message)
   {
      $currentTimer = $this->battleInfo['stats']['duration'];

      $this->battleInfo['log'][] = array('timer' => $currentTimer, 'event' => $message);
      $this->debug(7,"$currentTimer: $message");
   }
}

?>
