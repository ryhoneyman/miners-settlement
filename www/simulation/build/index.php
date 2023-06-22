<?php
include_once 'miners-settlement-init.php';
include_once 'local/minersmain.class.php';

$main = new MinersMain(array(
   'debugLevel'     => 9,
   'errorReporting' => true,
   'sessionStart'   => true,
   'memoryLimit'    => null,
   'sendHeaders'    => true,
   'database'       => true,
   'input'          => true,
   'html'           => true,
   'adminlte'       => true,
));

$main->buildClass('constants','Constants',null,'local/constants.class.php');
$main->buildClass('format','Format',null,'local/format.class.php');

$main->title('Build Simulation');

$input = $main->obj('input');
$html  = $main->obj('html');
$alte  = $main->obj('adminlte');

$main->var('sessionName','buildsim/pageInput');

$buildHash = $input->get('build','alphanumeric,dot,dash,underscore,space');
$load      = ($input->isDefined('load')) ? true : false;
$clear     = ($input->isDefined('clear')) ? true : false;
$start     = ($input->isDefined('start')) ? true : false;

$simEntitlement = $main->getProfileEntitlement('simulation-usage',false);
$overrideAuth   = true;

include 'ui/header.php';

if ($simEntitlement || $overrideAuth) {
   $sessionInput = $main->sessionValue($main->var('sessionName')) ?: array();
   $pageInput    = processInput($sessionInput);

   $main->var('simEntitlement',$simEntitlement);

   if ($load && $buildHash) {
      $playerBuildList     = $main->getPlayerBuildList();
      $playerBuildHashList = $main->getPlayerBuildHashList();
      $buildId             = $playerBuildHashList[$buildHash] ?: null;

      if ($buildId) {
         $buildData = $playerBuildList[$buildId];
         $pageInput = json_decode($buildData['build'],true);
      }
   }

   // save the session back without the load/start parameter
   $main->sessionValue($main->var('sessionName'),array_diff_key($pageInput,array_flip(array('load','start'))));

   if ($clear) {
      $main->sessionValue($main->var('sessionName'),null,true);
      $buildHash = null;
      $pageInput = array();
   }

   $main->var('build',$buildHash);
   $main->var('pageInput',$pageInput);

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
             playerBuildSelect($main).
             $html->endForm().
             $html->startForm(array('method' => 'post')).
             gearDisplay($main).
             runeDisplay($main).
             tabsDisplay($main).
             $html->endForm().
             resultsDisplay($main).
             "<script src='/assets/js/simulation.js?t=".$main->now."' type='text/javascript'></script>\n".
             "<script src='/assets/js/runeselect.js?t=".$main->now."' type='text/javascript'></script>\n".
             "<link rel='stylesheet' href='/assets/css/simulation.css?t=".$main->now."'>\n".
             "<script>\n".
             "  updateRunes('buildsim');\n".
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
          "<script>$(function() { loadBuildSimulationResults(); });</script>";
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
   foreach ($main->getMonsterList() as $monsterId => $monsterInfo) {
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

   $tabs[] = array('name' => 'Monster', 'data' => $html->select('monster',$monsterList,$selectedMonster,array('required' => true, 'style' => 'width:300px;')).
                                                  $html->select('iterations',$iterationsList,$selectedIterations,array('class' => 'form-control iterationSelect', 'style' => 'width:150px;')).
                                                  $html->inputHidden('build',$main->var('build')).
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
   $constants = $main->obj('constants');
   $pageInput = $main->var('pageInput');

   $gearList = array();

   foreach ($main->getPlayerGearList() as $gearId => $gearInfo) {
      $gearList[$gearInfo['type']][$gearId] = $gearInfo;
   }

   foreach ($constants->gearTypes() as $gearType => $gearTypeLabel) {
      $typeList   = array('' => "Select $gearTypeLabel", 'none' => 'NONE');
      $selectOpts = array('id' => $gearType, 'class' => 'form-control gear', 'style' => 'width:800px;', 'script' => "onChange='updateRunes(&quot;buildsim&quot;)'");

      $gearTypeList = $gearList[$gearType] ?: array();

      foreach ($gearTypeList as $gearId => $gearInfo) {
         $gearHash  = $main->hashPlayerGearId($gearId);
         $gearStats = json_decode($gearInfo['stats'],true);
         $gearImage = $gearInfo['image'];
         $gearLabel = str_replace("'",'&apos;',$gearInfo['label']);
         $gearLevel = ($gearStats['level'] > 0) ? sprintf(" +%d ",$gearStats['level']) : '';

         $typeList[$gearHash] = $gearInfo['label'];

         $gearItemStats = formatGearStats($main,$gearStats);

         $selectOpts['data'][$gearHash]['raw'] = sprintf("<img src=&quot;%s&quot; width=25px height=25px> %s%s : [ %s ]",$gearImage,$gearLevel,$gearLabel,$gearItemStats);
      }

      $typeSelected = $pageInput[$gearType];

      $gear .= "<div class='row'><div style='display:inline-block'>".$html->select($gearType,$typeList,$typeSelected,$selectOpts)."</div>".
               "<div id='$gearType-stats' class='statsSelect'></div></div>";
   }

   return $alte->displayRow($alte->displayCard($gear,array('container' => 'col-12 col-xl-9 col-lg-12 col-md-12 col-sm-12','title' => 'Gear Selection')));
}

function formatGearStats($main, $gearStats)
{
   $constants = $main->obj('constants');

   $attribList = array();

   $iconFormat = "<i class=&quot;fa %s&quot;></i>";

   foreach ($constants->attribs() as $attribName => $attribInfo) {
      if (!array_key_exists($attribName,$gearStats)) { continue; }

      $attribIconList = $attribInfo['icon-combo'] ?: $attribName;
      $attribIconData = $constants->buildAttribIconClass($attribIconList);
      $iconGroup      = array_map(function($value) use($iconFormat) { return sprintf($iconFormat,$value); },explode(';',$attribIconData));

      $attribList[] = sprintf("%s %s",implode('',$iconGroup),$gearStats[$attribName]);
   }

   return implode(' | ',$attribList);
}

function playerBuildSelect($main)
{
   $html            = $main->obj('html');
   $alte            = $main->obj('adminlte');
   $selectedBuild   = $main->var('build');
   $playerBuildList = $main->getPlayerBuildList() ?: array();

   $buildList = array();
   foreach ($playerBuildList as $playerBuildId => $playerBuildInfo) {
      $playerBuildHash = $main->hashPlayerBuildId($playerBuildId);
      $buildList[$playerBuildHash] = sprintf("%s (%s)",$playerBuildInfo['player_name'],$playerBuildInfo['name']);
   }

   $selectContent = "<div class='input-group'>".
                    $html->select('build',$buildList,$selectedBuild).
                    "<span class='input-group-append'>".
                    $html->submit('load','Load',array('class' => 'btn btn-warning btn-sm')).
                    $html->submit('clear','Clear',array('class' => 'btn btn-success btn-sm')).
                    "</span></div>";

   return $alte->displayRow($alte->displayCard($selectContent,array('title' => 'Player Builds', 'container' => 'col-12 col-sm-12 col-md-12 col-lg-6 col-xl-6 mb-3', 'card' => 'card-secondary')));
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
