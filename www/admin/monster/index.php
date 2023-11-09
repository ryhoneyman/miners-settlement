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

$main->title('Monster Administration');

$effectsList = array(
   'myself' => array(
      'lifesteal'     => array('format' => '{"percent.chance":100,"percent.adjust":{{percentAdjust}}}'),
      'critical-hit'  => array('format' => '{"percent.chance":{{percentChance}},"percent.adjust":{{percentAdjust}}}'),
      'stun-resist'   => array('format' => '{"percent.chance":100,"percent.adjust":{{percentAdjust}}}'),
      'extra-defense' => array('format' => '{"percent.chance":{{percentChance}},"percent.adjust":{{percentAdjust}}}'),
      'speed'         => array('format' => '{"percent.chance":100,"percent.adjust":{{percentAdjust}}}'),
   ),
   'enemy' => array(
      'stun'          => array('format' => '{"percent.chance":{{percentChance}},"flat.adjust":{{flatAdjust}}}'),
      'speed'         => array('format' => '{"percent.chance":100,"percent.adjust":-{{percentAdjust}}}'),
      'defense'       => array('format' => '{"percent.chance":100,"percent.adjust":-{{percentAdjust}}}'),
   ),
);

$validations = array(
   'health'  => array('type' => 'int', 'checks' => 'gt:0'),
   'attack'  => array('type' => 'int', 'checks' => 'gte:0'),
   'defense' => array('type' => 'int', 'checks' => 'gte:0'),
   'speed'   => array('type' => 'float', 'format' => '%1.2f', 'checks' => 'gt:0'),

   'fire-damage'      => array('type' => 'int', 'checks' => 'gt:0'),
   'water-damage'     => array('type' => 'int', 'checks' => 'gt:0'),
   'wind-damage'      => array('type' => 'int', 'checks' => 'gt:0'),
   'earth-damage'     => array('type' => 'int', 'checks' => 'gt:0'),
   'lightning-damage' => array('type' => 'int', 'checks' => 'gt:0'),

   'fire-resist'      => array('type' => 'int', 'checks' => 'gt:0'),
   'water-resist'     => array('type' => 'int', 'checks' => 'gt:0'),
   'wind-resist'      => array('type' => 'int', 'checks' => 'gt:0'),
   'earth-resist'     => array('type' => 'int', 'checks' => 'gt:0'),
   'lightning-resist' => array('type' => 'int', 'checks' => 'gt:0'),

   'effects_myself_lifesteal_percentAdjust'     => array('type' => 'int', 'checks' => 'gt:0'),
   'effects_myself_critical-hit_percentChance'  => array('type' => 'int', 'checks' => 'gt:0;lte:100'),
   'effects_myself_critical-hit_percentAdjust'  => array('type' => 'int', 'checks' => 'gt:0'),
   'effects_myself_stun-resists_percentAdjust'  => array('type' => 'int', 'checks' => 'gt:0'),
   'effects_myself_extra-defense_percentChance' => array('type' => 'int', 'checks' => 'gt:0;lte:100'),
   'effects_myself_extra-defense_percentAdjust' => array('type' => 'int', 'checks' => 'gt:0'),
   'effects_myself_speed_percentAdjust'         => array('type' => 'int', 'checks' => 'gt:0'),

   'effects_enemy_stun_percentChance'    => array('type' => 'int', 'checks' => 'gt:0;lte:100'),
   'effects_enemy_stun_flatAdjust'       => array('type' => 'int', 'checks' => 'gt:0'),
   'effects_enemy_speed_percentAdjust'   => array('type' => 'int', 'checks' => 'gt:0'),
   'effects_enemy_defense_percentAdjust' => array('type' => 'int', 'checks' => 'gt:0'),
);

$main->var('effectsList',$effectsList);
$main->var('validations',$validations);
$main->var('inputPrefix','data_');

if ($main->obj('input')->get('clear')) {
   $main->sessionValue('monster.admin.inputData',null,true);
}
else if ($main->obj('input')->get('submit')) {
   $inputData = array();

   foreach ($_POST as $name => $value) {
      if (preg_match('/^'.$main->var('inputPrefix').'(\S+)$/',$name,$match)) { $inputData[$match[1]] = $value; }
   }

   $main->sessionValue('monster.admin.inputData',$inputData);
}

checkInputData($main);

include 'ui/header.php';

displayPage($main);
displayResults($main);

include 'ui/footer.php';

?>
<?php

function displayResults($main)
{
   $alte        = $main->obj('adminlte');
   $html        = $main->obj('html');
   $inputPrefix = $main->var('inputPrefix');
   $inputValid  = $main->var('inputValid');
   $validations = $main->var('validations');
   $effectsList = $main->var('effectsList');
   $inputData   = $main->sessionValue('monster.admin.inputData');

   if (!$inputData) { return; }

   // If we have invalid input, do not render results
   foreach ($inputValid as $varName => $varValid) { if ($varValid === false) { return; } }

   $attributes = array();
   $effects    = array();

   // independently process the normal attributes and then setup pre-processing of effects
   foreach ($inputData as $varName => $varValue) {
      if ($varValue == '') { continue; }

      if (preg_match('/^effects_(\S+?)_(\S+?)_(\S+?)$/',$varName,$match)) {
         $affects    = $match[1];
         $attribName = $match[2];
         $variable   = $match[3];


         $effects[$affects][$attribName][$variable] = $varValue;
      }
      else { $attributes[$varName] = $varValue; }
   }

   // process effects attributes
   foreach ($effects as $affects => $attribList) {
      foreach ($attribList as $attribName => $attribInfo) {
         $replace = array();
         foreach ($attribInfo as $key => $value) { $replace['{{'.$key.'}}'] = $value; }

         $effectAttrib = json_decode(str_replace(array_keys($replace),array_values($replace),$effectsList[$affects][$attribName]['format']),true);

         if (is_null($effectAttrib)) { continue; }

         $attributes['effects'][$affects][$attribName] = $effectAttrib;
      }
   }

   $resultsContent = '';
   $warnings       = array();

   $maxKnownAttack = (770 + 140) * 46;  // TS+15 & GS+15 with runes

   if ($attributes['attack'] == 0 && $attributes['effects']['myself']['critical-hit']) { 
      $warnings[] = '<li>Critical Hit detected with 0 base attack, will not activate in battle.'; 
   }
   if ($maxKnownAttack < ($attributes['defense'] * 0.6) && $attributes['effects']['myself']['extra-defense']) {
      $warnings[] = '<li>Extra Defense detected when player maximum damage cannot penetrate current defense';
   } 
   if ($attributes['attack'] == 0 && $attributes['effects']['myself']['lifesteal']) {
      $elementAttck = 0;
      foreach ($main->obj('constants')->elementAttribs() as $elementName => $elementInfo) {
         $elementAttack += $attributes[$elementName];
      }
      if ($elementAttack == 0) {
         $warnings[] = '<li>Lifesteal detected with no base/elemental damage possible, will not activate in battle.';
      }
   }

   if ($warnings) {
      $resultsContent .= "<div class='text-red mb-4'><ul>".implode('<br>',$warnings)."</ul></div>";
   }

   $resultsContent .= "<span class='text-white' style='font-family:monoscape,monospace;'>".json_encode($attributes,JSON_UNESCAPED_SLASHES)."</span>";

   print $alte->displayRow($alte->displayCard($resultsContent,array('container' => 'col-12 col-xl-12 col-lg-12 col-md-12 col-sm-12', 'title' => 'Results')));
}

function displayPage($main)
{
   $alte        = $main->obj('adminlte');
   $html        = $main->obj('html');
   $effectsList = $main->var('effectsList');
   $inputPrefix = $main->var('inputPrefix');
   $inputValid  = $main->var('inputValid');
   $inputData   = $main->sessionValue('monster.admin.inputData');

   $content = $html->startForm(array('method' => 'post'));

   $primaryContent = "<div>";
   $statsContent   = '';
   $effectsContent = '';

   foreach ($main->obj('constants')->primaryAttribs() as $attribName => $attribInfo) {
      $varName  = sprintf("%s%s",$inputPrefix,$attribName);
      $varValue = array_key_exists($attribName,$inputData) ? $inputData[$attribName] : '';
      $varValid = $inputValid[$attribName] ?: true;

      $primaryLabel = ucwords($attribName);
      $primaryIcon  = sprintf("<i class='fas %s %s'></i>",$attribInfo['color'],$attribInfo['icon']);

      $primaryContent .= "<div style='display:inline-block;'><label for='$varName'>$primaryIcon $primaryLabel</label>".
                         $html->inputText($varName,$varValue,10,5,array('required' => true, 'class' => 'form-control mr-4'.((!$varValid) ? ' is-invalid' : ''), 'style' => 'height:1.5rem;'))."</div>\n";

   }
   
   $primaryContent .= "</div>";

   $statsContent .= $alte->displayCard($primaryContent,array('container' => 'col-12 col-xl-12 col-lg-12 col-md-12 col-sm-12', 'title' => 'Primary'));

   $elementalContent = '';

   $elementalList = array();
   foreach ($main->obj('constants')->elementAttribs() as $elementName => $elementInfo) {
      $elementType = $elementInfo['type'];
      $elementalList[$elementType][$elementName] = $elementInfo; 
   }
   
   foreach ($elementalList as $elementType => $elementTypeList) {
      $elementTypeLabel = ucwords(preg_replace('/\W/',' ',$elementType));
      $elementalContent = "<div>";

      foreach ($elementTypeList as $elementName => $elementInfo) {
         $varName  = sprintf("%s%s",$inputPrefix,$elementName);
         $varValue = array_key_exists($elementName,$inputData) ? $inputData[$elementName] : '';
         $varValid = $inputValid[$elementName] ?: true;

         $elementIcon  = sprintf("<i class='fas %s %s'></i>",$elementInfo['color'],$elementInfo['icon']);
         $elementLabel = ucfirst($elementInfo['element']);

         $elementalContent .= "<div style='display:inline-block;'><label for='$varName'>$elementIcon $elementLabel</label>".
                              $html->inputText($varName,$varValue,10,5,array('class' => 'form-control mr-4'.((!$varValid) ? ' is-invalid' : ''), 'style' => 'height:1.5rem;'))."</div>\n";
          
      }

      $elementalContent .= "</div>";

      $statsContent .= $alte->displayCard($elementalContent,array('container' => 'col-12 col-xl-12 col-lg-12 col-md-12 col-sm-12', 'title' => $elementTypeLabel));
   }

   $labels = array(
      'percentAdjust' => '% Adjust',
      'percentChance' => '% Chance',
      'flatAdjust'    => 'Adjust',
   );

   foreach ($effectsList as $affects => $affectAttribs) {
      $affectsContent = "<div>";
      $affectsContent = '';
      foreach ($affectAttribs as $attribName => $attribInfo) {
         if (preg_match_all('/{{(\S+?)}}/',$attribInfo['format'],$matches)) {
            $variables   = $matches[1];
            $attribLabel = ucwords(preg_replace('/\W/',' ',$attribName));

            $affectsContent .= "<div class='col-12' style='display:inline-block;'>".
                               "<div class='text-yellow' style='display:inline-block; width:100px;'>$attribLabel</div>"; 

            foreach ($variables as $variable) {
               $varKey   = sprintf("effects_%s_%s_%s",$affects,$attribName,$variable);
               $varName  = sprintf("%s%s",$inputPrefix,$varKey);
               $varValue = array_key_exists($varKey,$inputData) ? $inputData[$varKey] : '';
               $varValid = $inputValid[$varKey] ?: true;

               $affectsContent .= "<div style='display:inline-block;'><label for='$varName'>".$labels[$variable]."</label>".
                                  $html->inputText($varName,$varValue,10,5,array('class' => 'form-control'.((!$varValid) ? ' is-invalid' : ''), 'style' => 'height:1.5rem;'))."</div>";
            }
            $affectsContent .= "</div>";
         }
      }
      //$affectsContent .= "</div>";

      $effectsContent .= $alte->displayCard($affectsContent,array('container' => 'col-12 col-xl-6 col-lg-6 col-md-6 col-sm-12', 'title' => ucfirst($affects)));
   }

   $effectsContent .= "<div>".
                      "<div style='display:inline-block;'>".$html->submit('submit','Calculate',array('class' => 'form-control bg-primary'))."</div>". 
                      "<div style='display:inline-block;'>".$html->submit('clear','Clear',array('class' => 'form-control bg-primary ml-2'))."</div>".
                      "</div>";

   $content .= $alte->displayRow($alte->displayCard($alte->displayRow($statsContent),array('container' => 'col-12 col-xl-6 col-lg-12 col-md-12 col-sm-12', 'title' => 'Stats')).
                                 $alte->displayCard($alte->displayRow($effectsContent),array('container' => 'col-12 col-xl-6 col-lg-12 col-md-12 col-sm-12', 'title' => 'Effects'))); 

   $content .= $html->endForm();

   print $content;
}

function validateInput($inputValue, $validInfo)
{
    if ($inputValue == '') { return true; }

    $type   = $validInfo['type'];  
    $format = $validInfo['format'] ?: '%d';
    $checks = $validInfo['checks'] ?: null;

    foreach (explode(';',$checks) as $check) {
       list($checkOp,$checkValue) = explode(':',$check,2);

       if      ($checkOp == 'gt' && $inputValue <= $checkValue) { return false; }
       else if ($checkOp == 'gte' && $inputValue < $checkValue) { return false; }
       else if ($checkOp == 'lt' && $inputValue >= $checkValue) { return false; }
       else if ($checkOp == 'lte' && $inputValue > $checkValue) { return false; }
    }

    return true;
}

function checkInputData($main)
{
   $validations = $main->var('validations');
   $inputData   = $main->sessionValue('monster.admin.inputData') ?: array();
   $inputValid  = array();

   foreach ($inputData as $varKey => $varValue) {
      $inputValid[$varKey] = validateInput($varValue,$validations[$varKey]);
   }

   $main->var('inputValid',$inputValid);

   return true;
}


?>
