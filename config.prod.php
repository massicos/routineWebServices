<?php

function load_password_file($filename) {
	$fp = fopen($filename, "r");
	$content = fscanf($fp, "%s");
	fclose($fp);
	return($content[0]);
}

$config['displayErrorDetails'] = true;
$config['addContentLengthHeader'] = false;

$config['mongo']['host']   = 'clustera-shard-00-00-s8w9x.azure.mongodb.net:27017,clustera-shard-00-01-s8w9x.azure.mongodb.net:27017,clustera-shard-00-02-s8w9x.azure.mongodb.net:27017';
$config['mongo']['port'] = 27017;
$config['mongo']['dbname'] = 'test';
$config['mongo']['dbuser'] = 'routineuser';
$config['mongo']['dbuserpassword'] = rawurlencode(load_password_file("/vagrant/data/mongodb/routineuser.txt"));

$config['mongo']['connectionString'] = "mongodb://" . 
$config['mongo']['dbuser'] . ":" .
$config['mongo']['dbuserpassword'] . "@" .
$config['mongo']['host'] . "/" .
$config['mongo']['dbname'] .
"?ssl=true&replicaSet=ClusterA-shard-0&authSource=admin&retryWrites=true";