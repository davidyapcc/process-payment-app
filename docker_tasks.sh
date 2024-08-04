#!/bin/sh

# Check Docker container status
docker ps

# Run a command inside the Docker container
docker-compose exec app php bin/console cache:clear

# Run PHPUnit tests
docker-compose exec app ./vendor/bin/phpunit

# Perform an API call using curl for ACI
echo "Performing ACI API call..."
curl -X GET 'http://localhost:9000/payment/process/aci?amount=92&currency=EUR&card_number=4200000000000000&card_exp_month=05&card_exp_year=2034&card_cvv=123'
echo "\n\n"

# Perform an API call using curl for Shift4
echo "Performing Shift4 API call..."
curl -X GET 'http://localhost:9000/payment/process/shift4?amount=499&currency=USD&card_number=4242424242424242&card_exp_month=12&card_exp_year=2025&card_cvv=123'
echo "\n\n"
