<?php
include_once 'miners-settlement-init.php';
include_once 'local/minersmain.class.php';

$main = new MinersMain(array(
   'debugLevel'     => 9,
   'errorReporting' => true,
   'sessionStart'   => true,
   'memoryLimit'    => null,
   'sendHeaders'    => true,
   'database'       => false,
   'input'          => true,
   'html'           => true,
));

include_once 'local/constants.class.php';
include_once 'local/item.class.php';

//$db        = $main->db();
$input     = $main->obj('input');
$html      = $main->obj('html');
$constants = new Constants($main->debug);

$title    = 'Item';
$subtitle = 'Analytics';

$selectedItem = $input->get('item','alphanumeric,dash');
$select       = ($input->get('select')) ? true : false;
$calculate    = ($input->get('calculate')) ? true : false;
$copy         = ($input->get('copy')) ? true : false;
$save         = ($input->get('save')) ? true : false;
$godRoll      = ($input->get('godroll')) ? true : false;
$randomRoll   = ($input->get('randomRoll')) ? true : false;
$itemSubmit   = ($calculate || $godRoll || $randomRoll || $copy || $save);
$itemInput    = ($select) ? array() : getItemInput();
$inputErrors  = array();

$itemUID = $input->get('uid','alphanumeric');

if ($itemUID) {
   $decodedItem = json_decode(file_get_contents(APP_CONFIGDIR."/copy/$itemUID"),true);

   if (!is_null($decodedItem)) { 
      $selectedItem = $decodedItem['id'];
      $itemInput    = $decodedItem;
      $itemSubmit   = true;
   }
}

$itemList = getItems();
//$itemList = getRunes();

/*
$dbItems = $db->query("select label,id from item");

$statement = "insert into runeword values (NULL,?,?,?,?,?,?,?,now(),now(),1)";
$types     = "sssissi";
$data      = array();
*/

foreach ($itemList as $itemName => $itemInfo) {
   $itemId = $dbItems[$itemInfo['requires']]['id'];
   $data[] = array($itemName,$itemInfo['name'],$itemInfo['description'],$itemId,json_encode($itemInfo['cost']),json_encode($itemInfo['attribs']),$itemInfo['location']);
}

//var_dump("<pre>",$data); exit;

//$result = $db->bindExecute($statement,$types,$data);

//var_dump(json_encode($result),$db->error()); exit;

$itemBase = $itemList[$selectedItem] ?: null;     // Base is the raw data for the item

// Build the pulldown list of items
$selectItem = array();
foreach ($itemList as $itemId => $itemData) { 
   $selectItem[ucfirst($itemData['type'])][$itemId] = $itemData['name']; 
}
asort($selectItem);

// If there was no input and Calculate was pressed, just do a random roll.
if (!$itemInput && $calculate) { $randomRoll = true; }

if ($godRoll)         { $itemInput = setItemValues($itemBase,'max'); }
else if ($randomRoll) { $itemInput = randomItemValues($selectedItem); }

$itemInfo = buildItemInfo($itemBase,$itemInput);  // Info is the raw + user data + validity

if ($selectedItem && $calculate) {
   $inputErrors = validateItemInput($itemInfo,$itemInput);
}

$itemValid = ($selectedItem && !$inputErrors && $itemSubmit) ? true : false;

if ($itemValid) {
   ksort($itemInput);
   $jsonData   = json_encode(array_diff_key(array_merge(array('id' => $selectedItem),$itemInput),array_flip(array('name','type','description','image'))));
   $itemHash   = hash("crc32",$jsonData);
   $hashFile   = APP_CONFIGDIR."/copy/$itemHash";

   if ($copy && !file_exists($hashFile)) { file_put_contents($hashFile,$jsonData); }

   if ($save) { }
}

include 'ui/header.php';

?>


<div class='row'>
   <div class='col-6 mb-3'>
   <?php
      print $html->startForm(array('method' => 'post'));
      print "<div class='input-group'>";
      print $html->select('item',$selectItem,$selectedItem,array('class' => 'foo', 'style' => 'width:auto;'));
      print "<span class='input-group-append'>";
      print $html->submit('select','Select',array('class' => 'btn btn-primary btn-sm'));
      print "</span></div>";
      print $html->inputHidden('uid','');
   ?>
   </div>
   <div class='col-6 col-lg-4 col-sm-1 col-md-6 col-xl-4'>
      <?php if ($itemValid) { ?>
        <?php print $html->submit('save',"Save",array('class' => 'float-right btn btn-primary btn-sm')); ?>
        <input type="submit" name="copy" value="Copy" class="mr-2 float-right btn btn-success btn-sm copy-btn" data-clipboard="<?php print $itemHash; ?>">
      <?php } ?>
   </div>
</div>
<script src="/assets/js/copytoclipboard.js"></script>

<div class="row">
   <div class="col-12 col-xl-3 col-lg-6 col-md-6 col-sm-12">
       <div class="card card-outline card-success">
          <div class="card-header"><b>Item Ranges</b></div>
          <div class="card-body">
          <?php print itemRangeDisplay($itemBase); ?>
          </div>
      </div>
   </div>

   <div class="col-12 col-xl-3 col-lg-6 col-md-6 col-sm-12">
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

   <div class="col-12 col-xl-4 col-lg-6 col-md-12 col-sm-12">
      <div class="card card-outline card-secondary">
          <div class="card-header"><b>Item Quality</b></div>
          <div class="card-body">
          <?php
             if ($itemValid) {
                print itemInputQuality($itemInfo);
             }
          ?>
          </div>
      </div>
      <div class="card card-outline card-secondary">
          <div class="card-header"><b>Item Overall</b></div>
          <div class="card-body">
          <?php
             if ($itemValid) {
                print itemInputOverall($selectedItem,$itemBase,$itemInfo);
             }
          ?>
          </div>
      </div>
   </div>
</div>
<div class="row">
   <div class="col-12 col-xl-10 col-lg-10 col-md-12 col-sm-12">
       <div class="card card-outline card-danger">
          <div class="card-header"><b>Item Results</b></div>
          <div class="card-body">
          <?php if ($selectedItem && isset($itemInput['level']) && !$inputErrors) { print itemResultsDisplay($selectedItem,$itemInput); } ?>
          </div>
      </div>
   </div>
</div>

<?php
print $html->endForm();

include 'ui/footer.php';

print "<script>window.history.replaceState(null, null, window.location.pathname);</script>\n";

?>
<?php

function itemResultsDisplay($selectedItem, $itemInput)
{
   global $main, $constants;

   $item = new Item($main->debug);

   $item->load($selectedItem);

   $item->generate($itemInput,array('level' => $itemInput['level']));

   $return = "<div class='row'>";

   for ($level = 0; $level <= $constants->maxEnhanceLevel(); $level++) {
      $item->enhance(0);
      $item->enhance($level);
      $return .= "<div class='card col-9 col-xl-3 col-lg-4 col-md-6 col-sm-9'>".itemDisplay($item)."</div>";
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

   $return = "";

   $percentColors = $constants->percentColors();

   $weights = json_decode(file_get_contents(APP_CONFIGDIR.'/weights.json'),true);

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

      $return .= sprintf("<div class='progress-group'><span class='progress-text' style='font-size:0.8em;'>%s</span><span class='float-right' style='font-size:0.8em;'>%d%%</span>".
                         "<div class='progress progress-sm'><div class='progress-bar' style='background-color:%s; width:%d%%;'></div></div></div>",
                         $labelName,$labelPercent,$labelColor,$labelPercent);
   }

   return $return;
}

function itemInputQuality($itemInfo)
{
   global $constants;

   if (!$itemInfo) { return ''; }

   $return = "";

   $percentColors = $constants->percentColors();

   foreach ($itemInfo as $attribName => $attribInfo) {
      if ($attribName == 'level' || !$attribInfo['value']) { continue; }

      $attribLabel  = $attribInfo['label'];
      $levelPercent = sprintf("%d",$attribInfo['level.percent']);

      foreach ($percentColors as $percent => $percentInfo) {
         if ($levelPercent <= $percent) { $percentColor = $percentInfo['color']; break; }
      }

      $return .= sprintf("<div class='progress-group'><span class='progress-text' style='font-size:0.8em;'>%s</span><span class='float-right' style='font-size:0.8em;'>%d%%</span>".
                         "<div class='progress progress-sm'><div class='progress-bar' style='background-color:%s; width:%d%%;'></div></div></div>",
                         $attribLabel,$levelPercent,$percentColor,$levelPercent);
   }

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
             "<td><div class='input-group input-group-sm'>".$html->inputText("item_level",$itemInput["level"],10,8,$attribOptions)."</div></td>".
             "<td class='ia-input-line'>(0-".$constants->maxEnhanceLevel().")</td>".
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
                 "<td><div class='input-group input-group-sm'>".$html->inputText($inputName,$itemInput[$attribName],10,8,$attribOptions)."</div></td>".
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

   $elementDisplay = $constants->elementDisplay();

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
      if (!$itemElements[$feature]) { continue; }
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

   $attribDisplay = $constants->attribDisplay();

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
   global $main;

   $item = new Item($main->debug);

   $item->load($itemId);

   $item->generate();

   return $item->export();
}

function setItemValues($itemInfo, $strategy)
{
   $return = array('level' => '0');

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

   if (!$itemInfo) { return $return; }

   $attribList = $constants->attribs();

   $itemLevel = $itemInput['level'];

   foreach ($itemInfo as $attrib => $attribValue) {
      if (!preg_match('/^(.*)\.min$/i',$attrib,$match)) { continue; }

      $attribName  = $match[1];
      $attribValue = $itemInput[$attribName];

      $attribMin    = $itemInfo["$attribName.min"];
      $attribMax    = $itemInfo["$attribName.max"];
      $enhanceCalc  = $attribList[$attribName]['enhance'] ?: array();
      $attribEMax   = $attribMax + (($attribMax * $enhanceCalc['percent']/100) * $constants->maxEnhanceLevel());
      $levelMin     = $attribMin + (($attribMin * $enhanceCalc['percent']/100) * $itemLevel);
      $levelMax     = $attribMax + (($attribMax * $enhanceCalc['percent']/100) * $itemLevel);

      if ($enhanceCalc['round']) { 
         $attribEMax = round($attribEMax); 
         $levelMin   = round($levelMin);
         $levelMax   = round($levelMax);
      }

      $levelPercent = ($levelMin == $levelMax) ? 100 : 
                      (($attribList[$attribName]['reversed']) ? 100 - ((($attribValue - $levelMax) * 100) / ($levelMin - $levelMax)) 
                                                              : ((($attribValue - $levelMin) * 100) / ($levelMax - $levelMin)));

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

function getRunes()
{
   $itemList = array();
   $fileList = glob(APP_CONFIGDIR."/rune/*.json");

   foreach ($fileList as $fileName) {
       $itemId   = basename($fileName,".json");
       $itemData = json_decode(file_get_contents($fileName),true);

       if (is_null($itemData)) { continue; }

       if ($itemData['hidden']) { continue; }

       $itemList[$itemId] = $itemData;
   }

   return $itemList;
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
