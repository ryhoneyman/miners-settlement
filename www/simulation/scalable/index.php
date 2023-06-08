<?php
include_once 'miners-settlement-init.php';
include_once 'local/minersmain.class.php';

$main = new MinersMain(array(
   'debugLevel'     => 0,
   'errorReporting' => false,
   'sessionStart'   => true,
   'memoryLimit'    => null,
   'sendHeaders'    => true,
   'database'       => true,
   'input'          => true,
   'html'           => true,
   'adminlte'       => true,
));

$main->title('Scalable Simulation');

$input = $main->obj('input');
$html  = $main->obj('html');
$alte  = $main->obj('adminlte');

$main->buildClass('constants','Constants',null,'local/constants.class.php');
$main->buildClass('format','Format',null,'local/format.class.php');

$simEntitlement = $main->getProfileEntitlement('simulation-usage',false);

include 'ui/header.php';

if ($simEntitlement) { 
   $sessionInput = $main->sessionValue('simulation/pageInput') ?: array();
   $pageInput    = processInput($sessionInput);

   $main->var('pageInput',$pageInput);
   $main->var('simEntitlement',$simEntitlement);

   // save the session back without the start parameter
   $main->sessionValue('simulation/pageInput',array_diff_key($pageInput,array_flip(array('start'))));

   print pageDisplay($main);
}
else {
   print $alte->displayRow($alte->displayCard("You are not authorized to use this tool.",array('title' => 'Unauthorized', 'header' => 'bg-danger')));  
}

include 'ui/footer.php';

?>
<?php

function pageDisplay($main)
{
   $html      = $main->obj('html');
   $pageInput = $main->var('pageInput');

   $return = $html->startForm(array('method' => 'post')).
             gearDisplay($main).
             runeDisplay($main).
             tabsDisplay($main).
             $html->endForm().
             resultsDisplay($main).
             //"<div><pre>".json_encode($main->var('pageInput'),JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT)."</pre></div>".
             "<script src='/assets/js/simulation.js?t=".$main->now."' type='text/javascript'></script>\n".
             "<link rel='stylesheet' href='/assets/css/simulation.css?t=".$main->now."'>\n";

   $return .= "<script>\n";

   // Dynamic load any items selected from user session
   foreach ($main->obj('constants')->gearTypes() as $gearType => $gearTypeLabel) {
      if ($pageInput[$gearType]) { $return .= "  updateScalableGear('$gearType');\n"; }
   }

   $return .= "  updateScalableRunes();\n".
              "</script>\n";

   return $return;
}

function resultsDisplay($main)
{
   $alte      = $main->obj('adminlte');
   $pageInput = $main->var('pageInput');

   if (!$pageInput['start'] || !$pageInput['monster']) { return ''; }

   $results = "<div id='results'><i class='fa fa-sync fa-spin'></i></div>";

   return $alte->displayRow($alte->displayCard($results,array('container' => 'col-12 col-xl-9 col-lg-12 col-md-12 col-sm-12', 'title' => 'Results'))).
          "<script>$(function() { loadScalableResults(); });</script>";
}

function tabsDisplay($main)
{
   $tabs = array();

   $html      = $main->obj('html');
   $alte      = $main->obj('adminlte');
   $pageInput = $main->var('pageInput');

   $selectedMonster    = $pageInput['monster'];
   $selectedIterations = $pageInput['iterations'];

   $main->fetchMonsterList();

   $monsterList = array('' => "Select Monster");
   foreach ($main->var('monsterList') as $monsterId => $monsterInfo) {
      $monsterName = $monsterInfo['name'];
      $monsterArea = $monsterInfo['area'] ?: 'General';
      $monsterList[$monsterArea][$monsterName] = $monsterInfo['label'];
   }

   $simEntitlement      = $main->var('simEntitlement');
   $userIterations      = $simEntitlement['iterations'] ?: 10;
   $availableIterations = array(10,25,50,100,500,1000);
   $iterationsList      = array();

   foreach ($availableIterations as $totalIterations) {
      if ($totalIterations <= $userIterations) { $iterationsList[$totalIterations] = "$totalIterations iterations"; }
   }

   $tabs[] = array('name' => 'Monster', 'data' => $html->select('monster',$monsterList,$selectedMonster,array('style' => 'width:300px;')).
                                                  $html->select('iterations',$iterationsList,$selectedIterations,array('class' => 'form-control iterationSelect', 'style' => 'width:150px;')).
                                                  $html->submitButton('start','monster','Run'));
   //$tabs[] = array('name' => 'Tower', 'data' => '');
   //$tabs[] = array('name' => 'Progression', 'data' => $html->select('area',array('Select an Area','Dungeon','Necromancer\'s Lair','Mitar','Einlor'),null,array('style' => 'width:300px;')));

   return $alte->displayRow($alte->displayTabbedCard($tabs,array('container' => 'col-12 col-xl-9 col-lg-9 col-md-9 col-sm-12', 'title' => 'Simulation Selection')));
}

function runeDisplay($main)
{
   $html      = $main->obj('html');
   $alte      = $main->obj('adminlte');
   $pageInput = $main->var('pageInput');

   $runes = "<div id='rune-section'></div>";

   return $alte->displayRow($alte->displayCard($runes,array('container' => 'col-12 col-xl-9 col-lg-12 col-md-12 col-sm-12','title' => 'Rune Selection')));
}

function gearDisplay($main)
{
   $gear = '';

   $html      = $main->obj('html');
   $alte      = $main->obj('adminlte');
   $pageInput = $main->var('pageInput');

   $main->fetchGearList();

   $gearList = $main->var('gearList');

   foreach ($main->obj('constants')->gearTypes() as $gearType => $gearTypeLabel) {
      $typeList   = array('' => "Select $gearTypeLabel", 'none' => 'NONE');
      $selectOpts = array('id' => $gearType, 'class' => 'form-control gear', 'style' => 'width:300px;', 'script' => "onChange='updateScalableGear(&quot;$gearType&quot;,true,true)'");

      foreach ($gearList[$gearType] as $gearId => $gearInfo) {
         $typeList[$gearInfo['name']] = $gearInfo['label'];
         $selectOpts['data'][$gearInfo['name']]['image'] = $gearInfo['image'];
      }

      $typeSelected = $pageInput[$gearType];

      $gear .= "<div class='row'><div style='display:inline-block'>".$html->select($gearType,$typeList,$typeSelected,$selectOpts)."</div>".
               "<div id='$gearType-stats' class='statsSelect'></div></div>";
   }

   return $alte->displayRow($alte->displayCard($gear,array('container' => 'col-12 col-xl-9 col-lg-12 col-md-12 col-sm-12','title' => 'Gear Selection')));
}

function processInput($sessionInput)
{
   $postInput = $_POST;

   // Empty runes are not passed to us by the browser, so we initialize them to empty
   if (!array_key_exists('runes',$postInput) && $postInput['start']) { $postInput['runes'] = array(); }

   $return = array_merge($sessionInput,$postInput);

   return $return;
}

?>
