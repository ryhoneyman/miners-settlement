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
   'toastr'         => true,
));

$main->buildClass('constants','Constants',null,'local/constants.class.php');
$main->buildClass('item','Item',null,'local/item.class.php');

$main->title('Item Analytics');

$input  = $main->obj('input');
$html   = $main->obj('html');
$toastr = $main->obj('toastr');

$selectedItem = $input->get('item','alphanumeric,dash');
$player       = $input->get('player','alphanumeric,dot,dash,underscore,space');
$select       = ($input->get('select')) ? true : false;
$calculate    = ($input->get('calculate')) ? true : false;
$share        = ($input->get('share')) ? true : false;
$equip        = ($input->get('equip')) ? true : false;
$save         = ($input->get('save')) ? true : false;
$godRoll      = ($input->get('godroll')) ? true : false;
$randomRoll   = ($input->get('randomRoll')) ? true : false;
$itemSubmit   = ($calculate || $godRoll || $randomRoll || $share || $save);
$itemInput    = (!$itemSubmit) ? array() : getItemInput();
$inputErrors  = array();

// Allow inbound item link and player gear decoding for quick hash
if (preg_match('/^((il|pg)\w{8})$/i',$_SERVER['QUERY_STRING'],$match)) {
   $itemHash   = $match[1];
   $loadType   = strtolower($match[2]);
   $itemResult = ($loadType == 'il') ? $main->getItemLink($itemHash) : $main->getGear($itemHash);

   if ($itemResult) { 
      $selectedItem = $itemResult['item_name'];
      $itemInput    = $itemResult['stats'];
      $itemSubmit   = true;
   }
}

$itemList = getGear($main);
$itemBase = $itemList[$selectedItem] ?: null;     // Base is the raw data for the item

$main->var('itemList',$itemList);

// Build the pulldown list of items
$selectItem = array('' => 'Select an Item');
$selectOpts = array('class' => 'form-control gear', 'script' => 'onchange="autoChange(this.value);"');
$gearTypes  = $main->obj('constants')->gearTypes();

foreach ($itemList as $itemName => $itemData) { 
   $selectItem[$gearTypes[$itemData['type']]][$itemName] = $itemData['label']; 
   $selectOpts['data'][$itemName]['image'] = $itemData['image'];
}
ksort($selectItem);

// If there was no input and Calculate was pressed, just do a random roll.
if (!$itemInput && $calculate) { $randomRoll = true; }

if ($godRoll)         { $itemInput = setItemValues($itemBase,'max'); }
else if ($randomRoll) { $itemInput = randomItemValues($selectedItem); }

// Handle non-dotted decimals used in parts of the world with commas.
if (preg_match('/,/',$itemInput['speed'])) { $itemInput['speed'] = str_replace(',','.',$itemInput['speed']); }

$itemInfo = buildItemInfo($itemBase,$itemInput);  // Info is the raw + user data + validity

//@file_put_contents('/tmp/ia.debug.json',json_encode(array('post' => $_POST, 'get' => $_GET, 'server' => $_SERVER, 'selectedItem' => $selectedItem, 'itemBase' => $itemBase, 'itemInfo' => $itemInfo),JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT));

if ($selectedItem && $calculate) {
   $inputErrors = validateItemInput($itemInfo,$itemInput);
}

$itemValid = ($selectedItem && !$inputErrors && $itemSubmit) ? true : false;

if ($itemValid) {
   $itemHash    = $main->hashItemLink($selectedItem,$itemInput);
   $itemLinkUrl = $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'].preg_replace('/\?.*$/','',$_SERVER['REQUEST_URI'])."?$itemHash";

   if ($share)     { $shareResult = $main->saveItemLink($selectedItem,$itemInput); }
   else if ($save) { $saveResult  = $main->saveGear($itemBase['id'],$selectedItem,$itemInput); }
}

include 'ui/header.php';

print "
<style>
.select2-results__option { line-height:1.0; }
.select2-container--default .select2-results>.select2-results__options { max-height: 350px; }
</style>
";

// Need Toastr loaded from header first to process these alerts.
if ($share) {
   if ($shareResult) { $toastr->success('Item link copied to clipboard!'); }
   else              { $toastr->failure('Could not generate item link!'); }
}
else if ($save) {
   if ($saveResult) { $toastr->success('Item saved to profile!'); }
   else             { $toastr->failure('Could not save item to profile!'); }
}


print $html->startForm(array('method' => 'post'));

?>

<div class='row'>
   <div class='col-12 col-sm-9 col-md-6 col-lg-6 col-xl-3 mb-3'>
   <?php
      print $html->startForm(array('method' => 'post'));
      print "<div class='input-group' style='width:fit-content;'>";
      print $html->select('item',$selectItem,$selectedItem,$selectOpts);
      print "<span class='input-group-append'>";
      print $html->submit('select','Select',array('class' => 'btn btn-primary btn-sm'));
      print "</span></div>";
      print $html->inputHidden('uid','');
   ?>
   </div>
   <div class='col-12 col-sm-9 col-md-6 col-lg-6 col-xl-3 mb-3'>
   </div>
   <div class='col-12 col-sm-9 col-md-6 col-lg-6 col-xl-3 mb-3'>
      <?php
         if ($itemValid) {
            print $html->submit('save',"Save",array('class' => 'btn btn-primary btn-sm mr-2'));
            print $html->submit('share','Share',array('class' => 'mr-2 btn btn-success btn-sm copy-btn', 'data-clipboard' => $itemLinkUrl));
         }
      ?>
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
<!--
      <div class="card card-outline card-secondary">
          <div class="card-header"><b>Item Overall</b></div>
          <div class="card-body">
          <?php
             if ($itemValid) {
                //print itemInputOverall($selectedItem,$itemBase,$itemInfo);
             }
          ?>
          </div>
      </div>
-->
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

print "
<script type='text/javascript'>
   $('.gear').select2({
      templateSelection: select2_template,
      templateResult: select2_template,
   });
</script>
";

include 'ui/footer.php';

print "<script>window.history.replaceState(null, null, window.location.pathname);</script>\n";

?>
<?php

function itemResultsDisplay($selectedItem, $itemInput)
{
   global $main;

   $itemList = $main->var('itemList');
   $item     = $main->obj('item');

   $item->import($itemList[$selectedItem]);

   $item->generate($itemInput,array('level' => $itemInput['level']));

   $return = "<div class='row'>";

   for ($level = 0; $level <= $main->obj('constants')->maxEnhanceLevel(); $level++) {
      $item->enhance(0);
      $item->enhance($level);
      $return .= "<div class='card col-9 col-xl-3 col-lg-4 col-md-6 col-sm-9'>".itemDisplay($item)."</div>";
   }

   $return .= "</div>";

   return $return;
}

function itemDisplay($item)
{
   $itemStats = $item->export();

   if (!$itemStats) { return ''; }

   $itemLevel   = $item->level();
   $itemDesc    = $item->description() ?: 'USE THIS ITEM TO FIGHT THE EVIL THAT IS LURKING OUTSIDE THE VILLAGE';
   $itemImage   = $item->image() ?: '/images/item/none.png';
   $levelCircle = ($itemLevel) ? "<span style='position:relative; left:-7px; bottom:20px; height:15px; width:15px; ".
                                 "background-color:#000; color:#fff; border-radius:50%; font-size:0.8em; display:inline-block; ".
                                 "text-align:center; line-height:10px;'>$itemLevel</span>" : '';

   $return = "<table border=0 style='background:#214268; border-radius:10px;'>".
             "<tr><td colspan=4 style='color:#fff; background:#454545; text-align:left; border-radius:10px; padding:5px;'>".$item->label()."</td></tr>";

   $return .= "<tr><td style='padding:5px; margin-left:auto; margin-right:auto;'><span><img src='$itemImage' style='width:auto; height:auto; max-width:50px;'>$levelCircle</span></td>".
              "<td colspan=3 style='padding:10px;'><table border=0><tr><td style='color:#fff; background:#454545; font-size:0.6em;'>$itemDesc</td></tr></table></td></tr>";

   $return .= itemDisplayPrimary($itemStats);
   $return .= itemDisplayElements($itemStats,null,array('background' => '#0a1833'));

   $return .= "</table>";

   return $return;
}

function itemInputOverall($itemName, $itemBase, $itemInfo)
{
   global $main;

   if (!$itemInfo) { return ''; }

   $return = "";

   $percentColors = $main->obj('constants')->percentColors();

   $weights = json_decode(file_get_contents(APP_CONFIGDIR.'/weights.json'),true);

   $attribList = $main->obj('constants')->attribs();
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
         $weightValues = $typePurposeInfo['list'][$itemName] ?: $typePurposeInfo['list']['default'];

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
   global $main;

   if (!$itemInfo) { return ''; }

   $return = "";

   $percentColors = $main->obj('constants')->percentColors();

   foreach ($itemInfo as $attribName => $attribInfo) {
      if ($attribName == 'level' || !isset($attribInfo['value'])) { continue; }

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
   global $main;

   $html = $main->obj('html');

   if (!$itemInfo) { return ''; }

   $attribList = $main->obj('constants')->attribs();

   $inputOptions  = array('params' => array('class' => 'form-control'));
   $attribOptions = $inputOptions;

   if ($inputErrors['level']) { $attribOptions['class'] .= 'form-control is-invalid'; }

   $return = "<table border=0 cellpadding=5>".
             "<tr>".
             "<td class='ia-input-line'><b>Level</b></td>".
             "<td><div class='input-group input-group-sm'>".$html->inputText("item_level",$itemInput["level"],10,8,$attribOptions)."</div></td>".
             "<td class='ia-input-line'>(0-".$main->obj('constants')->maxEnhanceLevel().")</td>".
             "</tr>";

   foreach ($itemInfo as $attribName => $attribInfo) {
      if ($attribName == 'level') { continue; }

      $attribOptions = $inputOptions;

      if ($inputErrors[$attribName]) { $attribOptions['class'] .= 'form-control is-invalid'; }

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

   $return = "<table border=0 style='background:#ddaa99'>".
             "<tr><td colspan=4 style='text-align:center;'>FROM</td></tr>";

   $return .= itemDisplayPrimary($itemInfo,'min');

   $return .= itemDisplayElements($itemInfo,'min'); 

   $return .= "<tr><td colspan=4 style='text-align:center;'>TO</td></tr>";

   $return .= itemDisplayPrimary($itemInfo,'max');

   $return .= itemDisplayElements($itemInfo,'max'); 

   $return .= "</table>";

   $combinations = null;

   foreach ($itemInfo as $attribName => $attribInfo) {
      if (preg_match('/^(\S+)\.min$/i',$attribName,$match)) {
         $baseAttrib = $match[1];
         if (!isset($itemInfo["$baseAttrib.max"])) { $combinations = null; break; }

         $delta = abs($itemInfo["$baseAttrib.max"] - $attribInfo) * (($baseAttrib == 'speed') ? 100 : 1);

         if ($delta === 0) { $delta = 1; }

         $combinations = (is_null($combinations)) ? $delta : $combinations * $delta;
      }
   } 

   if (!is_null($combinations)) { $return .= "<div class='mt-3 text-sm text-warning'>The item has ".number_format($combinations)." possible combinations.</div>"; }

   return $return;
}

function itemDisplayElements($itemInfo, $limit = null, $options = null)
{
   global $main;

   $limit = (is_null($limit)) ? '' : ".$limit";

   $background = $options['background'] ?: '#6f4e37';

   $elementAttribs = $main->obj('constants')->elementAttribs();

   $return = "<tr>".
             "<td colspan=4 style='background:$background; text-align:center;'>";

   foreach ($elementAttribs as $attribName => $attribInfo) {
      if (!array_key_exists("$attribName$limit",$itemInfo)) { continue; }

      $attribValue = $itemInfo["$attribName$limit"];

      $return .= sprintf("<span class='%s'>%s: %s <i class='fa %s'></i></span><br>",$attribInfo['color'],$attribInfo['text'],$attribValue,$attribInfo['icon']);
   }

   $return .= "</td>".
              "</tr>";

   return $return;
}

function itemDisplayPrimary($itemInfo, $limit = null)
{
   global $main;

   $limit = (is_null($limit)) ? '' : ".$limit";

   $primaryAttribs = $main->obj('constants')->primaryAttribs();

   $return = "<tr>";

   foreach ($primaryAttribs as $attribName => $attribInfo) {
      $attribValue = $itemInfo["$attribName$limit"] ?: 0;

      $return .= sprintf("<td width=75px class='%s' style='padding:5px;'>%s <i class='float-right fa %s'></i></td>",$attribInfo['background'],$attribValue,$attribInfo['icon']);
   }
   
   $return .= "</tr>";

   return $return;
}

function randomItemValues($itemName)
{
   global $main;

   $itemList = $main->var('itemList');
   $item     = $main->obj('item');

   $item->import($itemList[$itemName]);

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
   global $main;

   $return = array();

   if (!$itemInfo) { return $return; }

   $attribList = $main->obj('constants')->attribs();
   $itemLevel  = $itemInput['level'];

   foreach ($itemInfo as $attrib => $attribValue) {
      if (!preg_match('/^(.*)\.min$/i',$attrib,$match)) { continue; }

      $attribName  = $match[1];
      $attribValue = $itemInput[$attribName];

      $attribMin    = $itemInfo["$attribName.min"];
      $attribMax    = $itemInfo["$attribName.max"];
      $enhanceCalc  = $attribList[$attribName]['enhance'] ?: array();
      $attribEMax   = $attribMax + (($attribMax * $enhanceCalc['percent']/100) * $main->obj('constants')->maxEnhanceLevel());
      $levelMin     = $attribMin + (($attribMin * $enhanceCalc['percent']/100) * $itemLevel);
      $levelMax     = $attribMax + (($attribMax * $enhanceCalc['percent']/100) * $itemLevel);

      if ($enhanceCalc['round']) { 
         $attribEMax   = $main->fvRound($attribEMax); 
         $levelMin     = $main->fvRound($levelMin);
         $levelMax     = $main->fvRound($levelMax);
      }

      $levelPercent = ($levelMin == $levelMax) ? 100 : 
                      (($attribList[$attribName]['reversed']) ? 100 - ((($attribValue - $levelMax) * 100) / ($levelMin - $levelMax)) 
                                                              : ((($attribValue - $levelMin) * 100) / ($levelMax - $levelMin)));

      // We allow for the user to provide one less and one more on values to counter rounding issues, so we need to cap percentages 
      if ($levelPercent < 0)        { $levelPercent = 0; }
      else if ($levelPercent > 100) { $levelPercent = 100; }

      $return[$attribName] = array(
         'min'           => $attribMin,
         'max'           => $attribMax,
         'emax'          => $attribEMax,
         'level.min'     => $levelMin,
         'level.max'     => $levelMax,
         'level.percent' => $levelPercent,
         'label'         => ucwords(preg_replace('/\-/',' ',$attribName)),
         'value'         => (preg_match('/^\s*$/',$attribValue)) ? null : $attribValue,
      );

      $isValid = true;

      if (!is_null($return[$attribName]['value'])) {
         if ($attribList[$attribName]['reversed']) {
            if ($attribValue > $levelMin || $attribValue < $levelMax) { $isValid = false; }
         }
         else {
            if ($attribValue < $levelMin || $attribValue > $levelMax) { $isValid = false; }
         }
      }

      $return[$attribName]['valid'] = $isValid;
   }

   $return['level'] = array(
      'min' => 0,
      'max' => $main->obj('constants')->maxEnhanceLevel(),
   );

   $return['level']['value'] = $itemLevel;
   $return['level']['valid'] = ($itemLevel < $return['level']['min'] || $itemLevel > $return['level']['max']) ? false : true;

   return $return;
}

function validateItemInput($itemInfo, &$itemInput)
{
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
         $attribName = $match[1];

         if (preg_match('/^\s*$/',$value)) { continue; }

         $itemInput[$attribName] = $value; 
      }
   }

   return $itemInput;
}

function getGear($main)
{
   $return       = array();
   $itemGearList = $main->getItemGearListByType();

   foreach ($itemGearList as $gearType => $gearItems) {
      foreach ($gearItems as $resultId => $resultInfo) {
         $gearData = json_decode($resultInfo['attributes'],true);

         $gearData['id']    = $resultInfo['id'];
         $gearData['name']  = $resultInfo['name'];
         $gearData['label'] = $resultInfo['label'];
         $gearData['type']  = $resultInfo['type'];
         $gearData['image'] = $resultInfo['image'];

         $return[$gearData['name']] = $gearData;
      }
   }

   return $return;
}

?>
