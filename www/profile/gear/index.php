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

$main->title('Gear Management');

$input      = $main->obj('input');
$alte       = $main->obj('adminlte');
$deleteGear = $input->get('delete','alphanumeric,dot,dash,underscore,space');

if ($deleteGear) {
   if ($main->deleteGear($deleteGear)) { $main->redirect($main->pageUri); }
} 

$errors = $main->error();

include 'ui/header.php';

if ($errors) { print $alte->displayRow($alte->errorCard($errors)); }

print aboutDisplay($main).
      gearDisplay($main).
      insertModal();

include 'ui/footer.php';

?>
<?php

function gearDisplay($main)
{
   $alte          = $main->obj('adminlte');
   $constants     = $main->obj('constants');
   $gearList      = $main->getPlayerGearListByType();
   $gearListItems = array();

   foreach ($constants->gearTypes() as $gearType => $gearTypeLabel) {
      $gearTypeList = $gearList[$gearType];
   
      if ($gearTypeList) {

         foreach ($gearTypeList as $gearId => $gearInfo) {
            $gearLabel   = $gearInfo['label'];
            $gearImage   = ($gearInfo['image']) ? sprintf("<img src='%s' height=50>",$gearInfo['image']) : '';
            $gearAttribs = json_decode($gearInfo['stats'],true);
            $gearLevel   = $gearAttribs['level'];
            $gearPrimary = '';
            $gearElement = '';
   
            foreach ($constants->primaryAttribs() as $attribName => $attribInfo) {
               $rangeFormat = ($attribName == 'speed') ? '%1.2f' : '%d';
               $gearPrimary .= sprintf("<span class='badge %s' style='width:90px;'>$rangeFormat <i class='fas %s float-right'></i></span> ",
                                       $attribInfo['background'],$gearAttribs[$attribName],$attribInfo['icon']);
            }
   
            foreach ($constants->elementAttribs() as $elementName => $elementInfo) {
               if (!isset($gearAttribs[$elementName])) { continue; }
   
                  $gearElement .= sprintf("<span class='%s'>%s: %d <i class='fa %s'></i></span><br>",
                                          $elementInfo['color'],$elementInfo['text'],$gearAttribs[$elementName],$elementInfo['icon']);
            }
   
            $levelBadge   = sprintf("<span class='badge' style='width:25px; background:black; color:white;'>%s</span>",$gearLevel);
            $deleteButton = sprintf("<button class='btn btn-tool fa fa-trash-alt text-danger open-modal' data-name='%s' data-value='%s' ".
                                    "data-toggle='modal' data-target='#modal-window' title='Delete Gear' style='border:none;'></button>",
                                    $gearInfo['label'],$gearInfo['item_hash']); 

            $gearEntry = sprintf("<tr><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td></tr>",
                                 $deleteButton,$gearImage,$gearLabel,$levelBadge,$gearPrimary,$gearElement,
                                 "<a href='/item/analytics/?".$gearInfo['item_hash']."'><i class='fas fa-analytics text-white'</i></a>");

            $gearListItems['All'][] = $gearEntry;
            $gearListItems[$gearTypeLabel][] = $gearEntry;
         }
         
      }
      else { $gearListItems[$gearTypeLabel][] = "<tr><td colspan=6>No $gearTypeLabel items found on this profile.</td></tr>"; }
   }

   $tabs = array();

   foreach ($gearListItems as $gearTypeLabel => $gearContentList) {
      $gearContent = "<table border=0 class='table' style='width:auto;'>".
                     "<thead><tr><th></th><th></th><th>Name</h><th>Level</th><th>Stats</th><th></th><th></th></tr></thead>".
                     "<tbody>".
                     implode("\n",$gearContentList).
                     "</tbody>".
                     "</table>";
         
      $tabs[] = array('name' => $gearTypeLabel, 'data' => $gearContent);
   }

   return $alte->displayRow($alte->displayTabbedCard($tabs,array('title' => 'Gear List', 'container' => 'col-12', 'card' => 'card-success')));
}

function aboutDisplay($main)
{
   $html = $main->obj('html');
   $alte = $main->obj('adminlte');

   return "<p class='text-yellow'>This tool is used to view and manage gear added to your profile.</p>";
}

function insertModal()
{
   return "<div class='modal fade' id='modal-window' aria-hidden='true' style='display:none;'>\n".
          "<div class='modal-dialog modal-sm'>\n".
          "<div class='modal-content'><form>\n".
          "<div class='modal-header'><h4 class='modal-title'>Confirm Delete?</h4>\n".
          "  <button type='button' class='close' data-dismiss='modal' aria-label='Close'><span aria-hidden='true'>&times;</span></button>\n".
          "</div>\n".
          "<div class='modal-body' id='modal-body'>\n".
          "</div>\n".
          "<div class='modal-footer justify-content-between'>\n".
          "  <button type='button' class='btn btn-default' data-dismiss='modal'>No</button>\n".
          "  <button type='submit' class='btn btn-primary'>Yes</button>\n".
          "</div>\n".
          "</form></div><!-- /.modal-content -->\n".
          "</div><!-- /.modal-dialog -->\n".
          "</div> <!-- /.modal -->\n\n".
          "<script>\n".
          "  $(document).on('click', '.open-modal', function() {\n".
          "     var deleteName  = $(this).data('name');\n".
          "     var deleteValue = $(this).data('value');\n".
          "     var modalHTML   = \"<p>Are you sure you want to delete \"+deleteName+\"?<input type='hidden' name='delete' id='delete' value='\"+deleteValue+\"'/>\";\n".
          "     $('#modal-body').html(modalHTML);\n".
          "     $('#modal-window').modal('show');\n".
          "  });\n".
          "</script>\n";
}

?>
