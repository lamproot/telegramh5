<?php

// require_once "./sqs.php";

// $sqs = new SQS();
require_once('sqs.php');
//$sqs = new SQS('AKIAIDVXCZFV53V4562A', 'XecsgyuFIN9HeOhEcKNkjtHkVVwP40jaP9pHqiBz');
// $result = $sqs->listQueues('TokenManSendMsg.fifo');
// echo json_encode($result);exit;

$sqs = new SQS('AKIAIDVXCZFV53V4562A', 'XecsgyuFIN9HeOhEcKNkjtHkVVwP40jaP9pHqiBz', SQS::ENDPOINT_US_EAST);
//$queue1 = 'https://sqs.us-east-2.amazonaws.com/426025407280/TokenManSendMsg.fifo';
//$sqs->getQueueAttributes($queue1);
// // var_dump($res);exit;

// $response = $sqs->listQueues();
// $queue = $respose['Queues'][0];
$response = $sqs->createQueue('testQueue');
$queue = $response['QueueUrl'];
// print_r($response);

$response = $sqs->sendMessage($queue, 'This is a test message'.rand(1,10000));
print_r($response);