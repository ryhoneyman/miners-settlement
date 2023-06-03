<?php
include_once 'miners-settlement-init.php';
include_once 'local/minersmain.class.php';

$main = new MinersMain(array(
   'debugLevel'     => 0,
   'errorReporting' => false,
   'sessionStart'   => true,
   'memoryLimit'    => null,
   'sendHeaders'    => true,
   'database'       => false,
   'input'          => false,
   'html'           => false,
   'adminlte'       => false,
));

include 'ui/header.php';

?>

<div class="row">
   <div class="col-10 col-sm-6 col-md-4 col-lg-4 col-xl-4">
      <div class="info-box">
         <span class="info-box-icon bg-primary elevation-1"><a href="/profile"><i class="fas fa-user" aria-hidden="true"></i></a></span>
         <div class="ribbon-wrapper">
            <div class="ribbon bg-danger">NEW</div>
         </div>
         <div class="info-box-content">
            <span class="info-box-text"> <a href="/profile"><b>User Profile</b></a></span>
            <span class="info-box-number" style="font-weight:normal;">
               Modify your user profile
            </span>
         </div>
      </div>
   </div>

<!--
   <div class="col-10 col-sm-6 col-md-4 col-lg-4 col-xl-4">
      <div class="info-box">
         <span class="info-box-icon bg-danger elevation-1"><a href="/beastiary/list?area=dungeon"><i class="fas fa-skull" aria-hidden="true"></i></a></span>
         <div class="ribbon-wrapper">
            <div class="ribbon bg-danger">NEW</div>
         </div>
         <div class="info-box-content">
            <span class="info-box-text"> <a href="/beastiary/list?area=dungeon"><b>Beastiary - Dungeon</b></a></span>
            <span class="info-box-number" style="font-weight:normal;">
               Get details about monsters in the Dungeon
            </span>
         </div>
      </div>
   </div>
-->

   <div class="col-10 col-sm-6 col-md-4 col-lg-4 col-xl-4">
      <div class="info-box">
         <span class="info-box-icon bg-red elevation-1"><a href="/simulation"><i class="fas fa-tachometer-alt" aria-hidden="true"></i></a></span>
         <div class="ribbon-wrapper">
            <div class="ribbon bg-danger">NEW</div>
         </div>
         <div class="info-box-content">
            <span class="info-box-text"> <a href="/simulation"><b>Simulation</b></a></span>
            <span class="info-box-number" style="font-weight:normal;">
               Find rune words at runeposts for items
            </span>
         </div>
      </div>
   </div>

   <div class="col-10 col-sm-6 col-md-4 col-lg-4 col-xl-4">
      <div class="info-box">
         <span class="info-box-icon bg-lightblue elevation-1"><a href="/runepost"><i class="fas fa-sign" aria-hidden="true"></i></a></span>
         <div class="ribbon-wrapper">
            <!--<div class="ribbon bg-danger">NEW</div>-->
         </div>
         <div class="info-box-content">
            <span class="info-box-text"> <a href="/runepost"><b>Runeposts</b></a></span>
            <span class="info-box-number" style="font-weight:normal;">
               Find rune words at runeposts for items
            </span>
         </div>
      </div>
   </div>

   <div class="col-10 col-sm-6 col-md-4 col-lg-4 col-xl-4">
      <div class="info-box">
         <span class="info-box-icon bg-primary elevation-1"><a href="/item/gear/list"><i class="fas fa-sword" aria-hidden="true"></i></a></span>
         <div class="ribbon-wrapper">
         </div>
         <div class="info-box-content">
            <span class="info-box-text"> <a href="/item/gear/list"><b>Gear List</b></a></span>
            <span class="info-box-number" style="font-weight:normal;">
               Examine gear attribute ranges
            </span>
         </div>
      </div>
   </div>

   <div class="col-10 col-sm-6 col-md-4 col-lg-4 col-xl-4">
      <div class="info-box">
         <span class="info-box-icon bg-yellow elevation-1"><a href="/item/crafting"><i class="fas fa-puzzle-piece" aria-hidden="true"></i></a></span>
         <div class="ribbon-wrapper">
         </div>
         <div class="info-box-content">
            <span class="info-box-text"> <a href="/item/crafting"><b>Scheme Crafting</b></a></span>
            <span class="info-box-number" style="font-weight:normal;">
               Find details about crafting schemes
            </span>
         </div>
      </div>
   </div>

   <div class="col-10 col-sm-6 col-md-4 col-lg-4 col-xl-4">
      <div class="info-box">
         <span class="info-box-icon bg-green elevation-1"><a href="/item/analytics"><i class="fas fa-analytics" aria-hidden="true"></i></a></span>
         <div class="ribbon-wrapper">
            <div class="ribbon bg-warning">POPULAR</div>
         </div>
         <div class="info-box-content">
            <span class="info-box-text"> <a href="/item/analytics"><b>Item Analytics</b></a></span>
            <span class="info-box-number" style="font-weight:normal;">
               Perform research on items and their stats
            </span>
         </div>
      </div>
   </div>

   <div class="col-10 col-sm-6 col-md-4 col-lg-4 col-xl-4">
      <div class="info-box">
         <span class="info-box-icon bg-purple elevation-1"><a href="/item/enhancement"><i class="fas fa-sparkles" aria-hidden="true"></i></a></span>
         <div class="ribbon-wrapper">
         </div>
         <div class="info-box-content">
            <span class="info-box-text"> <a href="/item/enhancement"><b>Item Enhancement</b></a></span>
            <span class="info-box-number" style="font-weight:normal;">
               Find details about enhancing items
            </span>
         </div>
      </div>
   </div>

</div>

<?php

include 'ui/footer.php';

?>
