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
}

?>
