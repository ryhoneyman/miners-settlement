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
   $pageInput = $main->sessionValue('simulation/pageInput');
   $gearState = json_decode($main->obj('input')->get('state','all'),true);

   if (!$gearState) { return ''; }

   $main->fetchRunewordList();

   $runewordList = $main->var('runewordList');
   $runeList     = array('' => "Select Runes");

   $gearList = array('' => 'General');

   foreach ($main->obj('constants')->gearTypes() as $gearType => $gearTypeLabel) {
      if ($gearState[$gearType]) { $gearList[$gearState[$gearType]] = $gearTypeLabel; }
   }

   foreach ($gearList as $gearName => $gearTypeLabel) {
      $gearRunes = $runewordList[$gearName];

      if (!$gearRunes) { continue; }

      foreach ($gearRunes as $runeId => $runeInfo) { $runeList[$gearTypeLabel][$runeInfo['name']] = $runeInfo['label']; }
   }

   $selectOpts = array('id' => 'runes', 'class' => 'form-control runes', 'multi' => true);

   return $html->select('runes',$runeList,$pageInput['runes'],$selectOpts);
}

?>