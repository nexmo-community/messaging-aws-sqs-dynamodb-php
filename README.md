# vonage/messaging-aws-sqs-dynamodb-php

AWS Lambda function created in PHP that once triggered, moves a message from SQS into DynamoDB.

## Prerequisites

* PHP 7.4 (update `serverless.yml` for other versions)
* Composer installed [globally](https://getcomposer.org/doc/00-intro.md#globally)
* [Node.js](https://nodejs.org/en/) and npm
* [Serverless Framework](https://serverless.com/framework/docs/getting-started/)
* [AWS account](https://aws.amazon.com/)

## Setup Instructions

Clone this repo from GitHub, and navigate into the newly created directory to proceed.

### Use Composer to install dependencies

This example requires the use of Composer to install dependencies and set up the autoloader.

Assuming a Composer global installation. [https://getcomposer.org/doc/00-intro.md#globally](https://getcomposer.org/doc/00-intro.md#globally)

```
composer install
```

### AWS Setup

You will need to create [AWS credentials](https://www.serverless.com/framework/docs/providers/aws/guide/credentials/) as indicated by `Serverless`.

Also, create a new [SQS queue](https://aws.amazon.com/sqs/) using the default settings, and update `serverless.yml` with the ARN for the placeholder `<your-sqs-arn>`.

Lastly, create a new [DynamoDB table](https://aws.amazon.com/dynamodb/) using the default settings, and update `serverless.yml` with the ARN for the placeholder `<your-dynamodb-table-arn>`.

> Note: Ensure the primary key field name you set for the DynamoDB table matches the message ID in your SQS queue items. For this example we used `messageId`.

### Update Environment

Rename the provided `.env.default` file to `.env`:

```
mv .env.example .env
```

Then update the values as needed from `AWS` and `DynamoDB`, and save.

```
AWS_REGION=us-east-1
AWS_VERSION=latest
DYNAMODB_TABLE_NAME=
```

### Deploy to Lambda

With all the above updated successfully, you can now use `Serverless` to deploy the app to [AWS Lambda](https://aws.amazon.com/lambda/).

```bash
serverless deploy
```

### Invoke

If there are already messages in SQS, you can test the migration of these from `SQS` to `DynamoDB` by invoking the function by using `Serverless` locally:

```bash
serverless invoke -f hello
```

> Note: Above shows the use of function name `hello` as created in the default `serverless.yml` in this example.

For testing, you can add messages to SQS through the AWS Console website, or you can look at [this repo](https://github.com/nexmo-community/sms-aws-sqs-python-sender) for an example of how to add `SQS` messages through a typical `HTTP POST` request containing `JSON`.

### Automate

To automate the usage of this function, you can add the newly created `Lambda` as a [Lambda Trigger](https://docs.aws.amazon.com/AWSSimpleQueueService/latest/SQSDeveloperGuide/sqs-configure-lambda-function-trigger.html) for your `SQS` instance.

By adding the trigger, it ensures that any new `SQS` messages call the `Lambda` function to automatically move the message to `DynamoDB`, therefore, removing the message from `SQS`.

## Contributing

We love questions, comments, issues - and especially pull requests. Either open an issue to talk to us, or reach us on twitter: <https://twitter.com/VonageDev>.
