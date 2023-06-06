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
));

$main->title('Gear List');

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

   $main->fetchGearList();

   $gearNav = array();
   foreach (array_keys($main->var('gearList')) as $gearType) {
      $gearLabel = ($main->obj('constants')->gearTypes())[$gearType];
      $gearNav[] = sprintf("<a href='#gear-%s' class='text-green'>%s</a>",strtolower(preg_replace('/\W/','-',$gearType)),$gearLabel);
   }

   print '<div class="mb-4">'.implode(" | ",$gearNav).'</div>';

   foreach ($main->var('gearList') as $gearType => $gearTypeList) {
      $return .= gearDisplay($main,$gearType,$gearTypeList);
   }

   return $return;
}

function gearDisplay($main, $gearType, $gearTypeList)
{
   $gearTypeLabel = ($main->obj('constants')->gearTypes())[$gearType];

   $return = '<div class="row">'.
             '<div class="col-12 col-xl-9 col-lg-10 col-md-12 col-sm-12">'.
             '<div class="card card-outline card-success">'.
             '<div class="card-header"><b class="text-xl" id="gear-'.strtolower(preg_replace('/\W/','-',$gearType)).'">'.$gearTypeLabel.'</b>'.
             '<div class="card-tools"><a href="#"><button type="button" class="btn btn-tool"><i class="fas fa-level-up"></i></button></a></div>'.
             '</div>'.
             '<div class="card-body">'.
             '<table class="table table-striped table-hover" style="width:auto;" border=0 cellpadding=10>'.
             '<thead><tr class="text-yellow"><th></th><th>Name</th><th>Primary Stats</th><th>Elemental Stats</th></tr></thead><tbody>';

   foreach ($gearTypeList as $entryId => $entryInfo) {
      $gearName    = $entryInfo['label'];
      $gearImage   = ($entryInfo['image']) ? sprintf("<img src='%s' height=50>",$entryInfo['image']) : '';;
      $gearAttribs = json_decode($entryInfo['attributes'],true);
      $gearPrimary = '';
      $gearElement = '';

      foreach ($main->obj('constants')->primaryAttribs() as $attribName => $attribInfo) {
         $rangeFormat = $attribInfo['range-format'];
         $gearPrimary .= sprintf("<span class='badge %s' style='width:90px;'>$rangeFormat <i class='fas %s float-right'></i></span> ",
                                 $attribInfo['background'],$gearAttribs["$attribName.min"],$gearAttribs["$attribName.max"],$attribInfo['icon']);
      }

      foreach ($main->obj('constants')->elementAttribs() as $elementName => $elementInfo) {
         if (!$gearAttribs["$elementName.min"]) { continue; }

         $rangeFormat = $elementInfo['range-format'];
         $gearElement .= sprintf("<span class='%s'>%s: $rangeFormat <i class='fa %s'></i></span><br>",
                                 $elementInfo['color'],$elementInfo['text'],$gearAttribs["$elementName.min"],
                                 $gearAttribs["$elementName.max"],$elementInfo['icon']);
      }

      $return .= sprintf("<tr><td>%s</td><td>%s</td><td>%s</td><td>%s</td></tr>",$gearImage,$gearName,$gearPrimary,$gearElement);
   }

   $return .= '</tbody></table>'.
              '</div>'.
              '</div>'.
              '</div>'.
              '</div>';

   return $return;
}

?>
