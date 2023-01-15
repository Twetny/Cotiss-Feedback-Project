<?php
$feedback = $_POST['feedback'];
$rating = $_POST['rating'];



// convert rating to number
if ($rating == '1') {
    $rating = 1;
} elseif ($rating == '2') {
    $rating = 2;
} elseif ($rating == '3') {
    $rating = 3;
} else {
    $rating = 0;
}

// Create connection to AWS dynamoDB
require 'vendor/autoload.php';

use Aws\DynamoDb\DynamoDbClient;

$client = DynamoDbClient::factory(array(
    'region' => 'ap-southeast-2',
    'version' => 'latest'
));

$tableName = 'Cotiss-Feedback-DB';

$response = $client->putItem(array(
    'TableName' => $tableName,
    'Item' => $client->formatAttributes(array(
        'id' => getUniqueId($client, $tableName),
        'feedback' => $feedback,
        'rating' => $rating
    ))
));

function getUniqueId($client, $tableName) {
    // get largest id
    $response = $client->scan(array(
        'TableName' => $tableName,
        'ProjectionExpression' => 'id'
    ));

    $items = $response->get('Items');
    $ids = array();

    foreach ($items as $item) {
        $ids[] = $item['id']['N'];
    }

    $maxId = max($ids);

    // return largest id + 1
    return $maxId + 1;
}

header('Location: index.php');
