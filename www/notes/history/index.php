<?php
include_once 'miners-settlement-init.php';
include_once 'local/minersmain.class.php';

$main = new MinersMain(array(
   'sendHeaders' => true,
));

$main->title('Patch Notes History');

include 'ui/header.php';

$versionList = array(
  array(
     'version' => '1.6.5',
     'datetime' => '2024-05-07 05:00 UTC',
     'changes' => array(
        'backend' => array(
           'Added Nightwing Runepost and Runewords',
           'Added Rune XIII (R13)',
           'Added Naturesong Edge and Terraflora Shield'
        ),
     ),
  ),
   array(
      'version' => '1.6.4',
      'datetime' => '2023-09-21 20:00 UTC',
      'changes' => array(
         'frontend' => array(
            'Added Einlor Forge Crafting page',
         ),
      ),
   ),
   array(
      'version' => '1.6.3',
      'datetime' => '2023-09-10 20:00 UTC',
      'changes' => array(
         'frontend' => array(
            'Added item filter to Runeposts',
            'Added item links back to Item Analytics from Gear List',
            'Enabled auto-submit for pulldowns in Monster Viewer, Item Analytics, and Runeposts',
         ),
         'backend' => array(
            'Added boss monsters from Maze of Champions',
         ),
      ),
   ),
   array(
      'version' => '1.6.2',
      'datetime' => '2023-08-31 04:00 UTC',
      'changes' => array(
         'frontend' => array(
            'Added Mitar Forge Crafting page',
         ),
         'backend' => array(
            'Restructured all crafting data structures',
         ),
      ),
   ),
   array(
      'version' => '1.6.1',
      'datetime' => '2023-07-15 04:00 UTC',
      'changes' => array(
         'frontend' => array(
            'Fixed runeword selection on Scalable Simulation', 
            'Runewords can now recognize multiple items to activate',
         ),
         'backend' => array(
            'Updated all runeword requirement structures',
            'Added runeposts with area-exclusive runewords',
         ),
      ),
   ),
   array(
      'version' => '1.6.0',
      'datetime' => '2023-06-22 13:30 UTC',
      'changes' => array(
         'frontend' => array(
            'Added Build Simulation',
            'Added Build Management',
            'Grouped items by type in Gear Management',
         ),
      ),
   ),
   array(
      'version' => '1.5.2',
      'datetime' => '2023-06-18 01:30 UTC',
      'changes' => array(
         'backend' => array(
            'Updated all pass-by-hash references and lookups',
         ),
      ),
   ),
   array(
      'version' => '1.5.1',
      'datetime' => '2023-06-15 05:30 UTC',
      'changes' => array(
         'frontend' => array(
            'Added Battle Log to Scalable Simulation',
            'Unlocked Simulation for all users (default 10 iterations)',
         ),
         'backend' => array(
            'Updated stun-resist property on all runewords',
            'Updated all dragon attributes',
            'Corrected extra defense to be activated on attacker hit (battle engine)',
            'Corrected stun resist to mitigate a percentage of stun, not the entire thing (battle engine)',
            'Corrected defense lower stacking from additive to multiplicative (battle engine)',
         ),
      ),
   ),
   array(
      'version' => '1.5.0',
      'datetime' => '2023-06-12 18:00 UTC',
      'changes' => array(
         'frontend' => array(
            'Added Monster Viewer',
         ),
         'backend' => array(
            'Added most monsters in the game to date (still missing some Shadow Order Dungeon)',
         ),
      ),
   ),
   array(
      'version' => '1.4.1',
      'datetime' => '2023-06-05 18:30 UTC',
      'changes' => array(
         'frontend' => array(
            'Updated color/icons for runewords with elemental in Runeposts',
         ),
         'backend' => array(
            'Refactored stored game constants for access uniformity',
         ),
      ),
   ),
   array(
      'version' => '1.4.0',
      'datetime' => '2023-06-03 20:00 UTC',
      'changes' => array(
         'frontend' => array(
            'Added Simulation section and Scalable Simulation tool',
            'Placed info box link to profile on homepage',
         ),
      ),
   ),
   array(
      'version' => '1.3.3',
      'datetime' => '2023-05-27 17:00 UTC',
      'changes' => array(
         'frontend' => array(
            'Added images to pulldown in Item Analytics',
            'Adjusted table formatting/alignment in Runeposts',
         ),
         'backend' => array(
            'Added new Einlor rings',
         ),
      ),
   ),
   array(
      'version' => '1.3.2',
      'datetime' => '2023-05-19 16:00 UTC',
      'changes' => array(
         'frontend' => array(
            'Final corrections to rounding issues in Item Analytics',
         ),
      ),
   ),
   array(
      'version' => '1.3.0',
      'datetime' => '2023-05-16 20:30 UTC',
      'changes' => array(
         'frontend' => array(
            'Corrected mininum/maxinum bounds due to rounding issues on Item Analytics',
            'Added new links on Home page',
            'Added Activation Codes link and feature display to Profile page',
            'New Activation Codes page available in Profile',
         ),
         'backend' => array(
            'Creation of database elements to support activation codes',
         ),
      ),
   ),
   array(
      'version' => '1.2.4',
      'datetime' => '2023-05-12 21:00 UTC',
      'changes' => array(
         'frontend' => array(
            'Added Item Enhancement page',
         ),
         'backend' => array(
            'Adjustments to simulator CLI',
         ),
      ),
   ),
   array(
      'version' => '1.2.3',
      'datetime' => '2023-05-10 14:30 UTC',
      'changes' => array(
         'frontend' => array(
            'Runeposts page now shows runes in order that matches the game',
         ),
         'backend' => array(
            'Added Big Surprise and Pumpkin Reaper runewords',
         ),
      ),
   ),
   array(
      'version' => '1.2.2',
      'datetime' => '2023-05-10 14:30 UTC',
      'changes' => array(
         'backend' => array(
            'Added Christmas amulets to the database',
         ),
      ),
   ),
   array(
      'version' => '1.2.1',
      'datetime' => '2023-05-09 14:30 UTC',
      'changes' => array(
         'backend' => array(
            'Updated incorrect data on runewords for Glory Shield and Spear of the Gods',
         ),
      ),
   ),
   array(
      'version' => '1.2.0',
      'datetime' => '2023-05-06 23:30 UTC',
      'changes' => array(
         'frontend' => array(
            'Added Scheme Crafting area with basic schemes', 
            'Profile area now has Player and Gear sections',
            'Added ability to add/remove players and gear on profile',
            'Added item sharing on Item Analytics',
            'Added weapon and shield skins to database',
         ),
      ),
   ),
   array(
      'version' => '1.1.6',
      'datetime' => '2023-04-28 14:00 UTC',
      'changes' => array(
         'frontend' => array(
            'Fixed form handler from closing early preventing calculation on Item Analytics',
         ),
      ),
   ),
   array(
      'version' => '1.1.5',
      'datetime' => '2023-04-27 14:30 UTC',
      'changes' => array(
         'frontend' => array(
            'Adjusted decimal notation to support commas and periods',
            'Fixed invalid input warning on valid items',
            'Added level appropriate bounds checking for items',
         ),
         'backend' => array(
            'Decreased debug level and error outputing on production',
            'Deployed item database',
            'Linked items in Gear List to database',
            'Updated level from 10 to 15',
         ),
      ),
   ),
   array(
      'version' => '1.1.4',
      'datetime' => '2023-04-24 15:30 UTC',
      'changes' => array(
         'frontend' => array(
            'Update attribution and license footer',
         ),
      ),
   ),
   array(
      'version' => '1.1.3',
      'datetime' => '2023-04-22 05:30 UTC',
      'changes' => array(
         'frontend' => array(
            'Added jump links and return to top buttons on Runeposts page',
         ),
      ),
   ),
   array(
      'version' => '1.1.2',
      'datetime' => '2023-04-16 22:00 UTC',
      'changes' => array(
         'frontend' => array(
            'Gear List area created',
         ),
         'backend' => array(
            'Updated elemental effects',
         ),
      ),
   ),
   array(
      'version' => '1.1.1',
      'datetime' => '2023-04-15 05:30 UTC',
      'changes' => array(
         'frontend' => array(
            'Runeposts area created',
            'Cleanup page layout inconsistencies',
            'Updated title display on all pages',
         ),
         'backend' => array(
            'Added Weapon and Shield Skin to Simulator',
         ),
      ),
   ),
   array(
      'version' => '1.1.0',
      'datetime' => '2023-04-06 04:00 UTC',
      'changes' => array(
         'frontend' => array(
            'Updated Admin LTE UI framework to v3.2.0',
         ),
      ),
   ),
   array(
      'version' => '1.0.3',
      'datetime' => '2023-04-04 05:30 UTC',
      'changes' => array(
         'frontend' => array(
            'Updated title method for displaying page title',
         ),
         'backend' => array(
            'Added DPS to output in Simulator',
         ),
         'library' => array(
            'Base class uplifted to latest version',
            'Html class submit method adjusted for multiple parameters',
            'Main class added error handling method',
            'Main class added player data handling methods',
            'Input class updated variable acquistion method',
         ),
      ),
   ),
   array(
      'version' => '1.0.2',
      'datetime' => '2023-03-31 18:30 UTC',
      'changes' => array(
         'backend' => array(
            'Adjustments to minimum speed assessment',
         ),
      ),
   ),
   array(
      'version' => '1.0.1',
      'datetime' => '2023-03-19 17:30 UTC',
      'changes' => array(
         'frontend' => array(
            'Mobile rendering updates',
            'Loaded favicon',
            'Modified percentage bars in analytics to use progress-bar class',
         ),
      ),
   ),
   array(
      'version' => '1.0.0',
      'datetime' => '2023-03-18 03:30 UTC',
      'changes' => array(
         'system' => array(
            'Initial build',
         ),
      ),
   ),
);

$notesDisplay = '';

foreach ($versionList as $versionPatch) {
   $notesDisplay .= displayPatchNote($versionPatch);
}

print $notesDisplay;

include 'ui/footer.php';

?>
<?php

function displayPatchNote($patchInfo)
{
   $changeAttrib = array(
      'system'   => array('label' => 'System', 'color' => 'text-primary'),
      'frontend' => array('label' => 'Frontend', 'color' => 'text-green'),
      'backend'  => array('label' => 'Backend', 'color' => 'text-red'),
      'library'  => array('label' => 'Library', 'color' => 'text-pink'),
   );

   $patchVersion  = $patchInfo['version'];
   $patchDatetime = $patchInfo['datetime'];

   $return = "<div class='row'>\n".
             "  <div class='col-12 col-xl-9 col-lg-10 col-md-12 col-sm-12'>\n".
             "    <div class='card card-outline card-success'>\n".
             "      <div class='card-header'>\n".
             "        <b class='text-xl'>v$patchVersion</b><div class='card-tools text-yellow'>$patchDatetime</div>\n".
             "      </div>\n".
             "      <div class='card-body'>\n".
             "        <ul>\n";

   foreach ($patchInfo['changes'] as $changeType => $changeList) {
      foreach ($changeList as $changeText) {
         $changeLabel = $changeAttrib[$changeType]['label'];
         $changeColor = $changeAttrib[$changeType]['color'];

         $return .= "             <li><span class='$changeColor'>$changeLabel:</span> $changeText</li>\n"; 
      }
   }

   $return .= "        </ul>\n".
              "      </div>\n".
              "    </div>\n".
              "  </div>\n".
              "</div>\n";

   return $return;
}

?>

<!--
<div class="row">
  <div class="col-12 col-xl-9 col-lg-10 col-md-12 col-sm-12">
    <div class="card card-outline card-success">
      <div class="card-header">
        <b class="text-xl">v1.6.2</b><div class="card-tools text-yellow">2023-08-31 04:00 UTC</div>
      </div>
      <div class="card-body">
        <ul>
          <li><span class='text-green'>Frontend:</span> Added Mitar Forge Crafting page</li>
          <li><span class='text-red'>Backend:</span> Restructured all crafting data structures</li>
        </ul>
      </div>
    </div>
  </div>
</div>


<div class="row">
  <div class="col-12 col-xl-9 col-lg-10 col-md-12 col-sm-12">
    <div class="card card-outline card-success">
      <div class="card-header">
        <b class="text-xl">v1.6.1</b><div class="card-tools text-yellow">2023-07-15 04:00 UTC</div>
      </div>
      <div class="card-body">
        <ul>
          <li><span class='text-green'>Frontend:</span> Fixed runeword selection on Scalable Simulation</li>
          <li><span class='text-green'>Frontend:</span> Runewords can now recognize multiple items to activate</li>
          <li><span class='text-red'>Backend:</span> Updated all runeword requirement structures</li>
          <li><span class='text-red'>Backend:</span> Added runeposts with area-exclusive runewords</li>
        </ul>
      </div>
    </div>
  </div>
</div>


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
          <li><span class='text-red'>Backend:</span> Added Weapon and Shield Skin to Simulator</li>
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
          <li><span class='text-primary'>Backend:</span> Added DPS to output in Simulator</li>
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

-->
