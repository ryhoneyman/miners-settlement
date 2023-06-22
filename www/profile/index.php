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

$main->title('Profile Management');

$alte = $main->obj('adminlte');

include 'ui/header.php';

print "<div class='mb-4'>User ID: ".$main->userId."</div>";

print $alte->displayRow(
         $alte->infoBox('Link Profile','#','Link profile to email',
                        array('icon' => 'fa-link', 'icon-bg' => 'bg-gray', 'ribbon' => 'coming soon', 'ribbon-bg' => 'bg-warning text-bold text-tiny')).
         $alte->infoBox('Player Management','/profile/player/','Manage players on profile',
                        array('icon' => 'fa-users', 'icon-bg' => 'bg-success')).
         $alte->infoBox('Gear Management','/profile/gear/','Manage gear on profile',
                        array('icon' => 'fa-shield-alt', 'icon-bg' => 'bg-success')).
         $alte->infoBox('Build Management','/profile/player/build/','Manage builds on profile',
                        array('icon' => 'fa-user-shield', 'icon-bg' => 'bg-success', 'ribbon' => 'new', 'ribbon-bg' => 'bg-danger text-bold')).
         $alte->infoBox('Activation Codes','/profile/activate/','Unlock features with activation codes',
                        array('icon' => 'fa-key', 'icon-bg' => 'bg-warning'))
      ).
      displayEntitlements($main);

include 'ui/footer.php';

?>
<?php

function displayEntitlements($main)
{
   $db     = $main->db();
   $alte   = $main->obj('adminlte');
   $userId = $main->userId;

   $entitleResult = $db->query("select data from profile_data where profile_id = '$userId' and name = 'entitlement'",array('multi' => 0));

   if (!$entitleResult) { return false; } 

   $entitlements = json_decode($entitleResult['data'],true);
   $entitleList  = $db->query("select name,label,description,data from entitlement where active = 1");

   if (!$entitleList) { return false; }

   $featureContent = "<table border=0 class='table' style='width:auto;'>".
                     "<tbody>";

   foreach ($entitleList as $featureName => $featureInfo) {
      if (!array_key_exists($featureName,$entitlements)) { continue; }
      
      $featureConstraints = $entitlements[$featureName];
      $featureData        = json_decode($entitleList[$featureName]['data'],true);

      $featureOptions = array();
      foreach ($featureConstraints as $fcName => $fcValue) { $featureOptions[] = sprintf("%s %s",$fcValue,$fcName); }

      $featureContent .= sprintf("<tr><td>%s</td><td>%s</td><td>%s</td><td>%s</td></tr>",
                                 sprintf("<i class='text-lg fa fa-%s %s'>",$featureData['icon'],$featureData['class']),$featureInfo['label'],$featureInfo['description'],implode("<br>",$featureOptions));
   }

   $featureContent .= "</tbody>".
                      "</table>";

   return "<div class='mt-4'></div>".
          $alte->displayRow($alte->displayCard($featureContent,array('container' => 'col-12 col-xl-6 col-lg-9 col-md-12 col-sm-12', 'header' => 'Activated Features'))); 
}

?>
