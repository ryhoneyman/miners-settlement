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

$main->buildClass('constants','Constants',null,'local/constants.class.php');
$main->buildClass('format','Format',null,'local/format.class.php');

$main->title('Monster Viewer');

$input = $main->obj('input');
$html  = $main->obj('html');
$alte  = $main->obj('adminlte');

include 'ui/header.php';

print monsterDisplay($main);

include 'ui/footer.php';

?>
<?php

function monsterDisplay($main)
{
   $html   = $main->obj('html');
   $input  = $main->obj('input');
   $format = $main->obj('format');
   $alte   = $main->obj('adminlte');

   $selectedId    = null;
   $selectedHash  = $input->get('monster','alphanumeric,dash');
   $monsterList   = $main->getMonsterList();
   $monsterSelect = array('' => "Select Monster");

   foreach ($monsterList as $monsterId => $monsterInfo) {
      $monsterHash = $main->hashMonsterId($monsterId);
      $monsterName = $monsterInfo['name'];
      $monsterArea = $monsterInfo['area'] ?: 'General';
      $monsterSelect[$monsterArea][$monsterHash] = $monsterInfo['label'];

      if ($monsterHash == $selectedHash) { $selectedId = $monsterId; }
   }

   $selectContents = $html->select('monster',$monsterSelect,$selectedHash,array('style' => 'width:300px;')).
                     $html->submitButton('select','','Go');

   $monsterContents = '';

   if ($selectedId) {
      $monsterData    = $monsterList[$selectedId];
      $monsterImage   = ($monsterData['image']) ? sprintf("<img src='%s' width=300px>",$monsterData['image']) : '';
      $monsterXp      = $monsterData['xp'];
      $monsterAttribs = json_decode($monsterData['attributes'],true);

      $monsterContents .= "<div class='d-inline-block'>".
                          $format->monsterStatsDisplay($monsterData).
                          "</div>";

      if ($monsterImage) {
         $monsterContents .= "<div class='mr-4 mt-4 d-inline-block align-top'><table border=0 style='border-collapse:separate;'>".
                             "<tr><td class='text-center' style='width:300px;'>$monsterImage</td></tr>".
                             "</table></div>";
      }

      if ($monsterXp) {
         $monsterContents .= "<div class='mt-4 d-inline-block align-top'><table border=0 style='border-collapse:separate;'>".
                             "<tr><td class='text-center' style='width:300px;'><span class='text-bold' style='font-size:1em; color:#00aaaa;'>XP</span></td></tr>".
                             "<tr><td class='text-center' style='width:300px; border:3px solid #00aaaa; border-radius:15px;'><img src='/images/game/xp.png' width=35px;>".
                             "<br><span class='text-bold text-sm'>$monsterXp</span></td></tr>".
                             "</table></div>";
      }
   }

   return $html->startForm().
          $alte->displayRow($alte->displayCard($selectContents,array('title' => 'Monster List', 'container' => 'col-12 col-xl-6 col-lg-6 col-md-9 col-sm-12'))).
          $alte->displayRow($alte->displayCard($monsterContents,array('title' => 'Monster Stats', 'container' => 'col-12 col-xl-12 col-lg-12 col-md-12 col-sm-12'))).
          $html->endForm().
          "<script src='/assets/js/monsterviewer.js?t=".$main->now."' type='text/javascript'></script>\n".
          "<link rel='stylesheet' href='/assets/css/monsterviewer.css?t=".$main->now."'>\n".
          "<link rel='stylesheet' href='/assets/css/monsterdisplay.css?t=".$main->now."'>\n";
}

?>
