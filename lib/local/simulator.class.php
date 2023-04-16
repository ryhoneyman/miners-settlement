<?php

//    Copyright 2009,2010 - Ryan Honeyman

include_once 'common/base.class.php';
include_once 'local/constants.class.php';
include_once 'local/entity.class.php';
include_once 'local/battle.class.php';

class Simulator extends Base
{
   public $constants      = null;
   public $attribList     = array();
   public $elements       = array();
   public $gearTypes      = array();
   public $primaryAttribs = array();

   public function __construct($debug = null, $options = null)
   {
      parent::__construct($debug,$options);

      $this->constants = new Constants($debug);

      $this->attribList     = $this->constants->attribs();
      $this->elements       = $this->constants->elements();
      $this->gearTypes      = $this->constants->gearTypes();
      $this->primaryAttribs = $this->constants->primaryAttribs();
   }

   public function start($config)
   {
      $this->debug(8,"called");

      $this->debug(9,"config = ".json_encode($config));

      $baseAttacker = new Entity($this->debug);
      $baseDefender = new Entity($this->debug);
      $battle       = new Battle($this->debug);

      $simType    = $config['type'] ?: 'general';
      $iterations = $config['iterations'] ?: 1;
      $godroll    = ($config['godroll']) ? true : false;
      $enhance    = $config['enhance'] ?: null;
      $aName      = $config['aname'] ?: null;

      $stats = array(
         'type'  => $simType,
         'label' => $config['label'] ?: $simType,
         'time'  => array('start' => microtime(true)),
      );

      $baseRoles = array(
         'attacker' => $baseAttacker,
         'defender' => $baseDefender,
      );

      // load in entities and equip them
      foreach ($baseRoles as $role => $entity) {
         if (!$entity->load($config[$role]['id'],array('godroll' => $godroll, 'enhance' => $enhance, 'name' => $aName))) { 
            $this->debug(0,"Could not find $role profile for ".$config[$role][$id]); 
            exit; 
         }

         if ($config[$role]['gear']) {
            foreach ($config[$role]['gear'] as $itemId => $itemInfo) {
               $entity->equipItem($itemId,$itemInfo['values'],$itemInfo['options']);
            }
         }
      }

      $battleOpts = array();

      if ($simType == 'pvp') { $battleOpts['revive'] = false; }

      while ($iterations-- > 0) {
         $attacker = clone $baseAttacker;
         $defender = clone $baseDefender;

         $roles = array(
            'attacker' => $attacker,
            'defender' => $defender,
         );

         $results     = $battle->start($attacker,$defender,$battleOpts);
         $resultStats = $results['info']['stats'];
         $duration    = $resultStats['duration'];

         if ($iterations == 0) {
            foreach ($roles as $role => $entity) {
               $stats[$role]["name"]        = $entity->name();
               $stats[$role]["description"] = $entity->description();
               $stats[$role]["type"]        = $entity->type();

               $stats[$role]["gear"] = array(
                  'runes' => ($entity->runes()) ? array_keys($entity->runes()) : 'NONE',
               );

               $stats[$role]["effects"] = $entity->effects();

               foreach ($this->constants->gearTypes() as $gearType) {
                  $gearItem = $entity->getItemByType($gearType);
                  $stats[$role]["gear"][$gearType] = (is_null($gearItem)) ? null : $gearItem->export();
               }
            }

            $stats['effective'] = $results['info']['effective'];
         }

         //print json_encode($results,JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT)."\n";

         foreach ($roles as $role => $entity) {
            $stats[$role]['results']['final'][$results['results'][$role]['final']]++;
            $stats[$role]['results']['reason'][sprintf("%s/%s",$results['results'][$role]['final'],$results['results'][$role]['reason'])]++;

            $damage = $resultStats[$role]['damage']['total'];
            $dps    = $resultStats[$role]['dps'];
            $hits   = $resultStats[$role]['hits'];

            if      ($damage < $stats[$role]['damage']['min']) { $stats[$role]['damage']['min'] = $damage; }
            else if ($damage > $stats[$role]['damage']['max']) { $stats[$role]['damage']['max'] = $damage; }

            if      ($dps < $stats[$role]['dps']['min']) { $stats[$role]['dps']['min'] = $dps; }
            else if ($dps > $stats[$role]['dps']['max']) { $stats[$role]['dps']['max'] = $dps; }

            if      ($hits < $stats[$role]['hits']['min']) { $stats[$role]['hits']['min'] = $hits; }
            else if ($hits > $stats[$role]['hits']['max']) { $stats[$role]['hits']['max'] = $hits; }


            $stats[$role]['damage']['total'] += $damage;
            $stats[$role]['damage']['spread'][$damage]++;

            $stats[$role]['dps']['total'] += $dps;
            $stats[$role]['dps']['spread'][$dps]++;

            $stats[$role]['hits']['total'] += $hits;
            $stats[$role]['hits']['spread'][$hits]++;
         }

         if      ($duration < $stats['duration']['min']) { $stats['duration']['min'] = $duration; }
         else if ($duration > $stats['duration']['max']) { $stats['duration']['max'] = $duration; }

         $stats['duration']['total'] += $duration;
         $stats['duration']['spread'][$duration]++;

         $stats['iterations']++;
      }

      foreach ($roles as $role => $entity) {
         $stats[$role]['damage']['average'] = floor($stats[$role]['damage']['total'] / $stats['iterations']);
         $stats[$role]['dps']['average']    = sprintf("%1.1f",($stats[$role]['dps']['total'] / $stats['iterations']));
         $stats[$role]['hits']['average']   = floor($stats[$role]['hits']['total'] / $stats['iterations']);
         $stats[$role]['chance.win']        = sprintf("%1.1f%%",($stats[$role]['results']['final']['won'] / $stats['iterations'])*100);
      }

      $stats['duration']['average'] = $stats['duration']['total'] / $stats['iterations'];

      $stats['time']['end']   = microtime(true);
      $stats['time']['total'] = $stats['time']['end'] - $stats['time']['start'];
   
      return $stats;
   }

   public function formatResults($results, $options = null)
   {
      $return = '';

      if ($results['type'] == 'pvp')      { return $this->formatResultsPVP($results,$options); }
      else if ($results['type'] == 'pve') { return $this->formatResultsPVE($results,$options); }
   }

   public function formatResultsPVE($results, $options = null)
   {
      $output = sprintf("=== Monster Simulation Results after %d iteration%s (%s) [%1.3f secs] ==========\n\n",
                        $results['iterations'],(($results['iterations'] == 1) ? '' : 's'),$results['label'],$results['time']['total']);

      $shortOutput = ($options['short']) ? true : false;

      foreach (array('attacker','defender') as $role) {
         if ($shortOutput && $role == 'defender') { continue; }
 
         $output .= sprintf("%s%s\n------------------------------------------------\n",
                            $results[$role]['name'],(($results[$role]['description']) ? ' ('.$results[$role]['description'].')' : ''));

         // Find maximum type length
         $maxTypeLength = 0;
         foreach ($this->gearTypes as $gearType) {
            $typeLength  = strlen($gearType);
            if ($typeLength > $maxTypeLength) { $maxTypeLength = $typeLength; }
         }
         $maxTypeLength += 2;


         if ($results[$role]['type'] == 'player') {
            foreach ($this->gearTypes as $gearType) {
               $typeLength  = strlen($gearType);
               $dotCount    = $maxTypeLength - $typeLength;
               $typeDisplay = sprintf("%s%s:",ucwords(str_replace('.',' ',$gearType)),str_repeat('.',$dotCount));

               $output .= sprintf("%s %s\n",$typeDisplay,$this->formatItem($results[$role]['gear'][$gearType]));
            }
         }

         if (is_array($results[$role]['gear']['runes'])) {
            $dotCount    = $maxTypeLength - strlen('Runes');
            $output .= sprintf("%s%s: %s\n\n",'Runes',str_repeat('.',$dotCount),implode(', ',array_map('strtoupper',$results[$role]['gear']['runes'])));
         }

         $effectList = $results[$role]['effects'];

         if ($effectList && !$shortOutput) {
            $effectDesc = $this->constants->effectDesc();

            foreach ($effectList as $affects => $effectAttribList) {
               foreach ($effectAttribList as $attribName => $attribList) {
                  foreach ($attribList as $effectInfo) { 
                     $desc   = $effectDesc[$affects][$attribName];
                     $format = $desc['format'];
                     $vars   = $desc['vars']; 

                     // We don't need the negative numbers here, the descriptions will say slower or faster
                     $effectInfo['percent.adjust'] = abs($effectInfo['percent.adjust']);

                     if (!$format) { continue; }

                     $output .= vsprintf($format."\n",array_intersect_key($effectInfo,array_fill_keys($vars,true)));
                  }
               }
            }
            $output .= "\n";
         }

         $output .= sprintf("Effective Power: %s\n\n\n",$this->formatAttribs($results['effective'][$role]));
      }

      $output .= "=== Results =======================\n\n";

      foreach (array('attacker','defender') as $role) {
         if ($shortOutput && $role == 'defender') { continue; }

         $enemyRole   = ($role == 'attacker') ? 'defender' : 'attacker';
         $damageSort  = $results[$role]['damage']['spread'];
         $dpsSort     = $results[$role]['dps']['spread'];
         $hitsSort    = $results[$role]['hits']['spread'];
         $resultsSort = $results[$role]['results']['reason'];

         arsort($damageSort);
         arsort($dpsSort);
         arsort($hitsSort);
         arsort($resultsSort);

         $damageList  = array();
         $dpsList     = array();
         $hitsList    = array();
         $resultsList = array();

         foreach ($damageSort as $damage => $occurance) { $damageList[] = sprintf("%8d: %6dx",$damage,$occurance); }
         foreach ($dpsSort as $dps => $occurance) { $dpsList[] = sprintf("%8.1f: %6dx",$dps,$occurance); }
         foreach ($hitsSort as $hits => $occurance) { $hitsList[] = sprintf("%8d: %6dx",$hits,$occurance); }
         foreach ($resultsSort as $result => $occurance) { $resultsList[] = sprintf("%13s: %6dx",$result,$occurance); }

         $maxPercentDamage = $damageSort[$results[$role]['damage']['max']] / $results['iterations'] * 100;
         $maxPercentDps    = $dpsSort[$results[$role]['dps']['max']] / $results['iterations'] * 100;

         $output .= sprintf("%s wins %s of the time against %s\n",$results[$role]['name'],$results[$role]['chance.win'],$results[$enemyRole]['name']);

         $output .= sprintf("%s average DPS: %1.1f (%1.1f%% chance for max dps %1.1f)\n",$results[$role]['name'],
                            $results[$role]['dps']['average'],$maxPercentDps,$results[$role]['dps']['max']);

         $output .= sprintf("%s averaged %d damage with %d hits (%1.1f%% chance for max damage %d / %d hits) over %1.2f seconds.\n\n",
                            $results[$role]['name'],$results[$role]['damage']['average'],$results[$role]['hits']['average'],
                            $maxPercentDamage,$results[$role]['damage']['max'],$results[$role]['hits']['max'],$results['duration']['average']);

         if (!$shortOutput) {
            $formatDisplay = "%17s %17s %17s %22s\n";

            $output .= sprintf($formatDisplay,'Damage','DPS','Hits','Results').
                       sprintf($formatDisplay,str_repeat("=",17),str_repeat("=",17),str_repeat("=",17),str_repeat("=",22));

            while ($damageList || $hitsList || $resultsList) {
               $nextDamage = ($damageList) ? array_shift($damageList) : '';
               $nextDps    = ($dpsList) ? array_shift($dpsList) : '';
               $nextHits   = ($hitsList) ? array_shift($hitsList) : '';
               $nextResult = ($resultsList) ? array_shift($resultsList) : '';
 
               $output .= sprintf($formatDisplay,$nextDamage,$nextDps,$nextHits,$nextResult);
            }
         }

         $output .= "\n\n";
      }

      return $output;
   }

   public function formatResultsPVP($results)
   {
      $output = sprintf("=== PVP Simulation Results after %d iteration%s ==========\n\n",
                        $results['iterations'],(($results['iterations'] == 1) ? '' : 's'));

      foreach (array('attacker','defender') as $role) {
         $output .= sprintf("%s%s\n------------------------------------------------\n",
                            $results[$role]['name'],(($results[$role]['description']) ? ' ('.$results[$role]['description'].')' : ''));

         // Find maximum type length
         $maxTypeLength = 0;
         foreach ($this->gearTypes as $gearType) {
            $typeLength  = strlen($gearType);
            if ($typeLength > $maxTypeLength) { $maxTypeLength = $typeLength; }
         }

         print "max: $maxTypeLength\n";
     
         foreach ($this->gearTypes as $gearType) {
            $typeLength  = strlen($gearType);
            $dotCount    = $maxTypeLength - $typeLength;
            $typeDisplay = sprintf("%s%s:",ucwords(str_replace('.',' ',$gearType)),str_repeat('.',$dotCount));

            $output .= sprintf("%s%s\n",$typeDisplay,$this->formatItem($results[$role]['gear'][$gearType]));
         }

         if (is_array($results[$role]['gear']['runes'])) {
            $dotCount    = $maxTypeLength - strlen('Runes');
            $output .= sprintf("%s%s: %s\n\n",'Runes',$dotCount,implode(', ',array_map('strtoupper',$results[$role]['gear']['runes'])));
         }

         $output .= sprintf("Effective Power: %s\n\n\n",$this->formatAttribs($results['effective'][$role]));
      }

      $output .= "=== Results =======================\n";

      foreach (array('attacker','defender') as $role) {
         $nameLength  = strlen($results[$role]['name']);
         $dotCount    = 15 - $nameLength;
         $nameDisplay = sprintf("%s%s:",$results[$role]['name'],str_repeat('.',$dotCount));

         $output .= sprintf("%s Won %6s (averging %d hits for %d total damage over %1.2f seconds)\n",
                            $nameDisplay,$results[$role]['chance.win'],$results[$role]['hits']['average'],
                            $results[$role]['damage']['average'],$results['duration']['average']);
      }

      return $output;
   }

   public function formatEffective($effectiveInfo) 
   {
      return $this->formatAttribs($effectiveInfo);
   }

   public function formatItem($itemInfo)
   {
      if (is_null($itemInfo)) { return 'NONE'; }

      $output = sprintf("%s%s | ",(($itemInfo['level'] > 0) ? '+'.$itemInfo['level'].' ' : ''),$itemInfo['name'],$itemInfo['type']);

      $output .= $this->formatAttribs($itemInfo);

      return $output;
   }

   public function formatAttribs($attribInfo)
   {
      $output = sprintf("%s:%d %s:%d %s:%d %s:%1.2f",$this->attribList['health']['abbr'],$attribInfo['health'],
                                                     $this->attribList['attack']['abbr'],$attribInfo['attack'],
                                                     $this->attribList['defense']['abbr'],$attribInfo['defense'],
                                                     $this->attribList['speed']['abbr'],$attribInfo['speed']);

      $elementList = array('damage' => array(), 'resist' => array());

      foreach ($this->elements as $element) {
         $eleDamage = "$element.damage";
         $eleResist = "$element.resist";

         if ($attribInfo[$eleDamage]) { $elementList['damage'][] = sprintf("%s:%d",$this->attribList[$eleDamage]['abbr'],$attribInfo[$eleDamage]); }
         if ($attribInfo[$eleResist]) { $elementList['resist'][] = sprintf("%s:%d",$this->attribList[$eleResist]['abbr'],$attribInfo[$eleResist]); }
      }

      if ($elementList['damage']) { $output .= sprintf(" | %s",implode(' ',$elementList['damage'])); }
      if ($elementList['resist']) { $output .= sprintf(" | %s",implode(' ',$elementList['resist'])); }

      return $output;
   }
}

?>
