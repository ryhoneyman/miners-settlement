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

$main->title('Gear Management');

$input = $main->obj('input');

$main->buildClass('constants','Constants',null,'local/constants.class.php');

$main->fetchPlayerGearList();

$deleteGear = $input->get('delete','alphanumeric,dot,dash,underscore,space');

if ($deleteGear) {
   if ($main->deleteGear($deleteGear)) { header("Location: ".$main->pageUri); exit; }
} 

$errors = $main->error();

include 'ui/header.php';


if ($errors) { 
   print displayRow(errorCard($errors));
}

gearDisplay($main);

insertModal();

include 'ui/footer.php';

?>
<?php

function gearDisplay($main)
{
   $db        = $main->db();
   $html      = $main->obj('html');
   $constants = $main->obj('constants');
   $userId    = $main->userId;
   $gearList  = $main->var('playerGearList');

   $gearContent = "<table border=0 class='table' style='width:auto;'>".
                  "<thead><tr><th></th><th></th><th>Name</h><th>Level</th><th>Stats</th><th></th><th></th></tr></thead>".
                  "<tbody>";

   if ($gearList) {
      foreach ($gearList as $gearId => $gearInfo) {
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

         $levelBadge = sprintf("<span class='badge' style='width:25px; background:black; color:white;'>%s</span>",$gearLevel);
         //$levelBadge = sprintf("<i class='fas fa-minus text-sm'></i> <span class='badge' style='width:25px; background:black; color:white;'>%s</span> <i class='fas fa-plus text-sm'></i>",$gearLevel);

         $deleteButton = sprintf("<button class='btn btn-tool fa fa-trash-alt text-danger open-modal' data-name='%s' data-value='%s' ".
                                 "data-toggle='modal' data-target='#modal-window' title='Delete Gear' style='border:none;'></button>",
                                 $gearInfo['label'],$gearInfo['item_hash']); 
         $gearContent .= sprintf("<tr><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td></tr>",
                                 $deleteButton,$gearImage,$gearLabel,$levelBadge,$gearPrimary,$gearElement,
                                 "<a href='/item/analytics/?".$gearInfo['item_hash']."'><i class='fas fa-analytics text-white'</i></a>");
      }
   }
   else { $gearContent .= "<tr><td colspan=6>No gear found on this profile.</td></tr>"; }

   $gearContent .= "</tbody>".
                   "</table>";

   print displayRow(displayCard(array('header' => 'Gear List', 'container' => 'col-12', 'card' => 'card-success'),$gearContent));
}

function displayRow($content)
{
   return "<div class='row'>".
          $content.
          "</div>";
}

function errorCard($message, $options = null)
{
   $containerClass = $options['container'] ?: 'col-12 col-xl-3 col-lg-6 col-md-6 col-sm-12';;
   $cardClass      = $options['card'] ?: 'card-danger';
   $cardTitle      = $options['title'] ?: 'Error';

   return "<div class='$containerClass'>".
          "    <div class='card $cardClass'>".
          "       <div class='card-header'><b>$cardTitle</b>".
          "          <div class='card-tools'>".
          "             <button type='button' class='btn bg-danger btn-sm' data-card-widget='remove'><i class='fas fa-times'></i></button>".
          "          </div>".
          "       </div>".
          "       <div class='card-body'>".
          "       ".$message.
          "       </div>".
          "   </div>".
          "</div>";
}

function displayCard($cardProperties, $content)
{
   $containerClass = $cardProperties['container'] ?: 'col-12 col-xl-3 col-lg-6 col-md-6 col-sm-12';;
   $cardClass      = $cardProperties['card'] ?: 'card-primary';
   $cardHeader     = $cardProperties['header'] ?: 'Card';

   return "<div class='$containerClass'>".
          "    <div class='card $cardClass'>".
          "       <div class='card-header'><b>$cardHeader</b></div>".
          "       <div class='card-body'>".
          "       ".$content.
          "       </div>".
          "   </div>".
          "</div>";

}

function insertModal()
{
   print "<div class='modal fade' id='modal-window' aria-hidden='true' style='display:none;'>\n".
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
         "</div> <!-- /.modal -->\n";

   print "<script>\n".
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
