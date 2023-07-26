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
   'format'         => true,
));

$main->title('Item Enhancement');

$input = $main->obj('input');
$html  = $main->obj('html');

$enhanceCosts = array(
   '1'  => array('coin' => 1, 'sacred-soul-stone' => 5, 'material' => array('rat-claw' => 5), 'shard' => 25, 'chance' => 20),
   '2'  => array('coin' => 2, 'sacred-soul-stone' => 8, 'material' => array('slime-drop' => 3), 'shard' => 30, 'chance' => 15),
   '3'  => array('coin' => 3, 'sacred-soul-stone' => 10, 'material' => array('bone-dust' => 5), 'shard' => 40, 'chance' => 12),
   '4'  => array('coin' => 5, 'sacred-soul-stone' => 15, 'material' => array('tanzanite-essence' => 2), 'shard' => 50, 'chance' => 10),
   '5'  => array('coin' => 8, 'sacred-soul-stone' => 20, 'material' => array('emerald-essence' => 2), 'shard' => 50, 'chance' => 5),
   '6'  => array('coin' => 10, 'sacred-soul-stone' => 25, 'material' => array('ruby-essence' => 2), 'shard' => 50, 'chance' => 3),
   '7'  => array('coin' => 10, 'sacred-soul-stone' => 25, 'material' => array('jade-essence' => 2), 'shard' => 50, 'chance' => 1),
   '8'  => array('coin' => 12, 'sacred-soul-stone' => 30, 'material' => array('demonic-matter' => 5), 'shard' => 75, 'chance' => 0.5),
   '9'  => array('coin' => 12, 'sacred-soul-stone' => 30, 'material' => array('magic-dust' => 1), 'shard' => 75, 'chance' => 0.4),
   '10' => array('coin' => 15, 'sacred-soul-stone' => 40, 'material' => array('magic-dust' => 2), 'shard' => 100, 'chance' => 0.3),
   '11' => array('coin' => 50, 'sacred-soul-stone' => 250, 'material' => array('rune-vii' => 3), 'shard' => 500, 'chance' => 0.2),
   '12' => array('coin' => 100, 'sacred-soul-stone' => 500, 'material' => array('rune-viii' => 3), 'shard' => 1000, 'chance' => 0.1),
   '13' => array('coin' => 250, 'sacred-soul-stone' => 1250, 'material' => array('rune-ix' => 3), 'shard' => 2000, 'chance' => 0.08),
   '14' => array('coin' => 0, 'sacred-soul-stone' => 2500, 'material' => array('rune-x' => 1, 'rune-xi' => 1, 'rune-xii' => 1), 'shard' => 0, 'chance' => 0.05),
);

$runeBoosts = array(
   'rune-i'    => 10,
   'rune-ii'   => 25,
   'rune-iii'  => 50,
   'rune-iv'   => 100,
   'rune-v'    => 175,
   'rune-vi'   => 250,
   'rune-vii'  => 300,
   'rune-viii' => 400,
   'rune-ix'   => 500,
   'rune-x'    => 800,
   'rune-xi'   => 1000,
   'rune-xii'  => 1200,
);

$main->var('enhanceCosts',$enhanceCosts);
$main->var('runeBoosts',$runeBoosts);

include 'ui/header.php';

print pageDisplay($main);

include 'ui/footer.php';

?>
<?php

function pageDisplay($main)
{
   $return = '<div class="row">'.
             overviewDisplay($main).
             '</div>'.
             '<div class="row">'.
             enhanceDisplay($main).
             boostDisplay($main).
             '</div>';

   return $return;
}

function boostDisplay($main)
{
   $return = '<div class="col-12 col-xl-3 col-lg-4 col-md-4 col-sm-12">'.
             '<div class="card card-outline card-success">'.
             '<div class="card-header"><b class="text-xl">Rune Boosts</b></div>'.
             '<div class="card-body">'.
             '<table class="table table-striped table-hover" style="width:auto;" border=0 cellpadding=10>'.
             '<thead><tr class="text-yellow"><th>Rune</th><th>Increase Success</th></thead><tbody>';

   $runeBoosts = $main->var('runeBoosts');

   $itemList = array();
   foreach ($runeBoosts as $runeName => $increaseChance) { $itemList[] = "'$runeName'"; }

   $itemResults = $main->db()->query("select name,label,image from item where name in (".implode(',',$itemList).")");
   $imageFormat = "<img src='%s' height=35 data-toggle='tooltip' title='%s'>";

   foreach ($runeBoosts as $runeName => $increaseChance) {
      $runeImage       = sprintf($imageFormat,$itemResults[$runeName]['image'],$itemResults[$runeName]['label']);
      $increaseDisplay = sprintf("<span class='text-green text-lg text-bold'>%s%%</span>",$increaseChance);

      $return .= sprintf("<tr><td>%s</td><td>%s</td></tr>",$runeImage,$increaseDisplay);
   }

   $return .= '</tbody></table>'.
              '</div>'.
              '</div>'.
              '</div>';

   return $return;
}

function enhanceDisplay($main)
{
   $format = $main->obj('format');

   $return = '<div class="col-12 col-xl-6 col-lg-8 col-md-8 col-sm-12">'.
             '<div class="card card-outline card-success">'.
             '<div class="card-header"><b class="text-xl">Enhancement Cost</b></div>'.
             '<div class="card-body">'.
             '<table class="table table-striped table-hover" style="width:auto;" border=0 cellpadding=10>'.
             '<thead><tr class="text-yellow"><th>Level</th><th>Coins</th><th>Soul Stones</th><th>Shards</th><th>Material</th><th>Base Success</th></thead><tbody>';

   $enhanceCosts = $main->var('enhanceCosts');

   $itemList = array("'trade-coin'","'sacred-soul-stone'","'shard'");
   foreach ($enhanceCosts as $enhanceLevel => $enhanceInfo) { 
      if (is_array($enhanceInfo['material'])) { 
         foreach ($enhanceInfo['material'] as $materialName => $materialCount) {
            $itemList[] = "'$materialName'"; 
         }
      }
   }

   $itemResults = $main->db()->query("select name,label,image from item where name in (".implode(',',$itemList).")");
   $imageFormat = "<img src='%s' height=20 data-toggle='tooltip' title='%s'>";
   $countFormat = "<span class='text-%s'>x %s</span>";

   $coinImage      = sprintf($imageFormat,$itemResults['trade-coin']['image'],$itemResults['trade-coin']['label']);
   $soulstoneImage = sprintf($imageFormat,$itemResults['sacred-soul-stone']['image'],$itemResults['sacred-soul-stone']['label']);
   $shardImage     = sprintf($imageFormat,$itemResults['shard']['image'],$itemResults['shard']['label']);

   foreach ($enhanceCosts as $enhanceLevel => $enhanceInfo) {
      $materialName   = key($enhanceInfo['material']);
      $materialCount  = reset($enhanceInfo['material']);
      $coinCount      = $enhanceInfo['coin'];
      $soulstoneCount = $enhanceInfo['sacred-soul-stone'];
      $shardCount     = $enhanceInfo['shard'];

      $materialList = array();
      foreach ($enhanceInfo['material'] as $materialName => $materialCount) {
         $materialResult = $itemResults[$materialName];
         $materialList[] = ($materialResult['image']) ? sprintf("<img src='%s' height=35 data-toggle='tooltip' title='%s'> %s",
                                                                $materialResult['image'],$materialResult['label'],sprintf($countFormat,'white',$format->numericReducer($materialCount))) : '';
      }

      var_dump($materialList);

      if (!$soulstoneCount) { continue; }

      $levelDisplay     = "<span class='text-lg text-bold'>+".$enhanceLevel."<span>";
      $coinDisplay      = "$coinImage ".sprintf($countFormat,'white',$format->numericReducer($coinCount));
      $soulstoneDisplay = "$soulstoneImage ".sprintf($countFormat,'white',$format->numericReducer($soulstoneCount));
      $shardDisplay     = "$shardImage ".sprintf($countFormat,'white',$format->numericReducer($shardCount));
      $materialDisplay  = implode('<br>',$materialList);
      $successDisplay   = sprintf("<span class='text-green text-lg text-bold'>%s%%</span>",$enhanceInfo['chance']);

      $return .= sprintf("<tr><td>%s</td><td>%s</td><td class='text-center'>%s</td><td>%s</td><td>%s</td><td class='text-right'>%s</td></tr>",
                         $levelDisplay,$coinDisplay,$soulstoneDisplay,$shardDisplay,$materialDisplay,$successDisplay);
   }

   $return .= '</tbody></table>'.
              '</div>'.
              '</div>'.
              '</div>';

   return $return;
}

function overviewDisplay($main)
{
   $return = '<div class="col-12 col-xl-6 col-lg-8 col-md-8 col-sm-12">'.
             '<div class="card card-outline card-primary">'.
             '<div class="card-header"><b class="text-xl">Overview</b></div>'.
             '<div class="card-body">';

   $return .= "<div class='text-blue text-bold text-lg'>Enhancement table on Emerald level of the Stone Mines unlocks after you complete the quest to create The Great Council Portal scroll. ".
              "Gear can be enhanced up to 15 times.</div>".
              "<div class='mt-3'>Enhancing gear increases its stats by a fixed percent of the base value per level:".
              "<ul>".
              "<li><span class='text-yellow'>+10%</span> Health, Elemental Damage, Elemental Resist <span class='text-green'>[Divide enhanced value by (1 + (LEVEL/10)) to find base]</span></li>".
              "<li><span class='text-yellow'>+5%</span> Attack, Defense <span class='text-green'>[Divide enhanced value by (1 + (LEVEL/20)) to find base]</span></li>".
              "<li><span class='text-red'>There is never an increase for Speed</span></li>".
              "</ul>".
              "</div>".
              "<div class='mt-3'>".
              "On success or failure, all trade coins, soul stones, shards, and materials are consumed (along with any rune used to boost). ".
              "<span class='text-red'>Failure does not cause loss of gear.</span>".
              "</div>";

   $return .= '</div>'.
              '</div>'.
              '</div>';

   return $return;
}

function getCrafting($main)
{
   $result     = $main->db()->query("select isc.id,isc.*,i.* from item_scheme isc, item i where isc.item_id = i.id and isc.active = 1 and i.active = 1",array('keyid' => 'id'));
   $components = array();
   $return     = array();

   foreach ($result as $entryId => $entryInfo) {
      $entryCost = json_decode($entryInfo['cost'],true);

      // We need to get the info (label, image) for the components that make up the scheme
      if (is_array($entryCost['item'])) {
         foreach ($entryCost['item'] as $componentName => $componentCount) { $components[$componentName]++; }
      }

      $return['scheme'][$entryId] = $entryInfo;
   }

   if ($components) {
      $nameList = implode(',',array_map(function($value) { return "'".preg_replace('/[^\w\-]/','',$value)."'"; },
                              array_unique(array_filter(array_keys($components)))));

      $result = $main->db()->query("select i.name,i.label,i.image from item i where i.name in ($nameList)");

      $return['component'] = $result;
   }

   return $return; 
}

?>
