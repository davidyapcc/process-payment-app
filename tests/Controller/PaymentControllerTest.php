<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class PaymentControllerTest extends WebTestCase
{
    public function testProcessAciPayment()
    {
        $client = static::createClient();

        $client->request('GET', '/payment/process/aci', [
            'amount' => 92,
            'currency' => 'EUR',
            'card_number' => '4200000000000000',
            'card_exp_month' => 5,
            'card_exp_year' => 2034,
            'card_cvv' => '123'
        ]);

        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());
        $responseContent = $client->getResponse()->getContent();
        $this->assertJson($responseContent);

        $responseData = json_decode($responseContent, true);
        $this->assertArrayHasKey('transactionId', $responseData);
        $this->assertArrayHasKey('dateOfCreation', $responseData);
        $this->assertArrayHasKey('amount', $responseData);
        $this->assertArrayHasKey('currency', $responseData);
        $this->assertArrayHasKey('cardBin', $responseData);
    }

    public function testProcessShift4Payment()
    {
        $client = static::createClient();

        $client->request('GET', '/payment/process/shift4', [
            'amount' => 499,
            'currency' => 'USD',
            'card_number' => '4242424242424242',
            'card_exp_month' => 12,
            'card_exp_year' => 2025,
            'card_cvv' => '123'
        ]);

        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());
        $responseContent = $client->getResponse()->getContent();
        $this->assertJson($responseContent);

        $responseData = json_decode($responseContent, true);
        $this->assertArrayHasKey('transactionId', $responseData);
        $this->assertArrayHasKey('dateOfCreation', $responseData);
        $this->assertArrayHasKey('amount', $responseData);
        $this->assertArrayHasKey('currency', $responseData);
        $this->assertArrayHasKey('cardBin', $responseData);
    }
}
