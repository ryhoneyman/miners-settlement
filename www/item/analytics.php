<?php
include_once 'miners-settlement-init.php';

if (0) {
   ini_set('display_errors', '1');
   ini_set('display_startup_errors', '1');
   error_reporting(E_ALL);
}

include_once 'common/debug.class.php';
include_once 'common/input.class.php';
include_once 'common/html.class.php';
include_once 'local/constants.class.php';
include_once 'local/item.class.php';

$debug     = new Debug(9,DEBUG_COMMENT);
$input     = new Input($debug);
$html      = new HTML($debug);
$constants = new Constants($debug);

$title    = 'Item';
$subtitle = 'Analytics';

$selectedItem = $input->get('item','alphanumeric,dash');
$select       = ($input->get('select')) ? true : false;
$calculate    = ($input->get('calculate')) ? true : false;
$godRoll      = ($input->get('godroll')) ? true : false;
$randomRoll   = ($input->get('randomRoll')) ? true : false;
$itemSubmit   = ($calculate || $godRoll || $randomRoll);
$itemInput    = ($select) ? array() : getItemInput();
$inputErrors  = array();

$itemList = getItems();
$itemBase = $itemList[$selectedItem] ?: null;     // Base is the raw data for the item

// Build the pulldown list of items
$selectItem = array();
foreach ($itemList as $itemId => $itemData) { $selectItem[$itemId] = sprintf("%s: %s",ucfirst($itemData['type']),$itemData['name']); }
asort($selectItem);

// If there was no input and Calculate was pressed, just do a random roll.
if (!$itemInput && $calculate) { $randomRoll = true; }

if ($godRoll)         { $itemInput = setItemValues($itemBase,'max'); }
else if ($randomRoll) { $itemInput = randomItemValues($selectedItem); }

$itemInfo = buildItemInfo($itemBase,$itemInput);  // Info is the raw + user data + validity

if ($selectedItem && $calculate) {
   $inputErrors = validateItemInput($itemInfo,$itemInput);
}

include 'ui/header.php';

print $html->startform(array('method' => 'post'));
print $html->select('item',$selectItem,$selectedItem);
print $html->submit('select','Select',array('class' => 'btn btn-primary btn-sm'));


print "<p>\n";

?>
<div class="row">
   <div class="col-3">
       <div class="card card-outline card-success">
          <div class="card-header"><b>Item Ranges</b></div>
          <div class="card-body">
          <?php print itemRangeDisplay($itemBase); ?>
          </div>
      </div>
   </div>

   <div class="col-3">
       <div class="card card-outline card-primary">
          <div class="card-header"><b>Item Input</b></div>
          <div class="card-body">
          <?php 
             if ($selectedItem) {
                print itemInputDisplay($itemInfo,$itemInput,$inputErrors)."<br>";
                print $html->submit('calculate','Calculate',array('class' => 'float-right btn btn-primary btn-sm'));
                print $html->submit('godroll','God Roll',array('class' => 'float-left btn btn-success btn-sm'));
                print $html->submit('randomRoll','Random',array('class' => 'float-left btn btn-warning btn-sm'));
             }
          ?>
          </div>
      </div>
   </div>

   <div class="col-4">
      <div class="card card-outline card-secondary">
          <div class="card-header"><b>Item Quality</b></div>
          <div class="card-body">
          <?php
             if ($selectedItem && $itemSubmit && !$inputErrors) {
                print itemInputQuality($itemInfo);
             }
          ?>
          </div>
      </div>
      <div class="card card-outline card-secondary">
          <div class="card-header"><b>Item Overall</b></div>
          <div class="card-body">
          <?php
             if ($selectedItem && $itemSubmit && !$inputErrors) {
                print itemInputOverall($selectedItem,$itemBase,$itemInfo);
             }
          ?>
          </div>
      </div>
   </div>
</div>
<div class="row">
   <div class="col-10">
       <div class="card card-outline card-danger">
          <div class="card-header"><b>Item Results</b></div>
          <div class="card-body">
          <?php if ($selectedItem && isset($itemInput['level']) && !$inputErrors) { print itemResultsDisplay($selectedItem,$itemInput); } ?>
          </div>
      </div>
   </div>
</div>

<?php
print $html->endform();

include 'ui/footer.php';

?>
<?php

function itemResultsDisplay($selectedItem, $itemInput)
{
   global $debug;

   $item = new Item($debug);

   $item->load($selectedItem);

   $item->generate($itemInput,array('level' => $itemInput['level']));

   $return = "<div class='row'>";

   for ($level = 0; $level <= 10; $level++) {
      $item->enhance(0);
      $item->enhance($level);
      $return .= "<div class='card col-3'>".itemDisplay($item)."</div>";
   }

   $return .= "</div>";

   return $return;
}

function itemDisplay($item)
{
   global $constants;

   $itemStats = $item->export();

   if (!$itemStats) { return ''; }

   $itemLevel   = $item->level();
   $itemDesc    = $item->description() ?: 'USE THIS ITEM TO FIGHT THE EVIL THAT IS LURKING OUTSIDE THE VILLAGE';
   $itemImage   = $item->image() ?: '/images/item/none.png';
   $levelCircle = ($itemLevel) ? "<span style='position:relative; left:-7px; bottom:20px; height:15px; width:15px; ".
                                 "background-color:#000; color:#fff; border-radius:50%; font-size:0.8em; display:inline-block; ".
                                 "text-align:center; line-height:10px;'>$itemLevel</span>" : '';

   $return = "<table border=0 style='background:#214268; border-radius:10px;'>".
             "<tr><td colspan=4 style='color:#fff; background:#454545; text-align:left; border-radius:10px; padding:5px;'>".$item->name()."</td></tr>";

   $return .= "<tr><td style='padding:5px; margin-left:auto; margin-right:auto;'><span><img src='$itemImage' style='width:auto; height:auto; max-width:50px;'>$levelCircle</span></td>".
              "<td colspan=3 style='padding:10px;'><table border=0><tr><td style='color:#fff; background:#454545; font-size:0.6em;'>$itemDesc</td></tr></table></td></tr>";

   $return .= itemDisplayPrimary($itemStats);
   $return .= itemDisplayElements($itemStats,null,array('background' => '#0a1833'));

   $return .= "</table>";

   return $return;
}

function itemInputOverall($itemId, $itemBase, $itemInfo)
{
   global $constants;

   if (!$itemInfo) { return ''; }

   $return = "<table border=0 cellpadding=5 style='width:100%;'>";

   $percentColors = array(
      '5'   => array('color' => '#ff0000', 'label' => 'Horrible'),
      '25'  => array('color' => '#ff9900', 'label' => 'Poor'),
      '50'  => array('color' => '#ffaa00', 'label' => 'Below-Average'),
      '75'  => array('color' => '#ffff00', 'label' => 'Average'),
      '95'  => array('color' => '#aaff00', 'label' => 'Good'),
      '100' => array('color' => '#66ff00', 'label' => 'Godly'),
   );

   $weights = array( 
      'ring' => array(
         'general' => array(
            'label' => 'General Use',
            'list' => array(
               'default' => array(
                  'health'  => 30,
                  'attack'  => 0,
                  'defense' => 0,
                  'speed'   => 0,
                  'elemental.damage' => 70,
                  'elemental.resist' => 0,
               ),
            ),
         ),
      ),
      'shield' => array(
         'general' => array(
            'label' => 'General Use',
            'list' => array(
               'default' => array(
                  'health'  => 20,
                  'attack'  => 0,
                  'defense' => 10,
                  'speed'   => 50,
                  'elemental.damage' => 20,
                  'elemental.resist' => 0,
               ),
               'uldreds-peace' => array(
                  'health'  => 5,
                  'attack'  => 0,
                  'defense' => 0,
                  'speed'   => 70,
                  'elemental.damage' => 25,
                  'elemental.resist' => 0,
               ),
               'thousand-soul' => array(
                  'health'  => 20,
                  'attack'  => 10,
                  'defense' => 0,
                  'speed'   => 70,
                  'elemental.damage' => 0,
                  'elemental.resist' => 0,
               ),
               'final-stand' => array(
                  'health'  => 20,
                  'attack'  => 0,
                  'defense' => 0,
                  'speed'   => 80,
                  'elemental.damage' => 0,
                  'elemental.resist' => 0,
               ),
            ),
         ),
         'red-dragon' => array(
            'label' => 'Red Dragon',
            'list' => array(
               'default' => array(
                  'health'  => 30,
                  'attack'  => 0,
                  'defense' => 0,
                  'speed'   => 40,
                  'elemental.damage' => 0,
                  'elemental.resist' => 30,
               ),
            ),
         ),
      ),
      'amulet' => array(
         'general' => array(
            'label' => 'General Use',
            'list' => array(
               'default' => array(
                  'health'  => 50,
                  'attack'  => 0,
                  'defense' => 0,
                  'speed'   => 0,
                  'elemental.damage' => 50,
                  'elemental.resist' => 0,
               ),
               'eyes-of-mitar' => array(
                  'health'  => 0,
                  'attack'  => 0,
                  'defense' => 20,
                  'speed'   => 0,
                  'elemental.damage' => 80,
                  'elemental.resist' => 0,
               ),
               'power-of-mitar' => array(
                  'health'  => 50,
                  'attack'  => 0,
                  'defense' => 0,
                  'speed'   => 0,
                  'elemental.damage' => 50,
                  'elemental.resist' => 0,
               ),
            ),
         ),
      ),
      'weapon' => array(
         'general' => array(
            'label' => 'General Use',
            'list' => array(
               'default' => array(
                  'health'  => 20,
                  'attack'  => 20,
                  'defense' => 10,
                  'speed'   => 25,
                  'elemental.damage' => 25,
                  'elemental.resist' => 0,
               ),
            ),
         ),
         'crit' => array(
            'label' => 'Crit Weapon',
            'list' => array(
               'default' => array(
                  'health'  => 0,
                  'attack'  => 80,
                  'defense' => 0,
                  'speed'   => 20,
                  'elemental.damage' => 0,
                  'elemental.resist' => 0,
               ),
            ),
         ),
         'low-dragon' => array(
            'label' => 'Low-level Dragon',
            'list' => array(
               'default' => array(
                  'health'  => 0,
                  'attack'  => 0,
                  'defense' => 0,
                  'speed'   => 80,
                  'elemental.damage' => 20,
                  'elemental.resist' => 0,
               ),
               'spear-of-the-gods' => array(
                  'health'  => 0,
                  'attack'  => 0,
                  'defense' => 0,
                  'speed'   => 80,
                  'elemental.damage' => 20,
                  'elemental.resist' => 0,
               ),
            ),
         ),
         'high-dragon' => array(
            'label' => 'High-level Dragon',
            'list' => array(
               'default' => array(
                  'health'  => 15,
                  'attack'  => 0,
                  'defense' => 0,
                  'speed'   => 70,
                  'elemental.damage' => 15,
                  'elemental.resist' => 0,
               ),
               'spear-of-the-gods' => array(
                  'health'  => 15,
                  'attack'  => 0,
                  'defense' => 0,
                  'speed'   => 70,
                  'elemental.damage' => 15,
                  'elemental.resist' => 0,
               ),
            ),
         ),
      ),
   );

   $attribList = $constants->attribs();
   $itemType   = $itemBase['type'];

   $finalStat   = 0;
   $statList    = array();
   $percentList = array();

   foreach ($itemInfo as $attribName => $attribInfo) {
      if ($attribName == 'level') { continue; }

      $attribPercent = sprintf("%d",$attribInfo['level.percent']);
      $attribType    = $attribList[$attribName]['type'];
  
      $statList['attrib'][$attribType][] = $attribPercent; 
      $statList['total']++;
   }

   foreach ($weights as $weightType => $itemTypeList) {
      if ($weightType != $itemType) { continue; }

      foreach ($itemTypeList as $typePurpose => $typePurposeInfo) {
         $typeLabel    = $typePurposeInfo['label'];
         $weightValues = $typePurposeInfo['list'][$itemId] ?: $typePurposeInfo['list']['default'];

         $percentList[$typeLabel]['percent'] = 0;
         
         foreach ($statList['attrib'] as $attribType => $typeList) {
            $listCount    = count($typeList);
            $percentTotal = array_sum($typeList);
            $statPercent  = $percentTotal/$listCount;

            $percentList[$typeLabel]['percent'] += $statPercent * ($weightValues[$attribType]/100);
         }

         foreach ($percentColors as $percent => $percentInfo) {
            if ($percentList[$typeLabel]['percent'] <= $percent) {
               $percentList[$typeLabel]['desc']  = $percentInfo['label'];
               $percentList[$typeLabel]['color'] = $percentInfo['color'];
               break;
            }
         }
      }
   }

   foreach ($percentList as $labelName => $labelInfo) {
      $labelPercent = $labelInfo['percent'];
      $labelColor   = $labelInfo['color'];
      $labelDesc    = $labelInfo['desc'];

      $percentBar = sprintf("<div style='width:100%%; background-color:#ddd;'>".
                            "<div style='text-align:right; padding-top:5px; padding-bottom:5px; color:white; width:%d%%; background-color:%s; font-size:0.8em;'></div></div>",$labelPercent,$labelColor);

      $return .= "<tr><td style='width:20%; font-size:0.8em;'>$labelName</td><td style='width:60%;'>$percentBar</td><td style='width:20%; font-size:0.8em;'>$labelDesc</td></tr>";
   }

   $return .= "</table>";

   return $return;
}

function itemInputQuality($itemInfo)
{
   if (!$itemInfo) { return ''; }

   $return = "<table border=0 cellpadding=5 style='width:100%;'>";

   $percentColors = array(
      '5'   => array('color' => '#ff0000'),
      '25'  => array('color' => '#ff9900'),
      '50'  => array('color' => '#ffaa00'),
      '75'  => array('color' => '#ffff00'),
      '95'  => array('color' => '#aaff00'),
      '100' => array('color' => '#66ff00'),
   );

   foreach ($itemInfo as $attribName => $attribInfo) {
      if ($attribName == 'level' || !$attribInfo['value']) { continue; }

      $attribLabel  = $attribInfo['label'];
      $levelPercent = sprintf("%d",$attribInfo['level.percent']);

      foreach ($percentColors as $percent => $percentInfo) {
         if ($levelPercent <= $percent) { $percentColor = $percentInfo['color']; break; }
      }

      $percentBar = sprintf("<div style='width:100%%; background-color:#ddd;'>".
                            "<div style='text-align:right; padding-top:5px; padding-bottom:5px; color:white; width:%d%%; background-color:%s; font-size:0.8em;'></div></div>",$levelPercent,$percentColor);
      
      $return .= "<tr><td style='width:20%; font-size:0.8em;'>$attribLabel</td><td style='width:80%;'>$percentBar</td></tr>";
   }

   $return .= "</table>";

   return $return;
}

function itemInputDisplay($itemInfo, $itemInput, $inputErrors)
{
   global $constants, $html;

   if (!$itemInfo) { return ''; }

   $attribList = $constants->attribs();

   $inputOptions  = array('params' => array('class' => 'form-control'));
   $attribOptions = $inputOptions;

   if ($inputErrors['level']) { $attribOptions['params']['class'] .= ' is-invalid'; }

   $return = "<table border=0 cellpadding=5>".
             "<tr>".
             "<td class='ia-input-line'><b>Level</b></td>".
             "<td><div class='input-group input-group-sm'>".$html->input_text("item_level",$itemInput["level"],10,8,$attribOptions)."</div></td>".
             "<td class='ia-input-line'>(0-10)</td>".
             "</tr>";

   foreach ($itemInfo as $attribName => $attribInfo) {
      if ($attribName == 'level') { continue; }
      $attribOptions = $inputOptions;

      if ($inputErrors[$attribName]) { $attribOptions['params']['class'] .= ' is-invalid'; }

      $inputName   = 'item_'.preg_replace('/\./','-',$attribName);
      $attribMin   = $attribInfo['min'];
      $attribMax   = $attribInfo['max'];
      $attribEMax  = $attribInfo['emax'];
      $attribLabel = $attribInfo['label'];

      $return .= "<tr>".
                 "<td class='ia-input-line'><b>$attribLabel</b></td>".
                 "<td><div class='input-group input-group-sm'>".$html->input_text($inputName,$itemInput[$attribName],10,8,$attribOptions)."</div></td>".
                 "<td class='ia-input-line'>($attribMin - $attribEMax)</td>".
                 "</tr>";
   }

   $return .= "</table>";

   return $return;
}

function itemRangeDisplay($itemInfo)
{
   global $constants;

   if (!$itemInfo) { return ''; }

   $return = "<table border=0 style='background:#ffccbb'>".
             "<tr><td colspan=4 style='text-align:center;'>FROM</td></tr>";

   $return .= itemDisplayPrimary($itemInfo,'min');

   $return .= itemDisplayElements($itemInfo,'min'); 

   $return .= "<tr><td colspan=4 style='text-align:center;'>TO</td></tr>";

   $return .= itemDisplayPrimary($itemInfo,'max');

   $return .= itemDisplayElements($itemInfo,'max'); 

   $return .= "</table>";

   return $return;
}

function itemDisplayElements($itemInfo, $limit = null, $options = null)
{
   global $constants;

   $limit = (is_null($limit)) ? '' : ".$limit";

   $background = $options['background'] ?: '#6f4e37';

   $elementDisplay = array(
      'fire' => array(
         'color' => '#ff0000',
         'icon' => 'fire',
      ),
      'water' => array(
         'color' => '#aaaaff',
         'icon' => 'tint',
      ),
      'wind' => array(
         'color' => '#00ffff',
         'icon' => 'wind',
      ),
      'earth' => array(
         'color' => '#00ff00',
         'icon' => 'leaf',
      ),
      'lightning' => array(
         'color' => '#ffff00',
         'icon' => 'bolt',
      ),
   );

   $itemElements = array();
   foreach ($constants->elements() as $element) {
      foreach (array('damage','resist') as $feature) {
         if (isset($itemInfo["$element.$feature$limit"])) {
            $itemElements[$feature][$element] = $itemInfo["$element.$feature$limit"];
         }
      }
   }

   if (!$itemElements) { return ''; }

   $return = "<tr>".
             "<td colspan=4 style='background:$background; text-align:center;'>";

   foreach (array('damage','resist') as $feature) {
      foreach ($itemElements[$feature] as $element => $value) {
         $elementProp = $elementDisplay[$element];
         $return .= sprintf("<span style='color:%s;'>%s %s: %s <i class='fa fa-%s'></i></span><br>",$elementProp['color'],strtoupper($element),strtoupper($feature),$value,$elementProp['icon']);
      }
   }

   $return .= "</td>".
              "</tr>";

   return $return;
}

function itemDisplayPrimary($itemInfo, $limit = null)
{
   global $constants;

   $limit = (is_null($limit)) ? '' : ".$limit";

   $attribDisplay = array(
      'health' => array(
         'color' => '#aa0000',
         'icon'  => 'heart',
      ),
      'attack' => array(
         'color' => '#ff6600',
         'icon'  => 'sword',
      ),
      'defense' => array(
         'color' => '#0000ff',
         'icon'  => 'shield',
      ),
      'speed' => array(
         'color' => '#00aa00',
         'icon'  => 'clock',
      ),
   );

   $return = "<tr>";

   foreach ($constants->primaryAttribs() as $attribName) {
      $attribProp  = $attribDisplay[$attribName];
      $attribValue = $itemInfo["$attribName$limit"] ?: 0;

      $return .= sprintf("<td width=75px style='padding:5px; color:#fff; background:%s;'>%s <i class='float-right fa fa-%s'></i></td>",$attribProp['color'],$attribValue,$attribProp['icon']);
   }
   
   $return .= "</tr>";

   return $return;
}

function randomItemValues($itemId)
{
   global $debug;

   $item = new Item($debug);

   $item->load($itemId);

   $item->generate();

   return $item->export();
}

function setItemValues($itemInfo, $strategy)
{
   $return = array('level' => 0);

   foreach ($itemInfo as $attrib => $attribValue) {
      if (!preg_match('/^(.*)\.min$/i',$attrib,$match)) { continue; }

      $attribName  = $match[1];
      $attribValue = $itemInfo["$attribName.$strategy"];

      $return[$attribName] = $attribValue;
   }
   
   return $return;
}

function buildItemInfo($itemInfo, $itemInput)
{
   global $constants;

   $return = array();

   $attribList = $constants->attribs();

   $itemLevel = $itemInput['level'];

   foreach ($itemInfo as $attrib => $attribValue) {
      if (!preg_match('/^(.*)\.min$/i',$attrib,$match)) { continue; }

      $attribName  = $match[1];
      $attribValue = $itemInput[$attribName];

      $attribMin    = $itemInfo["$attribName.min"];
      $attribMax    = $itemInfo["$attribName.max"];
      $enhanceCalc  = $attribList[$attribName]['enhance'] ?: array();
      $attribEMax   = $attribMax + (($attribMax * $enhanceCalc['percent']/100) * 10);
      $levelMin     = $attribMin + (($attribMin * $enhanceCalc['percent']/100) * $itemLevel);
      $levelMax     = $attribMax + (($attribMax * $enhanceCalc['percent']/100) * $itemLevel);

      if ($enhanceCalc['round']) { 
         $attribEMax = round($attribEMax); 
         $levelMin   = round($levelMin);
         $levelMax   = round($levelMax);
      }

      $levelPercent = ($attribList[$attribName]['reversed']) ? 100 - ((($attribValue - $levelMax) * 100) / ($levelMin - $levelMax)) 
                                                             : ((($attribValue - $levelMin) * 100) / ($levelMax - $levelMin));

      $return[$attribName] = array(
         'min'           => $attribMin,
         'max'           => $attribMax,
         'emax'          => $attribEMax,
         'level.min'     => $levelMin,
         'level.max'     => $levelMax,
         'level.percent' => $levelPercent,
         'label'         => ucwords(preg_replace('/\./',' ',$attribName)),
         'value'         => (preg_match('/^\s*$/',$attribValue)) ? null : $attribValue,
      );

      $isValid = true;

      if (!is_null($return[$attribName]['value'])) {
         if ($attribList[$attribName]['reversed']) {
            if ($attribValue > $attribMin || $attribValue < $attribEMax) { $isValid = false; }
         }
         else {
            if ($attribValue < $attribMin || $attribValue > $attribEMax) { $isValid = false; }
         }
      }

      $return[$attribName]['valid'] = $isValid;
   }

   $return['level'] = array(
      'min' => 0,
      'max' => 10,
   );

   $return['level']['value'] = $itemLevel;
   $return['level']['valid'] = ($itemLevel < $return['level']['min'] || $itemLevel > $return['level']['max']) ? false : true;

   return $return;
}

function validateItemInput($itemInfo, &$itemInput)
{
   global $constants;

   $errors = array();

   foreach ($itemInfo as $attribName => $attribInfo) {
      if (!$attribInfo['valid']) { $errors[$attribName] = true; }
   }

   return $errors;

}

function getItemInput()
{
   $itemInput = array();

   foreach ($_POST as $name => $value) {
      if (preg_match('/^item_(.*)$/i',$name,$match)) { 
         $attribName = preg_replace('/\-/','.',$match[1]);
         if (preg_match('/^\s*$/',$value)) { continue; }
         $itemInput[$attribName] = $value; 
      }
   }

   return $itemInput;
}

function getItems()
{
   $itemList = array();
   $fileList = glob(APP_CONFIGDIR."/item/*.json");
 
   foreach ($fileList as $fileName) {
       $itemId   = basename($fileName,".json");
       $itemData = json_decode(file_get_contents($fileName),true);

       if (is_null($itemData)) { continue; }

       if ($itemData['hidden']) { continue; }
 
       $itemList[$itemId] = $itemData;
   }

   return $itemList;
}
?>
