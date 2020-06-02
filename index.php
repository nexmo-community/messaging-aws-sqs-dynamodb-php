<?php

declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

use Aws\Sqs\SqsClient;
use Aws\DynamoDb\DynamoDbClient;
use Aws\DynamoDb\Exception\DynamoDbException;
use Aws\DynamoDb\Marshaler;
use Aws\Exception\AwsException;

return function ($event) {

    // UPDATE THESE VARIABLES AS NEEDED
    $queueUrl = "<your-sqs-url>";
    $DynamoDbTableName = '<your-dynamodb-table-name>';
    $region = 'us-east-1';
    $version = 'latest';

    $message = $event['Records'][0];

    $sqsClient = new SqsClient([
        'region' => $region,
        'version' => $version
    ]);

    $dynamoClient = new DynamoDbClient([
        'region' => $region,
        'version' => $version
    ]);

    // connect to DynamoDB, add the record, and remove from SQS
    if (!empty($message)) {
        try {
            $marshaller = new Marshaler();
            $item = $marshaller->marshalJson(json_encode($message));

            $params = [
                'TableName' => $DynamoDbTableName,
                'Item' => $item
            ];

            $response = $dynamoClient->putItem($params);
            echo "Added item!\n";

        } catch (DynamoDbException $e) {
            echo "Unable to add item:\n";
            echo $e->getMessage() . "\n";
        }

        // remove record from SQS as its confirmed in dynamo
        try {
            if ($response->statusCode = 200) {
                $sqsClient->deleteMessage([
                    'QueueUrl' => $queueUrl, // REQUIRED
                    'ReceiptHandle' => $message['receiptHandle']
                ]);
            }

        } catch (AwsException $e) {
            error_log($e->getMessage());
        }

    } else {
        echo "No message in queue. \n";
    }

    return "Message moved to DynamoDB: " . $message['messageId'];
};
