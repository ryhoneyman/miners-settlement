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

$main->title('Scheme Crafting');

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

function craftingDisplay($main)
{
   $format = $main->obj('format');

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
      $entryCost  = $entryInfo['details']['cost'];

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
   $result     = $main->db()->query("select ic.id,ic.*,i.* from item_crafting ic, item i where ic.type = 'scheme' and ic.item_name = i.name and ic.active = 1 and i.active = 1",array('keyid' => 'id'));
   $components = array();
   $return     = array();

   foreach ($result as $entryId => $entryInfo) {
      $entryInfo['details'] = json_decode($entryInfo['details'],true);

      $entryCostItems = $entryInfo['details']['cost']['item'];

      // We need to get the info (label, image) for the components that make up the scheme
      if (is_array($entryCostItems)) {
         foreach ($entryCostItems as $componentName => $componentCount) { $components[$componentName]++; }
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
