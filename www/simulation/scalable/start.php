<?php
include_once 'miners-settlement-init.php';
include_once 'local/minersmain.class.php';

$main = new MinersMain(array(
   'debugLevel'     => 0,
   'errorReporting' => false,
   'sessionStart'   => array('read_and_close' => true),
   'memoryLimit'    => null,
   'sendHeaders'    => true,
   'database'       => true,
   'input'          => true,
   'html'           => true,
   'adminlte'       => true,
));

$main->buildClass('constants','Constants',null,'local/constants.class.php');
$main->buildClass('simulator','Simulator',array('db' => $main->db()),'local/simulator.class.php');

$main->var('sessionName','scalablesim/pageInput');

print pageDisplay($main);

?>
<?php

function pageDisplay($main)
{
   $debug     = $main->obj('debug');
   $html      = $main->obj('html');
   $alte      = $main->obj('adminlte');
   $attribs   = $main->obj('constants')->attribs();
   $simulator = $main->obj('simulator');
   $pageInput = $main->sessionValue($main->var('sessionName'));

   $main->var('pageInput',$pageInput);

   $verbose       = (isset($pageInput['verbose'])) ? true : false;
   $shortResults  = (isset($pageInput['short'])) ? true : false;
   $simulateType  = strtolower($pageInput['type']) ?: 'pve';
   $uArea         = strtolower($pageInput['area']) ?: null;
   $attackerName  = $pageInput['aname'] ?: 'Player';
   $defenderName  = $pageInput['monster'] ?: null;
   $uIterations   = $pageInput['iterations'] ?: 1000;
   $uGodroll      = (isset($pageInput['godroll'])) ? true : false;

   $uEquip  = buildEquipMap($main);
   $uAdjust = buildAdjustMap($main);
   $uRunes  = buildRunesMap($main);

   if ($uArea) { $simulateType = 'area'; }

   //$debug->level($debugLevel);
   $debug->type(DEBUG_HTML);

   $baseConfig = array(
      'attacker'   => array('type' => 'player', 'name' => 'Player'),
      'defender'   => array('type' => 'monster', 'name' => $defenderName),
      'type'       => $simulateType,
      'iterations' => $uIterations,
      'godroll'    => $uGodroll,
      'enhance'    => $uEnhance,
      'adjust'     => $uAdjust,
      'equip'      => $uEquip,
      'runes'      => $uRunes,
      'aname'      => $attackerName,
      'label'      => $simulateType,
   );

   //print "./simulate --defender $defenderName --equip '$uEquip' --adjust '$uAdjust' --runes '$uRunes' --debug 9 --iterations 1\n";

   if (preg_match('/^(pve|pvp)$/i',$simulateType) && $attackerName && $defenderName) {
      $simParams = $baseConfig;
      $results   = $simulator->start($simParams);

      if ($results === false) { return $simulator->error(); }

      if ($verbose) { print json_encode($results,JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT)."\n"; }

      $main->logger('scalableSimulation',array('type' => $results['type'], 'defender' => $results['defender']['name'], 
                                               'iterations' => $results['iterations'], 'time' => sprintf("%1.3f",$results['time']['total'])));

      $logTabs = array(
         array('name' => 'Best Attempt', 'data' => "<div class='text-white text-sm' style='font-family:monospace; white-space:pre;'>".
                                                   $simulator->formatBattleLog($results['log']['max'])."</div>"),
         array('name' => 'Worst Attempt', 'data' => "<div class='text-white text-sm' style='font-family:monospace; white-space:pre;'>".
                                                    $simulator->formatBattleLog($results['log']['min'])."</div>"),
      );

      $tabs = array(
         array('name' => 'Overview', 'data' => "<div class='text-white text-sm' style='font-family:monospace; white-space:pre;'>".
                                               $simulator->formatResults($results,array('short' => $shortResults, 'name' => $testName))."</div>"),
         array('name' => 'Battle Log', 'data' => $alte->displayTabbedCard($logTabs,array('container' => 'col-12', 'card' => 'card-secondary', 'id' => 'battlelog'))),
      );

      return $alte->displayTabbedCard($tabs,array('container' => 'col-12 col-xl-12 col-lg-12 col-md-12 col-sm-12', 'id' => 'results'));
   }


   return 'Failed';
}

function buildAdjustMap($main)
{
   $pageInput = $main->var('pageInput');

   $adjustMap = array();

   foreach ($main->obj('constants')->gearTypes() as $gearType => $gearTypeLabel) {
      $gearBaseName = sprintf("%s_base",$gearType);

      $adjustMap[$gearType] = array('base' => decodePercentage($pageInput[$gearBaseName]));

      foreach ($main->obj('constants')->attribs() as $attribName => $attribInfo) {
         $gearAttribName = sprintf("%s_%s",$gearType,$attribName);

         if (!array_key_exists($gearAttribName,$pageInput)) { continue; }


         $adjustMap[$gearType][$attribName] = decodePercentage($pageInput[$gearAttribName]);
      }
   }

   return json_encode($adjustMap,JSON_UNESCAPED_SLASHES);
}

function buildRunesMap($main)
{
   $pageInput = $main->var('pageInput');

   return json_encode($pageInput['runes'],JSON_UNESCAPED_SLASHES);
}

function buildEquipMap($main)
{
   $pageInput    = $main->var('pageInput');
   $itemGearList = $main->getItemGearListByType();
   $hashLookup   = $main->getItemGearHashList();
   $equipMap     = array();
 
   foreach ($main->obj('constants')->gearTypes() as $gearType => $gearTypeLabel) {
      $gearId = $hashLookup[$pageInput[$gearType]];

      if (!$gearId) { continue; }

      $gearEntry = $itemGearList[$gearType][$gearId];
      $gearName  = $gearEntry['name'];

      $equipMap[$gearType] = array(
         'name'    => $gearName ?: '',
         'level'   => 0,
         'enhance' => (int)$pageInput[sprintf("%s_level",$gearType)] ?: 0, 
      );
   }

   return json_encode($equipMap,JSON_UNESCAPED_SLASHES);
}

function decodePercentage($value) 
{ 
   $percent = preg_replace('/^percent-/','',$value);

   return (preg_match('/^\d+$/',$percent)) ? (int)$percent : null; 
}

?>
