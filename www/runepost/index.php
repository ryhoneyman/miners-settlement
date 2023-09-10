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

$main->buildClass('constants','Constants',null,'local/constants.class.php');
$main->buildClass('format','Format',null,'local/format.class.php');

$main->title('Runeposts');

$input = $main->obj('input');
$html  = $main->obj('html');

$selectedItem = $input->get('item','alphanumeric,dash',null);
$clear        = $input->get('clear','alphanumeric');

if ($clear) { $selectedItem = null; }

$main->var('selectedItem',$selectedItem);

include 'ui/header.php';

print "
<style>
.select2-results__option { line-height:1.0; }
.select2-container--default .select2-results>.select2-results__options { max-height: 350px; }
</style>
";

print dataDisplay($main);

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

function dataDisplay($main)
{
   $html = $main->obj('html');

   $return = $html->startForm();

   $main->var('runewordList',getRunewords($main));
   $main->var('runeList',getRunes($main));
   $main->var('gearInfo',getSupportingGearInfo($main));

   // Build the pulldown list of items
   $selectItem = array('' => 'Select an Item');
   $selectOpts = array('class' => 'form-control gear', 'script' => 'onchange="autoChange(this.value)"');
   $gearTypes  = $main->obj('constants')->gearTypes();

   $gearInfo      = $main->var('gearInfo');
   $selectedItem  = $main->var('selectedItem');
   $selectedMatch = (is_null($selectedItem)) ? true : false;

   foreach ($gearInfo as $itemName => $itemData) {
      if ($itemName == $selectedItem) { $selectedMatch = true; }

      $selectItem[$gearTypes[$itemData['type']]][$itemName] = $itemData['label'];
      $selectOpts['data'][$itemName]['image'] = $itemData['image'];
   }
   array_multisort($selectItem);

   // If we received an unknown selected item, just null it
   if (!$selectedMatch) { 
      $selectedItem = null; 
      $main->var('selectedItem',$selectedItem);
   }

   $areaNav = array();
   foreach (array_keys($main->var('runewordList')) as $areaName) {
      $areaNav[] = sprintf("<a href='#area-%s' class='text-green'>%s</a>",strtolower(preg_replace('/\W/','-',$areaName)),$areaName);
   }

   foreach (array_chunk($areaNav,6) as $areaChunk) {
      $return .= '<div>'.implode(" | ",$areaChunk).'</div>';
   }
   
   $return .= '<div class="mb-4"></div>'; 

   $return .= "<div class='input-group mb-4' style='width:fit-content;'>".
              $html->select('item',$selectItem,$selectedItem,$selectOpts).
              "<span class='input-group-append'>".
              $html->submit('select','Select',array('class' => 'btn btn-primary btn-sm')).
              $html->submit('clear','Clear',array('class' => 'btn btn-success btn-sm')).
              "</span></div>";

   foreach ($main->var('runewordList') as $areaName => $areaPosts) {
      $return .= areaDisplay($main,$areaName,$areaPosts);
   }

   $return .= $html->endForm();

   return $return;
}

function areaDisplay($main, $areaName, $areaPosts)
{
   $return = '<div class="row">'.
             '<div class="col-12 col-xl-9 col-lg-10 col-md-12 col-sm-12">'.
             '<div class="card card-outline card-success">'.
             '<div class="card-header"><b class="text-xl" id="area-'.strtolower(preg_replace('/\W/','-',$areaName)).'">'.$areaName.'</b>'.
             '</div>'.
             '<div class="card-body">'.
             '<div class="row">';

   $areaMatched = false;

   foreach ($areaPosts as $postName => $postInfo) {
      $rpDisplay = runepostDisplay($main,$postName,$postInfo);

      if ($rpDisplay) { 
         $return .= $rpDisplay; 
         $areaMatched = true;
      }
   }

   if ($areaMatched === false) { return ''; }

   $return .= '</div>'.
              '</div>'.
              '</div>'.
              '</div>'.
              '</div>';

   return $return;
}

function runepostDisplay($main, $postName, $postInfo)
{
   $return = '<div class="col-12">'.
             '<div class="card card-outline card-warning">'.
             '<div class="card-header"><b class="text-lg">'.$postName.'</b>'.
             '<div class="card-tools"><a href="#"><button type="button" class="btn btn-tool"><i class="fas fa-level-up"></i></button></a></div>'.
             '</div>'.
             '<div class="card-body">'.
             '<table class="table table-striped table-hover" border=0 cellpadding=10>'.
             '<thead><tr class="text-yellow"><th style="width:25%">Name</th><th style="width:25%">Gear</th><th style="width:35%">Effects</th><th style="width:15%">Required Runes</th></tr></thead><tbody>';

   $runeList     = $main->var('runeList');
   $gearInfo     = $main->var('gearInfo');
   $selectedItem = $main->var('selectedItem');

   $itemCount = 0;

   foreach ($postInfo as $entryId => $entryInfo) {
      $runewordName  = $entryInfo['label'];
      $entryCost     = json_decode($entryInfo['cost'],true);
      $entryAttrib   = json_decode($entryInfo['attributes'],true);
      $itemRequired  = json_decode($entryInfo['requires'],true) ?: array('');
      $itemList      = array();
      $filterMatched = (is_null($selectedItem)) ? true : false;

      foreach ($itemRequired as $itemName) {
         if (!is_null($selectedItem) && $itemName == $selectedItem) { $filterMatched = true; }

         $itemLabel  = $gearInfo[$itemName]['label'] ?: 'None';
         $itemImage  = $gearInfo[$itemName]['image'];
         $itemList[] = ($itemImage) ? sprintf("<div class='mb-1'><a href='?item=$itemName'><img src='%s' height=50 data-toggle='tooltip' title=\"%s\"> ".
                                              "<span class='text-green'>$itemLabel</span></a></div>",$itemImage,$itemLabel) : $itemLabel;
      }

      if ($filterMatched === false) { continue; }

      $itemInsert    = implode('',$itemList);
      $requiredRunes = array();
      $runeEffects   = '';

      if ($entryCost['item']) { 
         foreach ($entryCost['item'] as $itemName => $itemCount) { 
            $runeLabel  = $runeList[$itemName]['label'];
            $runeImage  = $runeList[$itemName]['image'];
            $runeInsert = ($runeImage) ? sprintf("<img src='%s' height=25 data-toggle='tooltip' title='%s'>",$runeImage,$runeLabel) : $runeLabel;
 
            for ($count = 0; $count < $itemCount; $count++) { $requiredRunes[] = $runeInsert; } 
         }
      }
 
      if ($entryAttrib) {
         $runeEffects = '<code style="color:#aaaaaa;">'.$main->obj('format')->effects($entryAttrib,true).'</code>'; 
      }

      $itemCount++;

      $return .= sprintf("<tr><td>%s</td><td>%s</td><td>%s</td><td>%s</td></tr>",$runewordName,$itemInsert,$runeEffects,implode('',array_reverse($requiredRunes)));
   }

   $return .= '</tbody></table>'.
              '</div>'.
              '</div>'.
              '</div>';

   if (!$itemCount) { return ''; }

   return $return;
}

function getSupportingGearInfo($main)
{
   $runewordList = $main->var('runewordList');

   if (!$runewordList) { return array(); }

   $gearNames = array();

   foreach ($runewordList as $areaName => $areaPosts) {
      foreach ($areaPosts as $postName => $postInfo) {
         foreach ($postInfo as $runewordId => $runewordInfo) {
            $gearRequired = json_decode($runewordInfo['requires'],true) ?: null;

            if (!is_null($gearRequired)) { foreach ($gearRequired as $gearName) { $gearNames[$gearName]++; } }
         }
      }
   }

   if (!$gearNames) { return array();; }

   $nameList = implode(',',array_map(function($value) { return "'".preg_replace('/[^\w\-]/','',$value)."'"; },
                              array_unique(array_filter(array_keys($gearNames)))));
   $itemList = $main->db()->query("select * from item where name in ($nameList) order by tier asc, ranking asc, name asc",array('keyid' => 'name'));

   return $itemList;
}

function getRunes($main)
{
   $runeList = $main->db()->query("select * from item where type = 'rune' and active = 1",array('keyid' => 'name'));

   return $runeList; 
}

function getRunewords($main)
{
   $runewordList = array();

   $results = $main->db()->query("select rw.*, rp.label as runepost_label, rp.attributes as runepost_attributes, l.area as location_area, l.section as location_section ".
                                 "from runeword rw, runepost rp, location l ".
                                 "where rw.runepost_id = rp.id and rp.location_id = l.id and rw.active = 1 and rp.active = 1 and l.active = 1");

   foreach ($results as $resultId => $resultInfo) {
      $areaName    = $resultInfo['location_area'];
      $sectionName = $resultInfo['location_section'];
      $postLabel   = $resultInfo['runepost_label'];
      $postAttribs = json_decode($resultInfo['runepost_attributes'],true);

      if (!is_null($postAttribs)) { 
         if ($postAttribs['exclusive']) { $postLabel .= " <span class='text-yellow text-sm'>(only applies in this area)</span>"; }
      }

      $runewordList[$areaName][$postLabel][$resultId] = $resultInfo;
   }    

   return $runewordList;
}

?>
