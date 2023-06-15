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
   $pageInput = $main->sessionValue('simulation/pageInput');

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

      return "<div class='text-white text-sm' style='font-family:monospace; white-space:pre;'>".
             $simulator->formatResults($results,array('short' => $shortResults, 'name' => $testName)).
             "</div>";
   }


   return 'Failed';
}

function buildAdjustMap($main)
{
   $pageInput = $main->sessionValue('simulation/pageInput');

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
   $pageInput = $main->sessionValue('simulation/pageInput');

   return json_encode($pageInput['runes'],JSON_UNESCAPED_SLASHES);
}

function buildEquipMap($main)
{
   $pageInput = $main->sessionValue('simulation/pageInput');

   $equipMap = array();
 
   foreach ($main->obj('constants')->gearTypes() as $gearType => $gearTypeLabel) {
      $equipMap[$gearType] = array(
         'name'  => $pageInput[$gearType] ?: '',
         'level' => (int)$pageInput[sprintf("%s_level",$gearType)] ?: 0, 
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
