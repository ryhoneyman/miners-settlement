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

$main->title('Zero to Hero Guide');

$input = $main->obj('input');
$html  = $main->obj('html');
$alte  = $main->obj('adminlte');

include 'ui/header.php';

print guideDisplay($main);

print "<script src='/assets/js/colorpicker.js?t=".$main->now."' type='text/javascript'></script>\n".
      "<link rel='stylesheet' href='/assets/css/colorpicker.css?t=".$main->now."'>\n";

include 'ui/footer.php';

?>
<?php

function guideDisplay($main)
{
   $alte    = $main->obj('adminlte');
   $request = $main->obj('request');

   $baseURL        = 'https://minersettlementguide.github.io/zero2hero';
   $containerClass = 'col-12 col-xl-6 col-lg-9 mx-auto';

   $return    = '';
   $result    = $request->get(sprintf("%s/data/guide.json",$baseURL));
   $guideData = json_decode($request->responseBody,true);

   if ($result !== true || is_null($guideData)) { return $alte->displayRow($alte->displayCard("Error retrieving guide data",array('title' => 'Guide Error', 'container' => 'col-12'))); }

   
   if ($guideData['author']) {
      $authorInfo = sprintf("<h4>Created by <span class='text-red'>%s</span></h4><h5>%s %s</h5>",
                            $guideData['author'],
                            (($guideData['source']) ? sprintf('<a href="%s">%s</a>',$guideData['source'],$guideData['source']) : ''),
                            (($guideData['version']) ? sprintf('(version %s)',$guideData['version']) : ''));

      $return .= $alte->displayRow($alte->displayCard($authorInfo,array('container' => $containerClass)));
   }

   $return .= $alte->displayRow($alte->displayCard('
                            <div>
                                <div id="color-picker" class="grid-color-picker-container">
                                    <div class="flex-color-picker-column">
                                        <div id="picker-output-color" class="output-color-box"></div>
                                        <input id="picker-output-field" class="output-color-text" type="text" value="">
                                    </div>
                                    <div class="flex-color-picker-column">
                                        <div class="flex-color-sliders">
                                            <div id="picker-box-r" class="color-slider-box red-bg"></div>
                                            <input class="input-slider" type="range" id="picker-slider-r" min="0" max="15" value="15">
                                        </div>
                                        <div class="flex-color-sliders">
                                            <div id="picker-box-g" class="color-slider-box green-bg"></div>
                                            <input class="input-slider" type="range" id="picker-slider-g" min="0" max="15" value="0">
                                        </div>
                                        <div class="flex-color-sliders">
                                            <div id="picker-box-b" class="color-slider-box blue-bg"></div>
                                            <input class="input-slider" type="range" id="picker-slider-b" min="0" max="15" value="0">
                                        </div>
                                        <div class="flex-color-sliders">
                                            <div id="picker-box-grayscale" class="color-slider-box gray-bg"></div>
                                            <input class="input-slider" type="range" id="picker-slider-gray" min="0" max="15" value="0">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        ',array('title' => 'Name - Color Picker', 'container' => $containerClass)));

   foreach ($guideData['content'] as $sectionInfo) {
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

      $return .= $alte->displayRow($alte->displayCard(implode("\n",$sectionData),array('title' => $sectionName, 'container' => $containerClass)));
   }

   return $return;
}

?>
