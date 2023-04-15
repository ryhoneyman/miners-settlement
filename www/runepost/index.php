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
));

$main->title('Runeposts');

$input = $main->obj('input');
$html  = $main->obj('html');

$main->buildClass('constants','Constants',null,'local/constants.class.php');
$main->buildClass('format','Format',null,'local/format.class.php');

include 'ui/header.php';

print dataDisplay($main);

include 'ui/footer.php';

?>
<?php

function dataDisplay($main)
{
   $return = '';

   $main->var('runewordList',getRunewords($main));
   $main->var('runeList',getRunes($main));
   $main->var('gearInfo',getSupportingGearInfo($main));

   foreach ($main->var('runewordList') as $areaName => $areaPosts) {
      $return .= areaDisplay($main,$areaName,$areaPosts);
   }

   return $return;
}

function areaDisplay($main, $areaName, $areaPosts)
{
   $return = '<div class="row">'.
             '<div class="col-12 col-xl-9 col-lg-9 col-md-10 col-sm-12">'.
             '<div class="card card-outline card-success">'.
             '<div class="card-header"><b class="text-xl">'.$areaName.'</b></div>'.
             '<div class="card-body">'.
             '<div class="row">';

   foreach ($areaPosts as $postName => $postInfo) {
      $return .= runepostDisplay($main,$postName,$postInfo);
   }

   $return .= '</div>'.
              '</div>'.
              '</div>'.
              '</div>'.
              '</div>';

   return $return;
}

function runepostDisplay($main, $postName, $postInfo)
{
   $return = '<div class="col-12">'.
             '<div class="card card-outline card-warning">'.
             '<div class="card-header"><b class="text-lg">'.$postName.'</b></div>'.
             '<div class="card-body">'.
             '<table border=0 cellpadding=10>'.
             '<tr class="text-yellow"><th>Name</th><th>Gear</th><th>Effects</th><th>Required Runes</th></tr>';

   $runeList = $main->var('runeList');
   $gearInfo = $main->var('gearInfo');

   foreach ($postInfo as $entryId => $entryInfo) {
      $entryCost     = json_decode($entryInfo['cost'],true);
      $entryAttrib   = json_decode($entryInfo['attributes'],true);
      $itemLabel     = $gearInfo[$entryInfo['item_id']]['label'] ?: 'None';
      $itemImage     = $gearInfo[$entryInfo['item_id']]['image'];
      $itemInsert    = ($itemImage) ? sprintf("<img src='%s' height=50 data-toggle='tooltip' title='%s'>",$itemImage,$itemLabel) : $itemLabel;
      $requiredRunes = array();
      $runeEffects   = '';

      if ($entryCost['item']) { 
         foreach ($entryCost['item'] as $itemName => $itemCount) { 
            $runeLabel  = $runeList[$itemName]['label'];
            $runeImage  = $runeList[$itemName]['image'];
            $runeInsert = ($runeImage) ? sprintf("<img src='%s' height=25 data-toggle='tooltip' title='%s'>",$runeImage,$runeLabel) : $runeLabel;
 
            for ($count = 0; $count < $itemCount; $count++) { $requiredRunes[] = $runeInsert; } 
         }
      }
 
      if ($entryAttrib) {
         $runeEffects = '<code style="color:#aaaaaa;">'.$main->obj('format')->effects($entryAttrib,true).'</code>'; 
      }

      $return .= sprintf("<tr><td>%s</td><td>%s</td><td>%s</td><td>%s</td></tr>",$entryInfo['label'],$itemInsert,$runeEffects,implode('',$requiredRunes));
   }

   $return .= '</table>'.
              '</div>'.
              '</div>'.
              '</div>';

   return $return;
}

function getSupportingGearInfo($main)
{
   $runewordList = $main->var('runewordList');

   if (!$runewordList) { return array(); }

   $gearIds = array();

   foreach ($runewordList as $areaName => $areaPosts) {
      foreach ($areaPosts as $postName => $postInfo) {
         foreach ($postInfo as $runewordId => $runewordInfo) {
            $gearId = $runewordInfo['item_id'] ?: null;
            if (!is_null($gearId)) { $gearIds[$gearId]++; }
         }
      }
   }

   if (!$gearIds) { return array();; }

   $itemList = $main->db()->query("select * from item where id in (".implode(',',array_keys($gearIds)).")");

   return $itemList;
}

function getRunes($main)
{
   $runeList = $main->db()->query("select * from item where type = 'rune' and active = 1",array('keyid' => 'name'));

   return $runeList; 
}

function getRunewords($main)
{
   $runewordList = array();

   $results = $main->db()->query("select rw.*, rp.label as runepost_label, l.area as location_area, l.section as location_section ".
                                 "from runeword rw, runepost rp, location l ".
                                 "where rw.runepost_id = rp.id and rp.location_id = l.id and rw.active = 1 and rp.active = 1 and l.active = 1");

   foreach ($results as $resultId => $resultInfo) {
      $areaName    = $resultInfo['location_area'];
      $sectionName = $resultInfo['location_section'];
      $postLabel   = $resultInfo['runepost_label'];

      $runewordList[$areaName][$postLabel][$resultId] = $resultInfo;
   }    

   return $runewordList;
}

?>
