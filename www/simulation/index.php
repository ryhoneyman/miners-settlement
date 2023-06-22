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
   'adminlte'       => true,
));

$main->title('Simulation Home');

include 'ui/header.php';

print pageDisplay($main);

include 'ui/footer.php';

?>
<?php

function pageDisplay($main)
{
   $alte           = $main->obj('adminlte');
   $simEntitlement = $main->getProfileEntitlement('simulation-usage',false);
   $overrideAuth   = true;

   if (!$simEntitlement && !$overrideAuth) { return $alte->displayRow($alte->displayCard("You do not currently have any simulation privileges.",array('title' => 'Warning', 'header' => 'bg-danger'))); }

   return $alte->displayRow(
             $alte->infoBox('Scalable Simulation','/simulation/scalable/','Run simulations by adjusting scalable parameters.',
                            array('icon' => 'fa-chart-bar', 'icon-bg' => 'bg-primary', 'ribbon' => 'popular', 'ribbon-bg' => 'bg-success text-bold text-small')).
             $alte->infoBox('Build Simulation','/simulation/build/','Run simulations by loading player builds.',
                            array('icon' => 'fa-user-chart', 'icon-bg' => 'bg-warning', 'ribbon' => 'new', 'ribbon-bg' => 'bg-danger')).
             ''
          );
}

?>
