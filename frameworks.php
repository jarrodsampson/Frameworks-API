<?php

// Database Information
include_once('AccessClass.php');
include_once('Queries.php');
include_once('FrameworkAccessClass.php');

$frameobj = new FrameworkAccessClass;

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    
    $frameobj->getRequestParam();
    
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $frameobj->postFramework();
    
}

if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    
    $frameobj->deleteFramework();
    
}


if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    
    $frameobj->updateFramework();
    
}


?>