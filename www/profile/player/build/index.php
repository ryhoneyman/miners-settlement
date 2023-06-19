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

$main->title('Build Management');

$input = $main->obj('input');
$alte  = $main->obj('adminlte');

$main->fetchPlayerList();
$main->fetchPlayerBuildList();
$main->fetchPlayerGearList();

$player    = $input->get('player','alphanumeric,dot,dash,underscore,space');
$build     = $input->get('build','alphanumeric,dot,dash,underscore,space');
$buildName = $input->get('build-name','alphanumeric,dot,dash,underscore,space,forwardslash');
$load      = ($input->isDefined('load')) ? true : false;
$save      = ($input->isDefined('save')) ? true : false;


$sessionInput = $main->sessionValue('playerbuild/pageInput') ?: array();
$pageInput    = processInput($sessionInput);

if ($load && $build) {
   $playerBuildList = $main->var('playerBuildList');
  
   if (array_key_exists($build,$playerBuildList)) { 
      $buildData = $playerBuildList[$build];
      $pageInput = json_decode($buildData['build'],true);

      $buildName = $buildData['name'];
      $player    = $buildData['player_id'];

      $pageInput['build-name'] = $buildName;
      $pageInput['player']     = $player;
   }
}

$main->sessionValue('playerbuild/pageInput',array_diff_key($pageInput,array_flip(array('save','load'))));

if ($save) {
   if (!$player || !$buildName) { $main->error("Player and Build Name are required."); }
   else {
      $buildData = array();

      foreach ($main->obj('constants')->gearTypes() as $gearType => $gearTypeLabel) {
         $buildData[$gearType] = $pageInput[$gearType];
      }
      $buildData['runes'] = $pageInput['runes'];

      $rc = $main->db()->bindExecute("insert into player_build (name,player_id,build,created,updated) values (?,?,?,now(),now()) ".
                                     "on duplicate key update build = values(build), updated = values(updated)",
                                     "sis",array(array($buildName,$player,json_encode($buildData,JSON_UNESCAPED_SLASHES))));

      if (!$rc) { $this->error($main->db()->error()); }
      else { 
         $build = $main->db()->insertId();
         $main->fetchPlayerBuildList(); 
      }
   }
}

$main->var('player',$player);
$main->var('build',$build);
$main->var('buildName',$buildName);
$main->var('pageInput',$pageInput);

$errors = $main->error();

include 'ui/header.php';

if ($errors) { print $alte->displayRow($alte->errorCard($errors)); }

print pageDisplay($main);

include 'ui/footer.php';

?>
<?php

function pageDisplay($main)
{
   if (!$main->var('playerList')) { $return = "No players found, add them in <a href='/profile/player'>Player Management</a>"; }
   else {
      $return = $main->obj('html')->startForm(array('method' => 'post')).
                playerBuildSelect($main).
                $main->obj('html')->endForm().
                $main->obj('html')->startForm(array('method' => 'post')).
                gearDisplay($main).
                runeDisplay($main).
                playerSelect($main).
                $main->obj('html')->endForm().
                "<script src='/assets/js/equipmentmanager.js?t=".$main->now."' type='text/javascript'></script>\n".
                "<script src='/assets/js/runeselect.js?t=".$main->now."' type='text/javascript'></script>\n".
                "<link rel='stylesheet' href='/assets/css/simulation.css?t=".$main->now."'>\n";

      $return .= "<script>\n".
                 "  updateRunes('playerbuild');\n".
                 "</script>\n";

   }

   return $return;
}

function playerDisplay($main)
{
   $db         = $main->db();
   $html       = $main->obj('html');
   $userId     = $main->userId;
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

   $main->fetchPlayerGearList();

   $gearList = array();

   foreach ($main->var('playerGearList') as $gearId => $gearInfo) {
      $gearList[$gearInfo['type']][$gearId] = $gearInfo;
   }

   foreach ($constants->gearTypes() as $gearType => $gearTypeLabel) {
      $typeList   = array('' => "Select $gearTypeLabel", 'none' => 'NONE');
      $selectOpts = array('id' => $gearType, 'class' => 'form-control gear', 'style' => 'width:800px;', 'script' => "onChange='updateRunes(&quot;playerbuild&quot;)'");

      $gearTypeList = $gearList[$gearType] ?: array();

      foreach ($gearTypeList as $gearId => $gearInfo) {
         $gearHash  = $main->hashPlayerGearId($gearId);
         $gearStats = json_decode($gearInfo['stats'],true);

         $typeList[$gearHash] = $gearInfo['label'];

         $gearItemStats = formatGearStats($main,$gearStats);

         $selectOpts['data'][$gearHash]['raw'] = sprintf("<img src=&quot;%s&quot; width=25px height=25px> %s : [ %s ]",$gearInfo['image'],str_replace("'",'&apos;',$gearInfo['label']),$gearItemStats);
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


function playerSelect($main)
{
   $html           = $main->obj('html');
   $alte           = $main->obj('adminlte');
   $selectedPlayer = $main->var('player');
   $buildName      = $main->var('buildName');
   $playerList     = $main->var('playerList') ?: array();

   $selectPlayers = array();
   foreach ($playerList as $playerName => $playerInfo) { $selectPlayers[$playerInfo['id']] = $playerName; }

   $selectContent = "<div class='input-group'>".
                    $html->select('player',$selectPlayers).
                    $html->inputText('build-name',$buildName,null,null,array('required' => true, 'placeholder' => 'Build Name')).
                    "<span class='input-group-append'>".
                    $html->submit('save','Save',array('class' => 'btn btn-primary btn-sm')).
                    "</span></div>";

   return $alte->displayRow($alte->displayCard($selectContent,array('title' => 'Create Build', 'container' => 'col-12 col-sm-12 col-md-12 col-lg-6 col-xl-6 mb-3', 'card' => 'card-primary')));
}

function playerBuildSelect($main)
{
   $html            = $main->obj('html');
   $alte            = $main->obj('adminlte');
   $selectedBuild   = $main->var('build');
   $playerBuildList = $main->var('playerBuildList') ?: array();

   $buildList = array();
   foreach ($playerBuildList as $playerBuildId => $playerBuildInfo) { $buildList[$playerBuildId] = sprintf("%s (%s)",$playerBuildInfo['player_name'],$playerBuildInfo['name']); }

   $selectContent = "<div class='input-group'>".
                    $html->select('build',$buildList,$selectedBuild).
                    "<span class='input-group-append'>".
                    $html->submit('load','Load',array('class' => 'btn btn-warning btn-sm')).
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
