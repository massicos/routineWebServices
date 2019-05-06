<?php

require 'vendor/autoload.php';
require 'config.php';

$app = new \Slim\App(['settings' => $config]);

$container = $app->getContainer();

// monolog
$container['logger'] = function($c) {
    $logger = new \Monolog\Logger('my_logger');
    $file_handler = new \Monolog\Handler\StreamHandler('../logs/app.log');
    $logger->pushHandler($file_handler);
    return $logger;
};

$app->get('/routine/familly/{name}', function ($request, $response, $args) {
    session_start();
    //$manager = new MongoDB\Driver\Manager("mongodb://localhost:27017");

    $this->logger->addInfo($this->get('settings')['mongo']['connectionString']);
    $manager = new MongoDB\Driver\Manager($this->get('settings')['mongo']['connectionString']);

    $query = new MongoDB\Driver\Query(["name" => $args['name']], []);

    $cursor = $manager->executeQuery('test.familly', $query);

    $found = false;
    foreach ($cursor as $document) {
        $this->logger->addInfo('Boucle');
        $found = true;
        $_SESSION['idFamilly'] = 1;
        break;
    }
    //$a = $cursor->toArray();
    //$this->logger->addInfo(var_dump($a));
    if ($found) {
        return $response->withJson($document, 200, JSON_UNESCAPED_SLASHES);
    }
    return $response->withStatus(200);
    
});

$app->get('/routine/childs', function ($request, $response, $args) {
    session_start();
    $manager = new MongoDB\Driver\Manager($this->get('settings')['mongo']['connectionString']);
    
    //$idFamilly = $_SESSION['idFamilly'];
    $idFamilly = 1;
    
    $query = new MongoDB\Driver\Query(["idfamilly" => $idFamilly], []);

    $cursor = $manager->executeQuery('test.child', $query);

    $found = false;
    $childs = array();
    foreach ($cursor as $document) {
        $this->logger->addInfo('Boucle child - ' . $document->surname);
        $found = true;

        $queryLog = new MongoDB\Driver\Query(["idFamilly" => $idFamilly, "idChild" => $document->id], []);

        $cursorLog = $manager->executeQuery('test.log', $queryLog);

        $document->stars = 0;
        $document->medals = 0;
        foreach($cursorLog as $log) {
            $this->logger->addInfo('Boucle log');
            $this->logger->addInfo($log->event);
            $this->logger->addInfo($log->star);
            $document->stars += $log->star;
            $document->medals += $log->medal;
        }
        $this->logger->addInfo('-------');
        array_push($childs, $document);
    }
    //$a = $cursor->toArray();
    //$this->logger->addInfo(var_dump($a));
    if ($found) {
        return $response->withJson($childs, 200, JSON_UNESCAPED_SLASHES);
    }
    return $response->withStatus(200);
    
});

$app->post('/routine/log/stepComplete', function ($request, $response, $args) {
    $parsedBody = $request->getParsedBody();

    $idFamilly = 1;
    $idChild = $parsedBody["idChild"];
    $stars = $parsedBody["stars"];
    $medal = $parsedBody["medal"];

    $bulk = new MongoDB\Driver\BulkWrite;

    $bulk->insert([
        "id" => time(),
        "idFamilly" => $idFamilly,
        "idChild" => (int) $idChild,
        "event" => "StepComplete",
        "star" => (int) $stars,
        "medal" => (int) $medal
    ]);

    $manager = new MongoDB\Driver\Manager($this->get('settings')['mongo']['connectionString']);
    $result = $manager->executeBulkWrite('test.log', $bulk);

    return $response->withStatus(201);
});

$app->post('/routine/log/routineComplete', function ($request, $response, $args) {
    $parsedBody = $request->getParsedBody();

    $idFamilly = 1;
    $idChild = $parsedBody["idChild"];
    $stars = $parsedBody["stars"];
    $medal = $parsedBody["medal"];

    $bulk = new MongoDB\Driver\BulkWrite;

    $bulk->insert([
        "id" => time(),
        "idFamilly" => $idFamilly,
        "idChild" => (int) $idChild,
        "event" => "routineComplete",
        "star" => (int) $stars,
        "medal" => (int) $medal
    ]);

    $manager = new MongoDB\Driver\Manager($this->get('settings')['mongo']['connectionString']);
    $result = $manager->executeBulkWrite('test.log', $bulk);

    return $response->withStatus(201);
});

$app->get('/routine/routines-list', function ($request, $response, $args) {
    session_start();
    $manager = new MongoDB\Driver\Manager($this->get('settings')['mongo']['connectionString']);
    
    //$idFamilly = $_SESSION['idFamilly'];
    $idFamilly = 1;
    
    $query = new MongoDB\Driver\Query(["idfamilly" => $idFamilly], []);

    $cursor = $manager->executeQuery('test.routine', $query);

    $found = false;
    $routines = array();
    foreach ($cursor as $document) {
        $this->logger->addInfo('Boucle');
        $found = true;
        array_push($routines, $document);
    }
    //$a = $cursor->toArray();
    //$this->logger->addInfo(var_dump($a));
    if ($found) {
        return $response->withJson($routines, 200, JSON_UNESCAPED_SLASHES);
    }
    return $response->withStatus(200);
    
});

$app->options('/{routes:.+}', function ($request, $response, $args) {
    return $response;
});

$app->add(function ($req, $res, $next) {
    $response = $next($req, $res);
    return $response
            ->withHeader('Access-Control-Allow-Origin', 'http://localhost:4200')
            ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS');
});

// Catch-all route to serve a 404 Not Found page if none of the routes match
// NOTE: make sure this route is defined last
$app->map(['GET', 'POST', 'PUT', 'DELETE', 'PATCH'], '/{routes:.+}', function($req, $res) {
    $handler = $this->notFoundHandler; // handle using the default Slim page not found handler
    return $handler($req, $res);
});

$app->run();