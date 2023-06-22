<?php
include_once 'miners-settlement-init.php';
include_once 'local/minersmain.class.php';

$main = new MinersMain(array(
   'sendHeaders' => true,
));

$main->title('Patch Notes History');

include 'ui/header.php';

$versionList = array(
   '1.5.2' => array(
      'datetime' => '2023-06-18 01:30 UTC',
      'changes' => array(
         'backend' => array(
            'Updated all pass-by-hash references and lookups',
         ),
      ),
   ),
);

?>

<div class="row">
  <div class="col-12 col-xl-9 col-lg-10 col-md-12 col-sm-12">
    <div class="card card-outline card-success">
      <div class="card-header">
        <b class="text-xl">v1.6.0</b><div class="card-tools text-yellow">2023-06-22 13:30 UTC</div>
      </div>
      <div class="card-body">
        <ul>
          <li><span class='text-green'>Frontend:</span> Added Build Simulation</li>
          <li><span class='text-green'>Frontend:</span> Added Build Management</li>
          <li><span class='text-green'>Frontend:</span> Grouped items by type in Gear Management</li>
        </ul>
      </div>
    </div>
  </div>
</div>


<div class="row">
  <div class="col-12 col-xl-9 col-lg-10 col-md-12 col-sm-12">
    <div class="card card-outline card-success">
      <div class="card-header">
        <b class="text-xl">v1.5.2</b><div class="card-tools text-yellow">2023-06-18 01:30 UTC</div>
      </div>
      <div class="card-body">
        <ul>
          <li><span class='text-red'>Backend:</span> Updated all pass-by-hash references and lookups</li>
        </ul>
      </div>
    </div>
  </div>
</div>


<div class="row">
  <div class="col-12 col-xl-9 col-lg-10 col-md-12 col-sm-12">
    <div class="card card-outline card-success">
      <div class="card-header">
        <b class="text-xl">v1.5.1</b><div class="card-tools text-yellow">2023-06-15 05:30 UTC</div>
      </div>
      <div class="card-body">
        <ul>
          <li><span class='text-green'>Frontend:</span> Added Battle Log to Scalable Simulation</li>
          <li><span class='text-green'>Frontend:</span> Unlocked Simulation for all users (default 10 iterations)</li>
          <li><span class='text-red'>Backend:</span> Updated stun-resist property on all runewords</li>
          <li><span class='text-red'>Backend:</span> Updated all dragon attributes</li>
          <li><span class='text-red'>Backend:</span> Corrected extra defense to be activated on attacker hit (battle engine)</li>
          <li><span class='text-red'>Backend:</span> Corrected stun resist to mitigate a percentage of stun, not the entire thing (battle engine)</li>
          <li><span class='text-red'>Backend:</span> Corrected defense lower stacking from additive to multiplicative (battle engine)</li>
        </ul>
      </div>
    </div>
  </div>
</div>


<div class="row">
  <div class="col-12 col-xl-9 col-lg-10 col-md-12 col-sm-12">
    <div class="card card-outline card-success">
      <div class="card-header">
        <b class="text-xl">v1.5.0</b><div class="card-tools text-yellow">2023-06-12 18:00 UTC</div>
      </div>
      <div class="card-body">
        <ul>
          <li><span class='text-green'>Frontend:</span> Added Monster Viewer</li>
          <li><span class='text-red'>Backend:</span> Added most monsters in the game (still missing some Shadow Order Dungeon)</li>
        </ul>
      </div>
    </div>
  </div>
</div>


<div class="row">
  <div class="col-12 col-xl-9 col-lg-10 col-md-12 col-sm-12">
    <div class="card card-outline card-success">
      <div class="card-header">
        <b class="text-xl">v1.4.1</b><div class="card-tools text-yellow">2023-06-05 18:30 UTC</div>
      </div>
      <div class="card-body">
        <ul>
          <li><span class='text-green'>Frontend:</span> Updated color/icons for runewords with elemental in Runeposts</li>
          <li><span class='text-red'>Backend:</span> Refactored stored game constants for access uniformity</li>
        </ul>
      </div>
    </div>
  </div>
</div>


<div class="row">
  <div class="col-12 col-xl-9 col-lg-10 col-md-12 col-sm-12">
    <div class="card card-outline card-success">
      <div class="card-header">
        <b class="text-xl">v1.4.0</b><div class="card-tools text-yellow">2023-06-03 20:00 UTC</div>
      </div>
      <div class="card-body">
        <ul>
          <li><span class='text-green'>Frontend:</span> Added Simulation section and Scalable Simulation tool</li>
          <li><span class='text-red'>Frontend:</span> Placed info box link to profile on homepage</li>
        </ul>
      </div>
    </div>
  </div>
</div>


<div class="row">
  <div class="col-12 col-xl-9 col-lg-10 col-md-12 col-sm-12">
    <div class="card card-outline card-success">
      <div class="card-header">
        <b class="text-xl">v1.3.3</b><div class="card-tools text-yellow">2023-05-27 17:00 UTC</div>
      </div>
      <div class="card-body">
        <ul>
          <li><span class='text-green'>Frontend:</span> Added images to pulldown in Item Analytics</li>
          <li><span class='text-green'>Frontend:</span> Adjusted table formatting/alignment in Runeposts</li>
          <li><span class='text-red'>Backend:</span> Added new Einlor rings</li>
        </ul>
      </div>
    </div>
  </div>
</div>


<div class="row">
  <div class="col-12 col-xl-9 col-lg-10 col-md-12 col-sm-12">
    <div class="card card-outline card-success">
      <div class="card-header">
        <b class="text-xl">v1.3.2</b><div class="card-tools text-yellow">2023-05-19 16:00 UTC</div>
      </div>
      <div class="card-body">
        <ul>
          <li><span class='text-green'>Frontend:</span> Final corrections to rounding issues in Item Analytics</li>
        </ul>
      </div>
    </div>
  </div>
</div>


<div class="row">
  <div class="col-12 col-xl-9 col-lg-10 col-md-12 col-sm-12">
    <div class="card card-outline card-success">
      <div class="card-header">
        <b class="text-xl">v1.3.0</b><div class="card-tools text-yellow">2023-05-16 20:30 UTC</div>
      </div>
      <div class="card-body">
        <ul>
          <li><span class='text-green'>Frontend:</span> Correct mininum/maxinum bounds due to rounding issues on Item Analytics</li>
          <li><span class='text-green'>Frontend:</span> Added new links on Home page</li>
          <li><span class='text-green'>Frontend:</span> Added Activation Codes link and feature display to Profile page</li>
          <li><span class='text-green'>Frontend:</span> New Activation Codes page available in Profile</li>
          <li><span class='text-red'>Backend:</span> Creation of database elements to support activation codes</li>
        </ul>
      </div>
    </div>
  </div>
</div>


<div class="row">
  <div class="col-12 col-xl-9 col-lg-10 col-md-12 col-sm-12">
    <div class="card card-outline card-success">
      <div class="card-header">
        <b class="text-xl">v1.2.4</b><div class="card-tools text-yellow">2023-05-12 21:00 UTC</div>
      </div>
      <div class="card-body">
        <ul>
          <li><span class='text-green'>Frontend:</span> Added Item Enhancement page</li>
          <li><span class='text-red'>Backend:</span> Adjustments to simulator CLI</li>
        </ul>
      </div>
    </div>
  </div>
</div>


<div class="row">
  <div class="col-12 col-xl-9 col-lg-10 col-md-12 col-sm-12">
    <div class="card card-outline card-success">
      <div class="card-header">
        <b class="text-xl">v1.2.3</b><div class="card-tools text-yellow">2023-05-10 14:30 UTC</div>
      </div>
      <div class="card-body">
        <ul>
          <li><span class='text-green'>Frontend:</span> Runeposts page now shows runes in order that matches the game</li>
          <li><span class='text-red'>Backend:</span> Added Big Surprise and Pumpkin Reaper runewords</li>
        </ul>
      </div>
    </div>
  </div>
</div>


<div class="row">
  <div class="col-12 col-xl-9 col-lg-10 col-md-12 col-sm-12">
    <div class="card card-outline card-success">
      <div class="card-header">
        <b class="text-xl">v1.2.2</b><div class="card-tools text-yellow">2023-05-10 14:30 UTC</div>
      </div>
      <div class="card-body">
        <ul>
          <li><span class='text-red'>Backend:</span> Added Christmas amulets to the database</li>
        </ul>
      </div>
    </div>
  </div>
</div>


<div class="row">
  <div class="col-12 col-xl-9 col-lg-10 col-md-12 col-sm-12">
    <div class="card card-outline card-success">
      <div class="card-header">
        <b class="text-xl">v1.2.1</b><div class="card-tools text-yellow">2023-05-09 14:30 UTC</div>
      </div>
      <div class="card-body">
        <ul>
          <li><span class='text-red'>Backend:</span> Updated incorrect data on runewords for Glory Shield and Spear of the Gods</li>
        </ul>
      </div>
    </div>
  </div>
</div>


<div class="row">
  <div class="col-12 col-xl-9 col-lg-10 col-md-12 col-sm-12">
    <div class="card card-outline card-success">
      <div class="card-header">
        <b class="text-xl">v1.2.0</b><div class="card-tools text-yellow">2023-05-06 23:30 UTC</div>
      </div>
      <div class="card-body">
        <ul>
          <li><span class='text-green'>Frontend:</span> Added Scheme Crafting area with basic schemes</li>
          <li><span class='text-green'>Frontend:</span> Profile area now has Player and Gear sections</li>
          <li><span class='text-green'>Frontend:</span> Added ability to add/remove players and gear on profile</li>
          <li><span class='text-green'>Frontend:</span> Added item sharing on Item Analytics</li>
          <li><span class='text-green'>Frontend:</span> Added weapon and shield skins to database</li>
        </ul>
      </div>
    </div>
  </div>
</div>


<div class="row">
  <div class="col-12 col-xl-9 col-lg-10 col-md-12 col-sm-12">
    <div class="card card-outline card-success">
      <div class="card-header">
        <b class="text-xl">v1.1.6</b><div class="card-tools text-yellow">2023-04-28 14:00 UTC</div>
      </div>
      <div class="card-body">
        <ul>
          <li><span class='text-green'>Frontend:</span> Fixed form handler from closing early preventing calculation on Item Analytics</li>
        </ul>
      </div>
    </div>
  </div>
</div>


<div class="row">
  <div class="col-12 col-xl-9 col-lg-10 col-md-12 col-sm-12">
    <div class="card card-outline card-success">
      <div class="card-header">
        <b class="text-xl">v1.1.5</b><div class="card-tools text-yellow">2023-04-27 14:30 UTC</div>
      </div>
      <div class="card-body">
        <ul>
          <li><span class='text-green'>Frontend:</span> Adjusted decimal notation to support commas and periods</li>
          <li><span class='text-green'>Frontend:</span> Fixed invalid input warning on valid items</li>
          <li><span class='text-green'>Frontend:</span> Added level appropriate bounds checking for items</li>
          <li><span class='text-red'>Backend:</span> Decreased debug level and error outputing on production</li>
          <li><span class='text-red'>Backend:</span> Deployed item database</li>
          <li><span class='text-red'>Backend:</span> Linked items in Gear List to database</li>
          <li><span class='text-red'>Backend:</span> Updated level from 10 to 15</li>
        </ul>
      </div>
    </div>
  </div>
</div>


<div class="row">
  <div class="col-12 col-xl-9 col-lg-10 col-md-12 col-sm-12">
    <div class="card card-outline card-success">
      <div class="card-header">
        <b class="text-xl">v1.1.4</b><div class="card-tools text-yellow">2023-04-24 15:30 UTC</div>
      </div>
      <div class="card-body">
        <ul>
          <li><span class='text-green'>Frontend:</span> Update attribution and license footer</li>
        </ul>
      </div>
    </div>
  </div>
</div>


<div class="row">
  <div class="col-12 col-xl-9 col-lg-10 col-md-12 col-sm-12">
    <div class="card card-outline card-success">
      <div class="card-header">
        <b class="text-xl">v1.1.3</b><div class="card-tools text-yellow">2023-04-22 05:30 UTC</div>
      </div>
      <div class="card-body">
        <ul>
          <li><span class='text-green'>Frontend:</span> Added jump links and return to top buttons on Runeposts page</li>
        </ul>
      </div>
    </div>
  </div>
</div>


<div class="row">
  <div class="col-12 col-xl-9 col-lg-10 col-md-12 col-sm-12">
    <div class="card card-outline card-success">
      <div class="card-header">
        <b class="text-xl">v1.1.2</b><div class="card-tools text-yellow">2023-04-16 22:00 UTC</div>
      </div>
      <div class="card-body">
        <ul>
          <li><span class='text-green'>Frontend:</span> Gear List area created</li>
          <li><span class='text-red'>Backend:</span> Updated elemental effects</li>
        </ul>
      </div>
    </div>
  </div>
</div>


<div class="row">
  <div class="col-12 col-xl-9 col-lg-10 col-md-12 col-sm-12">
    <div class="card card-outline card-success">
      <div class="card-header">
        <b class="text-xl">v1.1.1</b><div class="card-tools text-yellow">2023-04-15 05:30 UTC</div>
      </div>
      <div class="card-body">
        <ul>
          <li><span class='text-green'>Frontend:</span> Runeposts area created</li>
          <li><span class='text-green'>Frontend:</span> Cleanup page layout inconsistencies</li>
          <li><span class='text-green'>Frontend:</span> Updated title dipslay on all pages</li>
          <li><span class='text-primary'>Simulator:</span> Added Weapon and Shield Skin to system</li>
        </ul>
      </div>
    </div>
  </div>
</div>


<div class="row">
  <div class="col-12 col-xl-9 col-lg-10 col-md-12 col-sm-12">
    <div class="card card-outline card-success">
      <div class="card-header">
        <b class="text-xl">v1.1.0</b><div class="card-tools text-yellow">2023-04-06 04:00 UTC</div>
      </div>
      <div class="card-body">
        <ul>
          <li><span class='text-green'>Frontend:</span> Updated Admin LTE UI framework to v3.2.0</li>
        </ul>
      </div>
    </div>
  </div>
</div>


<div class="row">
  <div class="col-12 col-xl-9 col-lg-10 col-md-12 col-sm-12">
    <div class="card card-outline card-success">
      <div class="card-header">
        <b class="text-xl">v1.0.3</b><div class="card-tools text-yellow">2023-04-04 05:30 UTC</div>
      </div>
      <div class="card-body">
        <ul>
          <li><span class='text-green'>Frontend:</span> Updated title method for displaying page title</li>
          <li><span class='text-primary'>Simulator:</span> Added DPS to output</li>
          <li><span class='text-pink'>Library:</span> Base class uplifted to latest version</li>
          <li><span class='text-pink'>Library:</span> Html class submit method adjusted for multiple parameters</li>
          <li><span class='text-pink'>Library:</span> Main class added error handling method</li>
          <li><span class='text-pink'>Library:</span> Main class added player data handling methods</li>
          <li><span class='text-pink'>Library:</span> Input class updated variable acquistion method</li>
        </ul>
      </div>
    </div>
  </div>
</div>


<div class="row">
  <div class="col-12 col-xl-9 col-lg-10 col-md-12 col-sm-12">
    <div class="card card-outline card-success">
      <div class="card-header">
        <b class="text-xl">v1.0.2</b><div class="card-tools text-yellow">2023-03-31 18:30 UTC</div>
      </div>
      <div class="card-body">
        <ul>
          <li><span class='text-red'>Backend:</span> Adjustments to minimum speed assessment</li>
        </ul>
      </div>
    </div>
  </div>
</div>


<div class="row">
  <div class="col-12 col-xl-9 col-lg-10 col-md-12 col-sm-12">
    <div class="card card-outline card-success">
      <div class="card-header">
        <b class="text-xl">v1.0.1</b><div class="card-tools text-yellow">2023-03-19 17:30 UTC</div>
      </div>
      <div class="card-body">
        <ul>
          <li><span class='text-green'>Frontend:</span> Mobile rendering updates</li>
          <li><span class='text-green'>Frontend:</span> Loaded favicon</li>
          <li><span class='text-green'>Frontend:</span> Modified percentage bars in analytics to use progress-bar class</li>
        </ul>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-12 col-xl-9 col-lg-10 col-md-12 col-sm-12">
    <div class="card card-outline card-success">
      <div class="card-header">
        <b class="text-xl">v1.0.0</b><div class="card-tools text-yellow">2023-03-18 03:30 UTC</div>
      </div>
      <div class="card-body">
        <ul>
          <li>Initial build</li>
        </ul>
      </div>
    </div>
  </div>
</div>

<?php

include 'ui/footer.php';

?>
