<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
echo "PHP OK: " . PHP_VERSION;
echo "<br>Dir: " . __DIR__;
echo "<br>open_basedir: " . ini_get('open_basedir');
