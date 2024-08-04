# Process Payment App

This application processes payments through specified providers (either ACI or Shift4) via an API endpoint and CLI command.

## Table of Contents

- [Setup and Installation](#setup-and-installation)
  - [Prerequisites](#prerequisites)
  - [Setup the Environment](#setup-the-environment)
  - [Running the Application](#running-the-application)
  - [Running Tests](#running-tests)
- [API Endpoint](#api-endpoint)
  - [URL](#url)
  - [Method](#method)
  - [Description](#description)
  - [URL Parameters](#url-parameters)
  - [Request Body Parameters](#request-body-parameters)
  - [Example Request](#example-request)
  - [Example Response](#example-response)
  - [Response Codes](#response-codes)
- [CLI Command](#cli-command)
  - [Command Name](#command-name)
  - [Description](#description)
  - [Arguments](#arguments)
  - [Options](#options)
  - [Example Usage](#example-usage)
  - [Example Output](#example-output)
  - [Response Codes](#response-codes)

## Setup and Installation

### Prerequisites
- Docker
- Docker Compose

### Setup the Environment
Clone the Repository:
```
git clone git@github.com:davidyapcc/process-payment-app.git
cd process-payment-app
```
Build and Start the Docker Containers:
```
docker-compose up -d --build
```
Install Dependencies:
```
docker-compose exec app composer install
```

### Running the Application
The application should be accessible at http://localhost:9000.

### Running Tests
Run All Tests:
```
docker-compose exec app ./vendor/bin/phpunit
```
Run Specific Tests:
```
docker-compose exec app ./vendor/bin/phpunit tests/Controller/PaymentControllerTest.php

docker-compose exec app ./vendor/bin/phpunit tests/Command/ProcessPaymentCommandTest.php

docker-compose exec app ./vendor/bin/phpunit tests/Service/PaymentServiceTest.php
```

## API Endpoint

### URL
`/payment/process/{provider}`

### Method
`POST`

### Description
This endpoint processes a payment through the specified provider (either ACI or Shift4).

### URL Parameters
- `provider` (required): The payment provider. Possible values: `aci`, `shift4`.

### Request Body Parameters
- `amount` (required): The amount to be charged. Example: `499`
- `currency` (required): The currency code. Example: `USD`
- `card_number` (required): The card number. Example: `4242424242424242`
- `card_exp_month` (required): The card expiration month. Example: `12`
- `card_exp_year` (required): The card expiration year. Example: `2025`
- `card_cvv` (required): The card CVV. Example: `123`

### Example Request
```
curl -X POST 'http://localhost:9000/payment/process/shift4' \
     -H 'Content-Type: application/json' \
     -d '{
           "amount": 499,
           "currency": "USD",
           "card_number": "4242424242424242",
           "card_exp_month": 12,
           "card_exp_year": 2025,
           "card_cvv": "123"
         }'
```

### Example Response
```bash
{
    "transactionId": "char_ORVCrwOrTkGsDwM3H50OIW7Q",
    "dateOfCreation": "2024-08-02 04:37:15",
    "amount": "499",
    "currency": "USD",
    "cardBin": "424242"
}
```

### Response Codes
```
200 OK: The payment was processed successfully.
400 Bad Request: Invalid input parameters.
500 Internal Server Error: An error occurred while processing the payment.
```

## CLI Command

### Command Name
`app:process-payment`

### Description
This command processes a payment via the specified provider (either ACI or Shift4).

### Arguments
- `provider` (required): The payment provider. Possible values: `aci`, `shift4`.

### Options
- `amount` (required): The amount to be charged. Example: `499`
- `currency` (required): The currency code. Example: `USD`
- `card_number` (required): The card number. Example: `4242424242424242`
- `card_exp_month` (required): The card expiration month. Example: `12`
- `card_exp_year` (required): The card expiration year. Example: `2025`
- `card_cvv` (required): The card CVV. Example: `123`

### Example Usage
```
php bin/console app:process-payment shift4 --amount=499 --currency=USD --card_number=4242424242424242 --card_exp_month=12 --card_exp_year=2025 --card_cvv=123
```

### Example Output
```bash
{
    "transactionId": "char_ORVCrwOrTkGsDwM3H50OIW7Q",
    "dateOfCreation": "2024-08-02 04:37:15",
    "amount": "499",
    "currency": "USD",
    "cardBin": "424242"
}
```

### Response Codes
```
0: The payment was processed successfully.
1: Invalid input parameters or an error occurred while processing the payment.
```
