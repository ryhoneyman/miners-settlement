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
   'request'        => true,
   'input'          => true,
   'html'           => true,
   'adminlte'       => true,
));

$main->buildClass('constants','Constants',null,'local/constants.class.php');
$main->buildClass('format','Format',null,'local/format.class.php');

$main->title('Guide');

$input = $main->obj('input');
$html  = $main->obj('html');
$alte  = $main->obj('adminlte');

include 'ui/header.php';

print guideDisplay($main);

include 'ui/footer.php';

?>
<?php

function guideDisplay($main)
{
   $html    = $main->obj('html');
   $input   = $main->obj('input');
   $format  = $main->obj('format');
   $alte    = $main->obj('adminlte');
   $request = $main->obj('request');

   $baseURL = 'https://minersettlementguide.github.io/zero2hero';
   $dataURL = sprintf("%s/%s",$baseURL,'data');
   $imgURL  = sprintf("%s/%s",$dataURL,'img');

   $return    = '';
   $result    = $request->get('https://minersettlementguide.github.io/zero2hero/data/guide.json');
   $guideData = json_decode($request->responseBody,true);

   if ($result !== true || is_null($guideData)) { return $alte->displayRow($alte->displayCard("Error retrieving guide data",array('title' => 'Guide Error', 'container' => 'col-12'))); }

   foreach ($guideData as $sectionInfo) {
      $sectionName = $sectionInfo['section'];
      $sectionData = array();
      $cardList    = $sectionInfo['cards'] ?: array();

      foreach ($cardList as $cardInfo) {
         $cardTitle  = $cardInfo['title'];
         $cardImages = $cardInfo['screenshots'];
         $cardSteps  = $cardInfo['steps'];

         $imageData = array();
         foreach ($cardImages as $cardImage) {
            $imageData[] = sprintf("<img src='%s%s' class='mb-2' width=100%%>",$baseURL,preg_replace('/^\./','',$cardImage));
         }

         $sectionData[] = $alte->displayCard("<ul><li>".implode("<li>",$cardSteps)."</ul><br>".implode("<br>",$imageData),array('title' => $cardTitle, 'container' => 'col-12'));
      }

      $return .= $alte->displayRow($alte->displayCard(implode("\n",$sectionData),array('title' => $sectionName, 'container' => 'col-12 col-xl-6 col-lg-9')));
   }

   return $return;
}

?>
