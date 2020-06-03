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
    $DynamoDbTableName = '<your-dynamodb-table-name>';
    $region = 'us-east-1';
    $version = 'latest';

    $dynamoClient = new DynamoDbClient([
        'region' => $region,
        'version' => $version
    ]);

    // connect to DynamoDB, add the record, and remove from SQS
    if (!empty($event['Records'])) {
        foreach ($event['Records'] as $value) {
            try {
                $marshaller = new Marshaler();
                $item = $marshaller->marshalJson(json_encode($value));

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
        }

    } else {
        echo "No message in queue. \n";
    }

    return "Message moved to DynamoDB!";
};
