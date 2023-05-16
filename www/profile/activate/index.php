<?php
include_once 'miners-settlement-init.php';
include_once 'local/minersmain.class.php';

$main = new MinersMain(array(
   'debugLevel'     => 0,
   'errorReporting' => true,
   'sessionStart'   => true,
   'memoryLimit'    => null,
   'sendHeaders'    => true,
   'database'       => true,
   'input'          => true,
   'html'           => true,
));

$main->title('Feature Activation');

$input = $main->obj('input');

$aCode  = $input->get('code','alphanumeric');
$submit = ($input->get('submit')) ? true : false;

if ($submit && $aCode) { 
   if (strlen($aCode) != 32) { $main->error('Invalid activation code entered [AC050]'); }
   else { 
      $applyResult = applyCode($main,$aCode); 

      if ($applyResult) { header("Location: /profile/"); }
   }
}

$errors = $main->error();

include 'ui/header.php';


if ($errors) { 
   print displayRow(errorCard($errors));
}

codeDisplay($main);

include 'ui/footer.php';

?>
<?php

function applyCode($main, $code)
{
   $db     = $main->db();
   $userId = $main->userId;

   $codeResult = $db->query("select * from activation_code where code = '$code' and applied is null and active = 1",array('multi' => 0));

   if (!$codeResult) { 
      $main->error('Invalid activation code entered [AC044]');
      return false;
   }

   if ($codeResult['profile_id'] && $codeResult['profile_id'] != $userId) {
      $main->error('Invalid activation code entered [AC041]');
      return false;
   }

   $codeFeatures = json_decode($codeResult['data'],true);

   if ($codeFeatures['entitlement']) {
      $newEntitlements    = $codeFeatures['entitlement'];
      $currentProfileData = $db->query("select data from profile_data where profile_id = '$userId' and name = 'entitlement'",array('multi' => 0));

      if ($currentProfileData === false) {
         $main->error('Could not apply activation code [AC053]');
         return false;
      }
    
      $currentEntitlements = ($currentProfileData['data']) ? json_decode($currentProfileData['data'],true) : array();
      $updatedEntitlements = array_replace_recursive($currentEntitlements,$newEntitlements);

      $insRc = $db->bindExecute("insert into profile_data (profile_id,name,data,created,updated) values (?,?,?,now(),now()) ".
                                "on duplicate key update data = values(data), updated = values(updated)",
                                "sss",
                                array($userId,'entitlement',json_encode($updatedEntitlements)));

      if (!$insRc) { 
         $main->error('Could not apply activation code [AC153]');
         return false;
      }

      $updRc = $db->bindExecute("update activation_code set applied = now() where id = ?","i",array($codeResult['id']));
   }

   return true;
}

function codeDisplay($main)
{
   $html    = $main->obj('html');
   $userId  = $main->userId;

   $addContent = "<div class='input-group'>".
                 $html->inputText('code',null,array('size' => 32)).
                 "<span class='input-group-append'>".
                 $html->submit('submit','Apply',array('class' => 'btn btn-primary btn-sm')).
                 "</span>".
                 "</div>";

   print $html->startForm(array('method' => 'post')).
         displayRow(displayCard(array('container' => 'col-12 col-xl-6 col-lg-6 col-md-9 col-sm-12', 'header' => 'Enter Activation Code'),$addContent)).
         $html->endForm();
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
         "     var modalHTML   = \"<p>Are you sure you want to delete \"+deleteName+\"?<input type='hidden' name='delete' id='delete' value='\"+deleteName+\"'/>\";\n".
         "     $('#modal-body').html(modalHTML);\n".
         "     $('#modal-window').modal('show');\n".
         "  });\n".
         "</script>\n";
}


?>
