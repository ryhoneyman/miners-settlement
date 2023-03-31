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
));

include_once 'common/input.class.php';
include_once 'common/html.class.php';
include_once 'local/constants.class.php';

$input     = new Input($main->debug);
$html      = new HTML($main->debug);
$constants = new Constants($main->debug);

$title    = 'Beastiary';
$subtitle = ' - Dungeon';

$area = $input->get('area','alphanumeric');

$monsterList = getMonsters($area);
$lootList    = getLootFromMonsters($monsterList);

include 'ui/header.php';

?>
<div class="row">
   <div class="col-12 col-xl-9 col-lg-9 col-md-10 col-sm-12">
       <div class="card card-outline card-success">
          <div class="card-header"><b>Monsters</b></div>
          <div class="card-body">
          <?php print monsterDisplay($monsterList,$lootList); ?>
          </div>
      </div>
   </div>
</div>

<?php

include 'ui/footer.php';

?>
<?php

function monsterDisplay($monsterList, $lootList)
{
   global $constants;

   $attribDisplay = $constants->attribDisplay();

   $return = "<table border=0 cellpadding=10>".
             "<tr><th></th><th>Name</th><th>Health</th><th>Attack</th><th>Defense</th><th>Speed</th><th>Effects</th><th>Exp</th><th>Drops</th></tr>";

   foreach ($monsterList as $monsterId => $monsterInfo) {
      $monsterImage = ($monsterInfo['image']) ? sprintf("<img src='%s'>",$monsterInfo['image']) : '';
      $monsterName  = $monsterInfo['name'];
      $monsterLoot  = $monsterInfo['loot'];

      $return .= sprintf("<tr><td>%s</td><td>%s</td>",$monsterImage,$monsterName);

      foreach ($attribDisplay as $attribName => $attribInfo) {
         $return .= sprintf("<td><span class='badge' style='width:75px; background:%s; color:white;'>%d <i class='fas fa-%s float-right'></i></span></td>",
                            $attribInfo['color'],$monsterInfo[$attribName],$attribInfo['icon']);
      }

      $return .= "<td></td>";
      $return .= "<td>".$monsterInfo['xp']."</td>";

      if ($monsterLoot) {
         $lootDisplay = "<td>";

         foreach ($monsterLoot as $lootId => $lootInfo) {
            $lootData = $lootList[$lootId];
            $lootDisplay .= sprintf("<img src='%s'> ",$lootData['image']);
         }
 
         $lootDisplay .= "</td>";

         $return .= $lootDisplay;
      } 
      else { $return .= "<td></td>"; }

      $return .= "</tr>";
   }

   $return .= "</table>";

   return $return;
}

function getMonsters($area = null)
{
   $monsterList = array();

   if (is_null($area)) { $area = '*'; }

   $area = strtolower($area);

   $areaList = array(
      '*'       => array(),
      'dungeon' => array(),
   );

   if (!isset($areaList[$area])) { return $monsterList; }

   $fileList = glob(APP_CONFIGDIR."/monster/$area/*.json");
 
   foreach ($fileList as $fileName) {
       $monsterId   = basename($fileName,".json");
       $monsterData = json_decode(file_get_contents($fileName),true);

       if (is_null($monsterData)) { continue; }

       if ($monsterData['hidden']) { continue; }
 
       $monsterList[$monsterId] = $monsterData;
   }

   return $monsterList;
}

function getLootFromMonsters($monsterList)
{
   $lootList = array();

   foreach ($monsterList as $monsterId => $monsterInfo) {
      $monsterLoot = $monsterInfo['loot'];

      foreach ($monsterLoot as $lootId => $lootInfo) {
         if ($lootList[$lootId]) { continue; }

         $lootData = json_decode(file_get_contents(APP_CONFIGDIR."/item/$lootId.json"),true);

         if (is_null($lootData)) { continue; }

         $lootList[$lootId] = $lootData;
      }
   }   

   return $lootList;
}

?>
