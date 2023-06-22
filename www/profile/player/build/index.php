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

$main->var('sessionName','playerbuild/pageInput');

$playerHash = $input->get('player','alphanumeric,dot,dash,underscore,space');
$buildHash  = $input->get('build','alphanumeric,dot,dash,underscore,space');
$buildName  = $input->get('build-name','alphanumeric,dot,dash,underscore,space,forwardslash');
$load       = ($input->isDefined('load')) ? true : false;
$delete     = ($input->isDefined('delete')) ? true : false;
$save       = ($input->isDefined('save')) ? true : false;
$clear      = ($input->isDefined('clear')) ? true : false;

$sessionInput = $main->sessionValue($main->var('sessionName')) ?: array();
$pageInput    = processInput($sessionInput);

if (($load || $delete) && $buildHash) {
   $playerBuildList     = $main->getPlayerBuildList(); 
   $playerBuildHashList = $main->getPlayerBuildHashList();
   $buildId             = $playerBuildHashList[$buildHash] ?: null;
  
   if ($buildId) { 
      if ($load) {
         $buildData = $playerBuildList[$buildId];
         $pageInput = json_decode($buildData['build'],true);

         $buildName  = $buildData['name'];
         $playerHash = $main->hashPlayerId($buildData['player_id']);

         $pageInput['build-name'] = $buildName;
         $pageInput['player']     = $playerHash;
      }
      else if ($delete) {
         $rc = $main->db()->bindExecute('delete from player_build where id = ?','s',array($buildId));
         if (!$rc) { $this->error($main->db()->error()); }
      }
   }
}

$main->sessionValue($main->var('sessionName'),array_diff_key($pageInput,array_flip(array('save','load'))));

if ($save) {
   if (!$playerHash || !$buildName) { $main->error("Player and Build Name are required."); }
   else {
      $buildData = array();

      foreach ($main->obj('constants')->gearTypes() as $gearType => $gearTypeLabel) {
         $buildData[$gearType] = $pageInput[$gearType];
      }

      $buildData['runes'] = $pageInput['runes'];

      $playerHashList = $main->getPlayerHashList();
      $playerId       = $playerHashList[$playerHash];
      
      $rc = $main->db()->bindExecute("insert into player_build (name,player_id,build,created,updated) values (?,?,?,now(),now()) ".
                                     "on duplicate key update build = values(build), updated = values(updated)",
                                     "sis",array(array($buildName,$playerId,json_encode($buildData,JSON_UNESCAPED_SLASHES))));

      if (!$rc) { $this->error($main->db()->error()); }
      else { 
         $buildId   = $main->db()->insertId();
         $buildHash = $main->hashPlayerBuildId($buildId);
      }
   }
}
else if ($clear) {
   $main->sessionValue($main->var('sessionName'),null,true);
   $playerHash = null;
   $buildHash  = null;
   $buildName  = null;
   $pageInput  = array();
}

$main->var('player',$playerHash);
$main->var('build',$buildHash);
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
   $html = $main->obj('html');

   if (!$main->getPlayerList()) { $return = "No players found, add them in <a href='/profile/player'>Player Management</a>"; }
   else {
      $return = $html->startForm(array('method' => 'post')).
                playerBuildSelect($main).
                $html->endForm().
                $html->startForm(array('method' => 'post')).
                gearDisplay($main).
                runeDisplay($main).
                playerSelect($main).
                $html->endForm().
                "<script src='/assets/js/buildmanager.js?t=".$main->now."' type='text/javascript'></script>\n".
                "<script src='/assets/js/runeselect.js?t=".$main->now."' type='text/javascript'></script>\n".
                "<link rel='stylesheet' href='/assets/css/simulation.css?t=".$main->now."'>\n";

      $return .= "<script>\n".
                 "  updateRunes('playerbuild');\n".
                 "</script>\n";

   }

   return $return;
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

   $gearList = $main->getPlayerGearListByType();

   foreach ($constants->gearTypes() as $gearType => $gearTypeLabel) {
      $typeList   = array('' => "Select $gearTypeLabel", 'none' => 'NONE');
      $selectOpts = array('id' => $gearType, 'class' => 'form-control gear', 'style' => 'width:800px;', 'script' => "onChange='updateRunes(&quot;playerbuild&quot;)'");

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


function playerSelect($main)
{
   $html           = $main->obj('html');
   $alte           = $main->obj('adminlte');
   $selectedPlayer = $main->var('player');
   $buildName      = $main->var('buildName');
   $playerList     = $main->getPlayerList() ?: array();

   $selectPlayers = array();
   foreach ($playerList as $playerId => $playerInfo) { $selectPlayers[$main->hashPlayerId($playerId)] = $playerInfo['name']; }

   $selectContent = "<div class='input-group'>".
                    $html->select('player',$selectPlayers,$selectedPlayer).
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
   $playerBuildList = $main->getPlayerBuildList(array('skipCache' => true)) ?: array();

   $buildList = array();
   foreach ($playerBuildList as $playerBuildId => $playerBuildInfo) { 
      $playerBuildHash = $main->hashPlayerBuildId($playerBuildId);
      $buildList[$playerBuildHash] = sprintf("%s (%s)",$playerBuildInfo['player_name'],$playerBuildInfo['name']); 
   }

   $selectContent = "<div class='input-group'>".
                    $html->select('build',$buildList,$selectedBuild).
                    "<span class='input-group-append'>".
                    $html->submit('load','Load',array('class' => 'btn btn-warning btn-sm')).
                    $html->submit('delete','Delete',array('class' => 'btn btn-danger btn-sm')).
                    $html->submit('clear','Clear',array('class' => 'btn btn-success btn-sm')).
                    "</span></div>";

   return $alte->displayRow($alte->displayCard($selectContent,array('title' => 'Player Builds', 'container' => 'col-12 col-sm-12 col-md-12 col-lg-6 col-xl-6 mb-3', 'card' => 'card-secondary')));
}

function processInput($sessionInput)
{
   $postInput = $_POST;

   // Empty runes are not passed to us by the browser, so we initialize them to empty
   if (!array_key_exists('runes',$postInput) && ($postInput['save'] || $postInput['clear'])) { $postInput['runes'] = array(); }

   $return = array_merge($sessionInput,$postInput);

   return $return;
}

?>
