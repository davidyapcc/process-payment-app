<?php

namespace App\Service;

use Exception;
use Psr\Log\LoggerInterface;
use Shift4\Shift4Gateway;
use Shift4\Exception\Shift4Exception;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class PaymentService
{
    private $httpClient;
    private $logger;

    public function __construct(HttpClientInterface $httpClient, LoggerInterface $logger)
    {
        $this->httpClient = $httpClient;
        $this->logger = $logger;
    }

    public function makePayment(string $provider, float $amount, string $currency, string $cardNumber, int $cardExpMonth, int $cardExpYear, string $cardCvv): array
    {
        if ($provider === 'aci') {
            return $this->processAciPayment($amount, $currency, $cardNumber, $cardExpMonth, $cardExpYear, $cardCvv);
        } elseif ($provider === 'shift4') {
            return $this->processShift4Payment($amount, $currency, $cardNumber, $cardExpMonth, $cardExpYear, $cardCvv);
        } else {
            $this->logger->error('Invalid provider specified', ['provider' => $provider]);
            return ['error' => 'Invalid provider specified'];
        }
    }

    private function processAciPayment(float $amount, string $currency, string $cardNumber, int $cardExpMonth, int $cardExpYear, string $cardCvv): array
    {
        $url = 'https://eu-test.oppwa.com/v1/payments';
        $formattedCardExpMonth = str_pad($cardExpMonth, 2, '0', STR_PAD_LEFT);
        $queryParams = [
            'entityId' => '8a8294174b7ecb28014b9699220015ca',
            'amount' => $amount,
            'currency' => 'EUR',
            'paymentBrand' => 'VISA',
            'paymentType' => 'PA',
            'card.number' => '4200000000000000',
            'card.expiryMonth' => $formattedCardExpMonth,
            'card.expiryYear' => $cardExpYear,
            'card.cvv' => $cardCvv,
        ];

        $this->logger->info('Processing ACI payment', $queryParams);

        try {
            $response = $this->httpClient->request('POST', $url, [
                'query' => $queryParams,
                'headers' => [
                    'Authorization' => 'Bearer OGE4Mjk0MTc0YjdlY2IyODAxNGI5Njk5MjIwMDE1Y2N8c3k2S0pzVDg=',
                ],
            ]);

            $content = $response->toArray();
            if (!isset($content['id']) || !isset($content['timestamp']) || !isset($content['amount']) || !isset($content['currency']) || !isset($content['card']['bin'])) {
                $this->logger->error('Invalid response from ACI', ['response' => $content]);
                return ['error' => 'Invalid response from ACI'];
            }
            $datetime = explode('.', $content['timestamp']);

            return [
                'transactionId' => $content['id'],
                'dateOfCreation' => $datetime[0],
                'amount' => $content['amount'],
                'currency' => $content['currency'],
                'cardBin' => $content['card']['bin'],
            ];
        } catch (Exception $e) {
            $errorMessage = $e->getMessage();
            $this->logger->error('Error processing ACI payment', ['errorMessage' => $errorMessage]);
            return ['error' => 'Error processing ACI payment', 'message' => $errorMessage];
        }
    }

    private function processShift4Payment(float $amount, string $currency, string $cardNumber, int $cardExpMonth, int $cardExpYear, string $cardCvv): array
    {
        $gateway = new Shift4Gateway('sk_test_tQ0hb2YMN4NLWe3b0zTml6o8');
        $request = [
            'amount' => $amount,
            'currency' => $currency,
            'card' => [
                'number' => '4242424242424242',
                'expMonth' => $cardExpMonth,
                'expYear' => $cardExpYear
            ]
        ];

        $this->logger->info('Processing Shift4 payment', $request);

        try {
            $charge = $gateway->createCharge($request);

            $this->logger->info('Charge response', $charge->toArray());

            if (null === $charge->getId() || null === $charge->getCreated() || null === $charge->getAmount() || null === $charge->getCurrency() || null === $charge->getCard()->getFirst6()) {
                $this->logger->error('Invalid response from Shift4', ['response' => $charge->toArray()]);
                return ['error' => 'Invalid response from Shift4'];
            }

            $chargeId = $charge->getId();
            $created = $charge->getCreated();
            $amount = $charge->getAmount();
            $currency = $charge->getCurrency();
            $cardBin = $charge->getCard()->getFirst6();

            return [
                'transactionId' => $chargeId,
                'dateOfCreation' => date('Y-m-d H:i:s', $created),
                'amount' => "$amount",
                'currency' => $currency,
                'cardBin' => $cardBin
            ];
        } catch (Shift4Exception $e) {
            $errorMessage = $e->getMessage();
            $this->logger->error('Error processing Shift4 payment', ['errorMessage' => $errorMessage]);
            return ['error' => 'Error processing Shift4 payment', 'message' => $errorMessage];
        } catch (Exception $e) {
            $errorMessage = $e->getMessage();
            $this->logger->error('Error processing Shift4 payment', ['errorMessage' => $errorMessage]);
            return ['error' => 'Error processing Shift4 payment', 'message' => $errorMessage];
        }
    }
}
