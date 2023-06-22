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

$main->title('Feature Activation');

$input = $main->obj('input');
$alte  = $main->obj('adminlte');

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


if ($errors) { print $alte->displayRow($alte->errorCard($errors)); }

print codeDisplay($main);

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
   $alte    = $main->obj('adminlte');
   $userId  = $main->userId;

   $addContent = "<div class='input-group'>".
                 $html->inputText('code',null,array('size' => 32)).
                 "<span class='input-group-append'>".
                 $html->submit('submit','Apply',array('class' => 'btn btn-primary btn-sm')).
                 "</span>".
                 "</div>";

   return $html->startForm(array('method' => 'post')).
          $alte->displayRow($alte->displayCard($addContent,array('container' => 'col-12 col-xl-6 col-lg-6 col-md-9 col-sm-12', 'title' => 'Enter Activation Code'))).
          $html->endForm();
}

?>
