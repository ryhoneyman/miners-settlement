<?php

$info = array(
   'SERVER' => $_SERVER,
);

unset($info['SERVER']['DOCUMENT_ROOT']);
unset($info['SERVER']['SCRIPT_FILENAME']);

print "<pre>\n";

print json_encode($info,JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT)."\n";

print "</pre>\n";

?>
