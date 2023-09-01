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

print craftingDisplay($main);

include 'ui/footer.php';

?>
<?php

function craftingDisplay($main)
{
   $format = $main->obj('format');

   $craftingItems = $main->getItemCrafting('type','mitar-forge',true);

   $craftingComponentItems = array();
   
   foreach ($craftingItems as $craftingId => $craftingData) {
      $craftingDetails = $craftingData['details'];
      $craftingComponentItems = array_merge($craftingComponentItems,array_column($craftingDetails['input'],'name'),array_column($craftingDetails['output'],'name'));
   }

   $craftingComponentItems = array_unique(array_filter($craftingComponentItems));

   $componentItemList = $main->getItem('name',$craftingComponentItems,true,'name');

   $craftingList = array();

   foreach ($craftingItems as $craftingId => $craftingData) {
      $craftingDetails = $craftingData['details'];

      foreach (array('input','output') as $itemIO) {
         foreach ($craftingDetails[$itemIO] as $detailId => $detailItem) { 
            $craftingDetails[$itemIO][$detailId] = array_merge($craftingDetails[$itemIO][$detailId],$componentItemList[$detailItem['name']]); 
         }
      }

      $craftingList[] = $craftingDetails;
   }
 
   $return = "<style>\n".
             "@media (max-width:576px) {\n".
             "   .crafting-slot  { width:50px; height:50px; }\n".
             "   .crafting-image { width:38px; }\n".
             "}\n".
             "@media (min-width:576px) {\n".
             "   .crafting-slot  { width:75px; height:75px; }\n".
             "   .crafting-image { width:63px; }\n".
             "}\n".
             "</style>\n";

   $return .= "<table border=0 style='background-color:#444444; width:auto; border-spacing:5px 0; border-collapse:separate;'>".
              $format->craftEntry($craftingList).
              "</table>";

   return $return;


   $craftList = array(
      array(
         'limit' => 'UNLIMITED',
         'output' => array(
            array('name' => 'amulet-of-protection', 'link' => true),
         ),
         'input' => array(
            array('name' => 'stone-scale', 'count' => '10'),
            array('name' => 'mitar-ore', 'count' => 50),
            array('name' => 'magic-dust', 'count' => 55),
            array('name' => 'amulet-of-truth'),
            array('name' => 'amulet-of-truth'),
         ),
      ),
      array(
         'limit' => 'UNLIMITED',
         'item' => array(
            array('label' => 'AMULET OF ELEMENTS', 'link' => true, 'image' => '/images/item/amulet-of-elements.png'),
         ),
         'requires' => array(
            array('label' => 'STONE SCALE', 'count' => '10', 'image' => '/images/item/stone-scale.png'),
            array('label' => 'MITAR ORE', 'count' => 50, 'image' => '/images/item/mitar-ore.png'),
            array('label' => 'MAGIC DUST', 'count' => 55, 'image' => '/images/item/magic-dust.png'),
            array('label' => 'AMULET OF TRUTH', 'image' => '/images/item/amulet-of-truth.png'),
            array('label' => 'AMULET OF TRUTH', 'image' => '/images/item/amulet-of-truth.png'),
         ),
      ),
      array(
         'limit' => 'UNLIMITED',
         'item' => array(
            array('label' => 'DWARVEN AMULET', 'link' => true, 'image' => '/images/item/dwarven-amulet.png'),
         ),
         'requires' => array(
            array('label' => 'GOLD BAR', 'count' => 25, 'image' => '/images/item/gold-bar.png'),
            array('label' => 'RUNE II', 'count' => 3, 'image' => '/images/item/rune-ii.png'),
            array('label' => 'SACRED SOUL STONE', 'count' => 1500, 'image' => '/images/item/sacred-soul-stone.png'),
            array('label' => 'AMULET OF PROTECTION', 'image' => '/images/item/amulet-of-protection.png'),
            array('label' => 'AMULET OF PROTECTION', 'image' => '/images/item/amulet-of-protection.png'),
         ),
      ),
      array(
         'limit' => 'UNLIMITED',
         'item' => array(
            array('label' => 'AMULET OF STRENGTH', 'link' => true, 'image' => '/images/item/amulet-of-strength.png'),
         ),
         'requires' => array(
            array('label' => 'GOLD BAR', 'count' => 25, 'image' => '/images/item/gold-bar.png'),
            array('label' => 'RUNE III', 'count' => 3, 'image' => '/images/item/rune-iii.png'),
            array('label' => 'SACRED SOUL STONE', 'count' => 1500, 'image' => '/images/item/sacred-soul-stone.png'),
            array('label' => 'AMULET OF ELEMENTS', 'image' => '/images/item/amulet-of-elements.png'),
            array('label' => 'AMULET OF ELEMENTS', 'image' => '/images/item/amulet-of-elements.png'),
         ),
      ),
      array(
         'limit' => 'UNLIMITED',
         'item' => array(
            array('label' => 'JEWEL OF MITAR', 'link' => true, 'image' => '/images/item/jewel-of-mitar.png'),
         ),
         'requires' => array(
            array('label' => 'PLATINUM BAR', 'count' => 25, 'image' => '/images/item/platinum-bar.png'),
            array('label' => 'RUNE V', 'count' => 5, 'image' => '/images/item/rune-v.png'),
            array('label' => 'TANZANITE ESSENCE', 'count' => 100, 'image' => '/images/item/tanzanite-essence.png'),
            array('label' => 'DWARVEN AMULET', 'image' => '/images/item/dwarven-amulet.png'),
            array('label' => 'DWARVEN AMULET', 'image' => '/images/item/dwarven-amulet.png'),
         ),
      ),
      array(
         'limit' => 'UNLIMITED',
         'item' => array(
            array('label' => 'DEPTH OF MITAR', 'link' => true, 'image' => '/images/item/depth-of-mitar.png'),
         ),
         'requires' => array(
            array('label' => 'PLATINUM BAR', 'count' => 25, 'image' => '/images/item/platinum-bar.png'),
            array('label' => 'RUNE VI', 'count' => 5, 'image' => '/images/item/rune-vi.png'),
            array('label' => 'EMERALD ESSENCE', 'count' => 100, 'image' => '/images/item/emerald-essence.png'),
            array('label' => 'DWARVEN AMULET', 'image' => '/images/item/dwarven-amulet.png'),
            array('label' => 'DWARVEN AMULET', 'image' => '/images/item/dwarven-amulet.png'),
         ),
      ),
      array(
         'limit' => 'UNLIMITED',
         'item' => array(
            array('label' => 'POWER OF MITAR', 'image' => '/images/item/power-of-mitar.png'),
         ),
         'requires' => array(
            array('label' => 'SACRED SOUL STONE', 'count' => '10000', 'image' => '/images/item/sacred-soul-stone.png'),
            array('label' => 'JEWEL OF MITAR', 'image' => '/images/item/jewel-of-mitar.png'),
            array('label' => 'DEPTH OF MITAR', 'image' => '/images/item/depth-of-mitar.png'),
            array('label' => 'EYES OF MITAR', 'image' => '/images/item/eyes-of-mitar.png'),
            array('label' => 'HEART OF MITAR', 'image' => '/images/item/heart-of-mitar.png'),
         ), 
      ),
      array(
         'limit' => 'UNLIMITED',
         'item' => array(
            array('label' => 'ETERNAL PYRE', 'image' => '/images/item/eternal-pyre.png'),
         ),
         'requires' => array(
            array('label' => 'SACRED SOUL STONE', 'count' => '10000', 'image' => '/images/item/sacred-soul-stone.png'),
            array('label' => 'JEWEL OF MITAR', 'image' => '/images/item/jewel-of-mitar.png'),
            array('label' => 'DEPTH OF MITAR', 'image' => '/images/item/depth-of-mitar.png'),
            array('label' => 'EYES OF MITAR', 'image' => '/images/item/eyes-of-mitar.png'),
            array('label' => 'HEART OF MITAR', 'image' => '/images/item/heart-of-mitar.png'),
         ),
      ),
      array(
         'limit' => 'UNLIMITED',
         'item' => array(
            array('label' => 'STORMFORGE AMULET', 'image' => '/images/item/stormforge-amulet.png'),
         ),
         'requires' => array(
            array('label' => 'SACRED SOUL STONE', 'count' => '10000', 'image' => '/images/item/sacred-soul-stone.png'),
            array('label' => 'JEWEL OF MITAR', 'image' => '/images/item/jewel-of-mitar.png'),
            array('label' => 'DEPTH OF MITAR', 'image' => '/images/item/depth-of-mitar.png'),
            array('label' => 'EYES OF MITAR', 'image' => '/images/item/eyes-of-mitar.png'),
            array('label' => 'HEART OF MITAR', 'image' => '/images/item/heart-of-mitar.png'),
         ),
      ),
      array(
         'limit' => 'UNLIMITED',
         'item' => array(
            array('label' => 'OCEAN\'S BREATH', 'image' => '/images/item/oceans-breath.png'),
         ),
         'requires' => array(
            array('label' => 'SACRED SOUL STONE', 'count' => '10000', 'image' => '/images/item/sacred-soul-stone.png'),
            array('label' => 'JEWEL OF MITAR', 'image' => '/images/item/jewel-of-mitar.png'),
            array('label' => 'DEPTH OF MITAR', 'image' => '/images/item/depth-of-mitar.png'),
            array('label' => 'EYES OF MITAR', 'image' => '/images/item/eyes-of-mitar.png'),
            array('label' => 'HEART OF MITAR', 'image' => '/images/item/heart-of-mitar.png'),
         ),
      ),
      array(
         'limit' => 'UNLIMITED',
         'item' => array(
            array('label' => 'AIRWHISPER CHARM', 'image' => '/images/item/airwhisper-charm.png'),
         ),
         'requires' => array(
            array('label' => 'SACRED SOUL STONE', 'count' => '10000', 'image' => '/images/item/sacred-soul-stone.png'),
            array('label' => 'JEWEL OF MITAR', 'image' => '/images/item/jewel-of-mitar.png'),
            array('label' => 'DEPTH OF MITAR', 'image' => '/images/item/depth-of-mitar.png'),
            array('label' => 'EYES OF MITAR', 'image' => '/images/item/eyes-of-mitar.png'),
            array('label' => 'HEART OF MITAR', 'image' => '/images/item/heart-of-mitar.png'),
         ),
      ),
      array(
         'limit' => 'UNLIMITED',
         'item' => array(
            array('label' => 'HIGH ULDRED\'S PICKAXE', 'image' => '/images/item/high-uldreds-pickaxe.png'),
         ),
         'requires' => array(
            array('label' => 'DWARVEN PICKAXE', 'count' => '2', 'image' => '/images/item/dwarven-pickaxe.png'),
            array('label' => 'SACRED SOUL STONE', 'count' => '5000', 'image' => '/images/item/sacred-soul-stone.png'),
            array('label' => 'RUNE IX', 'count' => 2, 'image' => '/images/item/rune-ix.png'),
            array('label' => 'MITAR ORE', 'count' => 100, 'image' => '/images/item/mitar-ore.png'),
         ),
      ),
      array(
         'limit' => 'UNLIMITED',
         'item' => array(
            array('label' => 'AXE OF DIVISION', 'image' => '/images/item/axe-of-division.png'),
         ),
         'requires' => array(
            array('label' => 'HIGH ULDRED\'S PICKAXE', 'count' => 2, 'image' => '/images/item/high-uldreds-pickaxe.png'),
            array('label' => 'MITAR ORE', 'count' => 100, 'image' => '/images/item/mitar-ore.png'),
            array('label' => 'SACRED SOUL STONE', 'count' => '1000', 'image' => '/images/item/sacred-soul-stone.png'),
            array('label' => 'SWORD OF MARRIAGE', 'image' => '/images/item/sword-of-marriage.png'),
            array('label' => 'SWORD OF MARRIAGE', 'image' => '/images/item/sword-of-marriage.png'),
         ),
      ),
   );
}

?>
