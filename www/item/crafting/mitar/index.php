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

$main->buildClass('constants','Constants',null,'local/constants.class.php');

$main->title('Mitar Forge Crafting');

$input = $main->obj('input');
$html  = $main->obj('html');

include 'ui/header.php';

print dataDisplay($main);

include 'ui/footer.php';

?>
<?php

function dataDisplay($main)
{
   $return = '';

   $main->var('craftingList',getCrafting($main));

   $return .= craftingDisplay($main);

   return $return;
}

function craftEntry($craftList = null)
{
   $tdSize        = 75;
   $tdItemFormat  = "<td width={$tdSize}px height={$tdSize}px style='background-image:url(\"/images/craft-border.png\"); background-size:contain; background-repeat:no-repeat; ".
                    "text-align:center; vertical-align:middle;'><img src='%s' width=".($tdSize-12)."px data-toggle='tooltip' title='%s'></td>";
   $tdCountFormat = "<td width=50px style='text-align:center; font-weight:bold;'>%s</a>";

   $return = '';

   $maxRequires = null;

   foreach ($craftList as $craftData) { 
      $requiredCount = count($craftData['requires']);
      if ($requiredCount > $maxRequires) { $maxRequires = $requiredCount; }
   }

   foreach ($craftList as $craftData) {
      $return .= "<tr>";

      $itemCount = count($craftData['requires']); 

      for ($gap = 0; $gap < ($maxRequires - $itemCount); $gap++) { $return .= "<td></td>"; }

      foreach ($craftData['requires'] as $itemLabel => $itemData) {
         $itemImage = $itemData['image'];
         $return .= sprintf($tdItemFormat,$itemImage,$itemLabel);
      }

      $return .= "<td style='font-weight:bold; font-size:30px; color:#e1c675; text-align:center;'>&#10142;</td>";

      foreach ($craftData['item'] as $itemLabel => $itemData) {
         $itemImage = $itemData['image'];
         $return .= sprintf($tdItemFormat,$itemImage,$itemLabel);
      }

      $return .= "</tr>".
                 "<tr>";

      for ($gap = 0; $gap < ($maxRequires - $itemCount); $gap++) { $return .= "<td></td>"; }

      foreach ($craftData['requires'] as $itemLabel => $itemData) {
         $itemCount = $itemData['count'];
         $return .= sprintf($tdCountFormat,$itemCount ?: '');
      }

      foreach ($craftData['item'] as $itemLabel => $itemData) {
         $itemCount = $itemData['count'];
         $return .= sprintf($tdCountFormat,$itemCount ?: '');
      }


      $return .= "</tr>".
                 "<tr><td colspan=7 style='background-color:#222222; text-align:left;'>".$craftData['limit']."</td></tr>".
                 "<tr><td colspan=7 height=25px></td></tr>";
   }

   return $return;
}

function craftingDisplay($main)
{
   $format = $main->obj('format');

   $craftList = array(
      array(
         'limit' => 'UNLIMITED',
         'item' => array(
            'SWORD OF A THOUSAND DICKS' => array('image' => '/images/item/stormbreaker-blade.png'),
         ),
         'requires' => array(
            'SACRED SOUL STONE' => array('count' => '10K', 'image' => '/images/item/sacred-soul-stone.png'),
            'DEPTH OF MITAR' => array('image' => '/images/item/necromancer-teeth.png'),
            'EYES OF MITAR' => array('image' => '/images/item/spiders-claw.png'),
            'HEART OF MITAR' => array('image' => '/images/item/fire-tongue.png'),
         ),
      ),
      array(
         'limit' => 'UNLIMITED',
         'item' => array(
            'SWORD OF A THOUSAND DICKS' => array('image' => '/images/item/stormbreaker-blade.png'),
         ),
         'requires' => array(
            'SACRED SOUL STONE' => array('image' => '/images/item/spear-of-the-gods.png'),
            'JEWEL OF MITAR' => array('image' => '/images/item/minotaurs-pride.png'),
            'DEPTH OF MITAR' => array('image' => '/images/item/necromancer-teeth.png'),
            'EYES OF MITAR' => array('image' => '/images/item/spiders-claw.png'),
            'HEART OF MITAR' => array('image' => '/images/item/fire-tongue.png'),
         ),
      ),
      array(
         'limit' => 'UNLIMITED',
         'item' => array(
            'POWER OF MITAR' => array('image' => '/images/item/power-of-mitar.png'),
         ),
         'requires' => array(
            'SACRED SOUL STONE' => array('count' => '10K', 'image' => '/images/item/sacred-soul-stone.png'),
            'JEWEL OF MITAR' => array('image' => 'https://funventure.eu/wiki/minerssettlement/images/c/c3/JewelofMitar.png'),
            'DEPTH OF MITAR' => array('image' => 'https://funventure.eu/wiki/minerssettlement/images/2/2f/DepthOfMitar.png'),
            'EYES OF MITAR' => array('image' => 'https://funventure.eu/wiki/minerssettlement/images/0/0c/EyesOfMitar.png'),
            'HEART OF MITAR' => array('image' => 'https://funventure.eu/wiki/minerssettlement/images/9/98/HeartofMitar.png'),
         ), 
      ),
      array(
         'limit' => 'UNLIMITED',
         'item' => array(
            'ETERNAL PYRE' => array('image' => '/images/item/eternal-pyre.png'),
         ),
         'requires' => array(
            'SACRED SOUL STONE' => array('count' => '10K', 'image' => '/images/item/sacred-soul-stone.png'),
            'JEWEL OF MITAR' => array('image' => 'https://funventure.eu/wiki/minerssettlement/images/c/c3/JewelofMitar.png'),
            'DEPTH OF MITAR' => array('image' => 'https://funventure.eu/wiki/minerssettlement/images/2/2f/DepthOfMitar.png'),
            'EYES OF MITAR' => array('image' => 'https://funventure.eu/wiki/minerssettlement/images/0/0c/EyesOfMitar.png'),
            'HEART OF MITAR' => array('image' => 'https://funventure.eu/wiki/minerssettlement/images/9/98/HeartofMitar.png'),
         ),
      ),
      array(
         'limit' => 'UNLIMITED',
         'item' => array(
            'STORMFORGE AMULET' => array('image' => '/images/item/stormforge-amulet.png'),
         ),
         'requires' => array(
            'SACRED SOUL STONE' => array('count' => '10K', 'image' => '/images/item/sacred-soul-stone.png'),
            'JEWEL OF MITAR' => array('image' => 'https://funventure.eu/wiki/minerssettlement/images/c/c3/JewelofMitar.png'),
            'DEPTH OF MITAR' => array('image' => 'https://funventure.eu/wiki/minerssettlement/images/2/2f/DepthOfMitar.png'),
            'EYES OF MITAR' => array('image' => 'https://funventure.eu/wiki/minerssettlement/images/0/0c/EyesOfMitar.png'),
            'HEART OF MITAR' => array('image' => 'https://funventure.eu/wiki/minerssettlement/images/9/98/HeartofMitar.png'),
         ),
      ),
      array(
         'limit' => 'UNLIMITED',
         'item' => array(
            'OCEAN\'S BREATH' => array('image' => '/images/item/oceans-breath.png'),
         ),
         'requires' => array(
            'SACRED SOUL STONE' => array('count' => '10K', 'image' => '/images/item/sacred-soul-stone.png'),
            'JEWEL OF MITAR' => array('image' => 'https://funventure.eu/wiki/minerssettlement/images/c/c3/JewelofMitar.png'),
            'DEPTH OF MITAR' => array('image' => 'https://funventure.eu/wiki/minerssettlement/images/2/2f/DepthOfMitar.png'),
            'EYES OF MITAR' => array('image' => 'https://funventure.eu/wiki/minerssettlement/images/0/0c/EyesOfMitar.png'),
            'HEART OF MITAR' => array('image' => 'https://funventure.eu/wiki/minerssettlement/images/9/98/HeartofMitar.png'),
         ),
      ),
      array(
         'limit' => 'UNLIMITED',
         'item' => array(
            'AIRWHISPER CHARM' => array('image' => '/images/item/airwhisper-charm.png'),
         ),
         'requires' => array(
            'SACRED SOUL STONE' => array('count' => '10K', 'image' => '/images/item/sacred-soul-stone.png'),
            'JEWEL OF MITAR' => array('image' => 'https://funventure.eu/wiki/minerssettlement/images/c/c3/JewelofMitar.png'),
            'DEPTH OF MITAR' => array('image' => 'https://funventure.eu/wiki/minerssettlement/images/2/2f/DepthOfMitar.png'),
            'EYES OF MITAR' => array('image' => 'https://funventure.eu/wiki/minerssettlement/images/0/0c/EyesOfMitar.png'),
            'HEART OF MITAR' => array('image' => 'https://funventure.eu/wiki/minerssettlement/images/9/98/HeartofMitar.png'),
         ),
      ),
   );

   $return = "<table border=0 style='background-color:#444444; width:auto; border-spacing:5px 0; border-collapse:separate;'>".
             craftEntry($craftList).
             "</table>";

   return $return;

   $return = '<div class="row">'.
             '<div class="col-12 col-xl-9 col-lg-10 col-md-12 col-sm-12">'.
             '<div class="card card-outline card-success">'.
             '<!--<div class="card-header"><b class="text-xl"></b></div>-->'.
             '<div class="card-body">'.
             '<table class="table table-striped table-hover" style="width:auto;" border=0 cellpadding=10>'.
             '<thead><tr class="text-yellow"><th></th><th>Item</th><th>Components</th></thead><tbody>';

   $craftingList = $main->var('craftingList');

   foreach ($craftingList['scheme'] as $entryId => $entryInfo) {
      $entryName  = $entryInfo['label'];
      $entryImage = ($entryInfo['image']) ? sprintf("<img src='%s' height=25>",$entryInfo['image']) : '';;
      $entryCost  = json_decode($entryInfo['cost'],true);

      $entryComponents = array();
      foreach ($entryCost['item'] as $itemName => $itemCount) {
         $componentImage = $craftingList['component'][$itemName]['image'] ?: null;
         $componentLabel = $craftingList['component'][$itemName]['label'] ?: $itemName;
         $entryComponents[] = (($componentImage) ? sprintf("<img class='ml-4' src='%s' height=25 data-toggle='tooltip' title='%s'>",$componentImage,$componentLabel) :
                                                  sprintf("<span class='ml-4'>%s</span>",$componentLabel)).sprintf(" x%s",$format->numericReducer($itemCount));
      }

      $return .= sprintf("<tr><td>%s</td><td>%s</td><td>%s</td></tr>",$entryImage,$entryName,implode(' ',$entryComponents));
   }

   $return .= '</tbody></table>'.
              '</div>'.
              '</div>'.
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
