<?php

$info = array(
   'SERVER' => $_SERVER,
);

print "<pre>\n";

print json_encode($info,JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT)."\n";

print "</pre>\n";

?>
