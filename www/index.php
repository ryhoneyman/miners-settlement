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
   'input'          => false,
   'html'           => false,
   'adminlte'       => false,
));

include 'ui/header.php';

?>

<div class="row mb-4">
   <div class="col-12">
      <h1>Welcome to Miners Tools!</h1>
      <h3>This site offers a comprehensive collection of information and resources for players of the <a href='https://funventure.eu/miners_settlement/'>Miners Settlement</a> game.</h3>
   </div>
</div>

<div class="row">

   <div class="col-10 col-sm-6 col-md-4 col-lg-4 col-xl-4">
      <div class="info-box">
         <span class="info-box-icon bg-warning elevation-1"><a href="/item/crafting/einlor/"><i class="fas fa-store-alt" aria-hidden="true"></i></a></span>
         <div class="ribbon-wrapper">
            <div class="ribbon bg-danger">NEW</div>
         </div>
         <div class="info-box-content">
            <span class="info-box-text"> <a href="/item/crafting/einlor/"><b>Einlor Forge</b></a></span>
            <span class="info-box-number" style="font-weight:normal;">
               Crafting recipes in the Einlor Forge
            </span>
         </div>
      </div>
   </div>

   <div class="col-10 col-sm-6 col-md-4 col-lg-4 col-xl-4">
      <div class="info-box">
         <span class="info-box-icon bg-warning elevation-1"><a href="/item/crafting/mitar/"><i class="fas fa-store-alt" aria-hidden="true"></i></a></span>
         <div class="ribbon-wrapper">
            <!--<div class="ribbon bg-danger">NEW</div>-->
         </div>
         <div class="info-box-content">
            <span class="info-box-text"> <a href="/item/crafting/mitar/"><b>Mitar Forge</b></a></span>
            <span class="info-box-number" style="font-weight:normal;">
               Crafting recipes in the Mitar Forge
            </span>
         </div>
      </div>
   </div>

   <div class="col-10 col-sm-6 col-md-4 col-lg-4 col-xl-4">
      <div class="info-box">
         <span class="info-box-icon bg-purple elevation-1"><a href="/tools/colorpicker"><i class="fas fa-palette" aria-hidden="true"></i></a></span>
         <div class="ribbon-wrapper">
            <!--<div class="ribbon bg-danger">NEW</div>-->
         </div>
         <div class="info-box-content">
            <span class="info-box-text"> <a href="/tools/colorpicker"><b>Color Picker</b></a></span>
            <span class="info-box-number" style="font-weight:normal;">
               Learn how to set colors in names and chat
            </span>
         </div>
      </div>
   </div>

   <div class="col-10 col-sm-6 col-md-4 col-lg-4 col-xl-4">
      <div class="info-box">
         <span class="info-box-icon bg-primary elevation-1"><a href="/profile"><i class="fas fa-user" aria-hidden="true"></i></a></span>
         <div class="ribbon-wrapper">
            <!--<div class="ribbon bg-danger">NEW</div>-->
         </div>
         <div class="info-box-content">
            <span class="info-box-text"> <a href="/profile"><b>User Profile</b></a></span>
            <span class="info-box-number" style="font-weight:normal;">
               Modify your user profile
            </span>
         </div>
      </div>
   </div>

   <div class="col-10 col-sm-6 col-md-4 col-lg-4 col-xl-4">
      <div class="info-box">
         <span class="info-box-icon bg-orange elevation-1"><a href="/monster/viewer/"><i class="fas fa-skull" aria-hidden="true"></i></a></span>
         <div class="ribbon-wrapper">
            <div class="ribbon bg-warning">POPULAR</div>
         </div>
         <div class="info-box-content">
            <span class="info-box-text"> <a href="/monster/viewer/"><b>Monster Viewer</b></a></span>
            <span class="info-box-number" style="font-weight:normal;">
               Get details about monsters in the game
            </span>
         </div>
      </div>
   </div>

   <div class="col-10 col-sm-6 col-md-4 col-lg-4 col-xl-4">
      <div class="info-box">
         <span class="info-box-icon bg-red elevation-1"><a href="/simulation"><i class="fas fa-tachometer-alt" aria-hidden="true"></i></a></span>
         <div class="ribbon-wrapper">
            <div class="ribbon bg-warning">POPULAR</div>
         </div>
         <div class="info-box-content">
            <span class="info-box-text"> <a href="/simulation"><b>Simulation</b></a></span>
            <span class="info-box-number" style="font-weight:normal;">
               Simulate battles and analyze outcomes
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
