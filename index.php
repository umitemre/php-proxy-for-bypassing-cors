<?php

require('UserRequest.php');
require('UserResponse.php');

use App\UserRequest;
use App\UserResponse;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: *');
header('Content-Type: application/json');

try {
    $user_request = new UserRequest();
} catch (Exception $e) {
    $response = UserResponse::createFromException($e);
    return print($response);
}

echo $user_request->execute();
