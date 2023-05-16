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

$main->title('Profile Management');

include 'ui/header.php';

print "<div class='mb-4'>User ID: ".$main->userId."</div>";

?>
<div class="row">

   <div class="col-10 col-sm-6 col-md-6 col-lg-6 col-xl-3">
      <div class="info-box">
         <span class="info-box-icon bg-gray elevation-1"><a href="#"><i class="fas fa-link" aria-hidden="true"></i></a></span>
         <div class="ribbon-wrapper">
            <div class="ribbon bg-warning text-bold" style="font-size:0.5em;">COMING SOON</div>
         </div>
         <div class="info-box-content">
            <span class="info-box-text"> <a href="#"><b>Link Profile</b></a></span>
            <span class="info-box-number" style="font-weight:normal;">
               Link profile to email
            </span>
         </div>
      </div>
   </div>

   <div class="col-10 col-sm-6 col-md-6 col-lg-6 col-xl-3">
      <div class="info-box">
         <span class="info-box-icon bg-success elevation-1"><a href="/profile/player/"><i class="fas fa-users" aria-hidden="true"></i></a></span>
         <div class="ribbon-wrapper">
            <div class="ribbon bg-danger">NEW</div>
         </div>
         <div class="info-box-content">
            <span class="info-box-text"> <a href="/profile/player/"><b>Player Management</b></a></span>
            <span class="info-box-number" style="font-weight:normal;">
               Manage players on profile
            </span>
         </div>
      </div>
   </div>

   <div class="col-10 col-sm-6 col-md-6 col-lg-6 col-xl-3">
      <div class="info-box">
         <span class="info-box-icon bg-success elevation-1"><a href="/profile/gear/"><i class="fas fa-swords" aria-hidden="true"></i></a></span>
         <div class="ribbon-wrapper">
            <div class="ribbon bg-danger">NEW</div>
         </div>
         <div class="info-box-content">
            <span class="info-box-text"> <a href="/profile/gear/"><b>Gear Management</b></a></span>
            <span class="info-box-number" style="font-weight:normal;">
               Manage gear on profile
            </span>
         </div>
      </div>
   </div>

   <div class="col-10 col-sm-6 col-md-6 col-lg-6 col-xl-3">
      <div class="info-box">
         <span class="info-box-icon bg-yellow elevation-1"><a href="/profile/activate/"><i class="fas fa-key" aria-hidden="true"></i></a></span>
         <div class="ribbon-wrapper">
            <div class="ribbon bg-danger">NEW</div>
         </div>
         <div class="info-box-content">
            <span class="info-box-text"> <a href="/profile/activate/"><b>Activation Codes</b></a></span>
            <span class="info-box-number" style="font-weight:normal;">
               Unlock features with activation codes
            </span>
         </div>
      </div>
   </div>

</div>

<?php


displayEntitlements($main);

include 'ui/footer.php';

?>
<?php

function displayEntitlements($main)
{
   $db     = $main->db();
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

   print "<div class='mt-4'></div>";
   print displayRow(displayCard(array('container' => 'col-12 col-xl-6 col-lg-9 col-md-12 col-sm-12', 'header' => 'Activated Features'),$featureContent)); 
}

function displayRow($content)
{
   return "<div class='row'>".
          $content.
          "</div>";
}

function displayCard($cardProperties, $content)
{
   $containerClass = $cardProperties['container'] ?: 'col-12 col-xl-3 col-lg-6 col-md-6 col-sm-12';
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

?>
