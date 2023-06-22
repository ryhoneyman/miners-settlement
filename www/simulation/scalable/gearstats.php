<?php
include_once 'miners-settlement-init.php';
include_once 'local/minersmain.class.php';

$main = new MinersMain(array(
   'debugLevel'     => 0,
   'errorReporting' => false,
   'sessionStart'   => array('read_and_close' => true),
   'memoryLimit'    => null,
   'sendHeaders'    => true,
   'database'       => true,
   'input'          => true,
   'html'           => true,
   'adminlte'       => true,
));

$main->buildClass('constants','Constants',null,'local/constants.class.php');

$input = $main->obj('input');
$html  = $main->obj('html');

$main->var('sessionName','scalablesim/pageInput');

print pageDisplay($main);

?>
<?php

function pageDisplay($main)
{
   $html       = $main->obj('html');
   $alte       = $main->obj('adminlte');
   $constants  = $main->obj('constants');
   $attribList = $constants->attribs();
   $pageInput  = $main->sessionValue($main->var('sessionName'));

   $clearStats = $main->obj('input')->get('clear','alphanumeric');
   $gearType   = $main->obj('input')->get('type','alphanumeric,dash');
   $gearHash   = $main->obj('input')->get('hash','alphanumeric');
   $hashLookup = $main->getItemGearHashList();
   $gearId     = $hashLookup[$gearHash] ?: null;

   if ($clearStats == 'true') { $pageInput = array(); }

   if (!$gearId) { return ''; }

   $itemInfo = $main->getItemById($gearId);

   if (!$itemInfo) { return ''; }

   $itemStats = json_decode($itemInfo['attributes'],true);

   $percentList = range(0,100,10);
   $statList    = array();

   foreach ($itemStats as $attribKey => $attribValue) {
      if (preg_match('/^(\S+)\.min$/i',$attribKey,$match)) {
         $attribName = $match[1];
 
         // If max = min, there's nothing to change, so we skip it.
         if ($itemStats["$attribName.max"] == $attribValue) { continue; }

         $attribPercentList = array();

         foreach ($percentList as $percentage) {
            $attribPercentList['percent-'.$percentage] = "$percentage%";
          }

         $statList[$attribName] = array_merge(array('' => '%'),$attribPercentList);
      }
   }

   $levelName = sprintf("%s_level",$gearType);
   $baseName  = sprintf("%s_base",$gearType);

   $baseOpts    = array('class' => 'form-control stats', 'data' => array('' => array('css' => 'text-sm')));
   $selectGroup = array($html->select($levelName,array_merge(array('' => 'Level'),range(0,$main->obj('constants')->maxEnhanceLevel())),$pageInput[$levelName],$baseOpts),
                        $html->select($baseName,array_merge(array('' => 'Base %'),$attribPercentList),$pageInput[$baseName],$baseOpts));

   foreach ($attribList as $attribName => $attribInfo) {
      if (!array_key_exists($attribName,$statList)) { continue; }

      $attribIconList = ($attribInfo['icon-combo']) ? $attribInfo['icon-combo'] : $attribName;
      $attribIconData = $constants->buildAttribIconClass($attribIconList);
      $selectOpts     = $baseOpts;

      $selectOpts['data']['']['icon'] = $attribIconData;

      foreach (range(0,100,10) as $percentage) { 
         $selectOpts['data']['percent-'.$percentage] = array('icon' => $attribIcon, 'css' => 'text-sm'); 
      }

      $typeName = sprintf("%s_%s",$gearType,$attribName);

      $selectGroup[] = $html->select($typeName,$statList[$attribName],$pageInput[$typeName],$selectOpts);
   }

   return implode('',$selectGroup);
}

?>
