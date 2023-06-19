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

$input = $main->obj('input');
$html  = $main->obj('html');

$main->buildClass('constants','Constants',null,'local/constants.class.php');

print pageDisplay($main);

?>
<?php

function pageDisplay($main)
{
   $html      = $main->obj('html');
   $alte      = $main->obj('adminlte');
   $attribs   = $main->obj('constants')->attribs();
   $caller    = strtolower($main->obj('input')->get('caller','alphanumeric,underscore,dash,dot,forwardslash'));
   $gearState = json_decode($main->obj('input')->get('state','all'),true);

   if (!$gearState || !$caller) { return ''; }
 
   $runeInput  = array();
   $hashLookup = array();

   if ($caller == 'scalable') {
      $runeInput = ($main->sessionValue('simulation/pageInput'))['runes'] ?: array();
      $hashLookup = $main->getGearHashList();
   }
   else if ($caller == 'playerbuild') {
      $runeInput = ($main->sessionValue('playerbuild/pageInput'))['runes'] ?: array();
      $hashLookup = $main->getPlayerGearHashList();
   }

   $main->fetchRunewordList();

   $runeList     = array();
   $gearRuneList = array('' => 'General');
   $runewordList = $main->var('runewordList');

   foreach ($main->obj('constants')->gearTypes() as $gearType => $gearTypeLabel) {
      if (!preg_match('/^(none|)$/i',$gearState[$gearType])) { $gearRuneList[$gearState[$gearType]] = $gearTypeLabel; }
   }

   foreach ($gearRuneList as $gearHash => $gearTypeLabel) {
      $gearName  = $hashLookup[$gearHash];
      $gearRunes = $runewordList[$gearName];

      if (!$gearRunes) { continue; }

      foreach ($gearRunes as $runeId => $runeInfo) { $runeList[$gearTypeLabel][$runeInfo['name']] = $runeInfo['label']; }
   }

   $selectOpts = array('id' => 'runes', 'class' => 'form-control runes', 'multi' => true);

   return $html->select('runes',$runeList,$runeInput,$selectOpts);
}

?>
