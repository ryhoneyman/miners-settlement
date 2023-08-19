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
   'request'        => false,
   'input'          => true,
   'html'           => true,
   'adminlte'       => true,
));

$main->title('Color Picker');

$input = $main->obj('input');
$html  = $main->obj('html');
$alte  = $main->obj('adminlte');

include 'ui/header.php';

print colorPickerDisplay($main);

print "<script src='/assets/js/colorpicker.js?t=".$main->now."' type='text/javascript'></script>\n".
      "<link rel='stylesheet' href='/assets/css/colorpicker.css?t=".$main->now."'>\n";

include 'ui/footer.php';

?>
<?php

function colorPickerDisplay($main)
{
   $alte = $main->obj('adminlte');

   return $alte->displayRow($alte->displayCard(file_get_contents('content.html'),array('container' => 'col-12 col-xl-6 col-lg-9'))).
          '<div><h5>Written and contributed by <span class="text-red">UserError</span>.</h5></div>';
}

?>
