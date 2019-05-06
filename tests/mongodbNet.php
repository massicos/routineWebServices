<?php

//mongodb://routineuser:<password>@clustera-shard-00-00-s8w9x.azure.mongodb.net:27017,clustera-shard-00-01-s8w9x.azure.mongodb.net:27017,clustera-shard-00-02-s8w9x.azure.mongodb.net:27017/test?ssl=true&replicaSet=ClusterA-shard-0&authSource=admin&retryWrites=true

$config['mongo']['host']   = 'clustera-shard-00-00-s8w9x.azure.mongodb.net:27017,clustera-shard-00-01-s8w9x.azure.mongodb.net:27017,clustera-shard-00-02-s8w9x.azure.mongodb.net:27017';
##$config['mongo']['host']   = 'clustera-s8w9x.azure.mongodb.net';
$config['mongo']['port'] = 27017;
$config['mongo']['dbname'] = 'test';
$config['mongo']['dbuser'] = 'routineuser';
$config['mongo']['dbuserpassword'] = rawurlencode('eeBahphuSh9hoht7');

//$config['mongo']['connectionString'] = "mongodb+srv://" . 
$config['mongo']['connectionString'] = "mongodb://" . 
$config['mongo']['dbuser'] . ":" .
$config['mongo']['dbuserpassword'] . "@" .
$config['mongo']['host'] . "/" .
$config['mongo']['dbname'] .
//"?retryWrites=true";
"?ssl=true&replicaSet=ClusterA-shard-0&authSource=admin&retryWrites=true";

echo "--- START ---\n";

$manager = new MongoDB\Driver\Manager($config['mongo']['connectionString']);

$query = new MongoDB\Driver\Query(["name" => "Massicotte"], []);

$cursor = $manager->executeQuery('test.familly', $query);

$found = false;
foreach ($cursor as $document) {
    echo("Boucle\n");
    echo("$document->name, $document->valueByStar\n");
    $found = true;
    break;
}

echo "--- END ---\n";