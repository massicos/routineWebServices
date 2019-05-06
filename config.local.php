<?php

function load_password_file($filename) {
	$fp = fopen($filename, "r");
	$content = fscanf($fp, "%s");
	fclose($fp);
	return($content[0]);
}

$config['displayErrorDetails'] = true;
$config['addContentLengthHeader'] = false;

$config['mongo']['host']   = 'localhost';
$config['mongo']['port'] = 27017;
$config['mongo']['dbname'] = 'test';

$config['mongo']['connectionString'] = "mongodb://" . 
$config['mongo']['host'] . "/" .
$config['mongo']['dbname'];