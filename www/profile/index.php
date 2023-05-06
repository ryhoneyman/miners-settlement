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


</div>

<?php

print "User ID: ".$main->userId;

include 'ui/footer.php';

?>
