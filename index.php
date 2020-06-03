<?php

declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

use Aws\DynamoDb\DynamoDbClient;
use Aws\DynamoDb\Exception\DynamoDbException;
use Aws\DynamoDb\Marshaler;
use Symfony\Component\Yaml\Yaml;

return function ($event) {

    $config = Yaml::parseFile('config.yml');

    $dynamoClient = new DynamoDbClient([
        'region' => $config['AWS_REGION'],
        'version' => $config['AWS_VERSION']
    ]);

    // connect to DynamoDB, add the record, and remove from SQS
    if (!empty($event['Records'])) {
        foreach ($event['Records'] as $value) {
            try {
                $marshaller = new Marshaler();
                $item = $marshaller->marshalJson(json_encode($value));

                $params = [
                    'TableName' => $config['AWS_DYNAMODB_TABLE_NAME'],
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
