<?php

namespace App\Tests\Service;

use App\Service\PaymentService;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Shift4\Shift4Gateway;

class PaymentServiceTest extends TestCase
{
    private $httpClient;
    private $logger;
    private $shift4Gateway;
    private $paymentService;

    protected function setUp(): void
    {
        $this->httpClient = $this->createMock(HttpClientInterface::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->shift4Gateway = $this->createMock(Shift4Gateway::class);

        $this->paymentService = new PaymentService($this->httpClient, $this->logger);
    }

    public function testProcessAciPaymentSuccess()
    {
        $response = $this->createMock(ResponseInterface::class);
        $response->method('toArray')->willReturn([
            'id' => '8ac7a4a091110c2101911160b2c16525',
            'timestamp' => '2024-08-02 04:37:15.701+0000',
            'amount' => '92.00',
            'currency' => 'EUR',
            'card' => [
                'bin' => '420000',
            ],
        ]);

        $this->httpClient->method('request')->willReturn($response);

        $result = $this->paymentService->makePayment('aci', 92.00, 'EUR', '4200000000000000', 5, 2034, '123');

        $this->assertSame('8ac7a4a091110c2101911160b2c16525', $result['transactionId']);
        $this->assertSame('2024-08-02 04:37:15', $result['dateOfCreation']);
        $this->assertSame('92.00', $result['amount']);
        $this->assertSame('EUR', $result['currency']);
        $this->assertSame('420000', $result['cardBin']);
    }

    public function testProcessShift4PaymentSuccess()
    {
        $this->shift4Gateway = $this->getMockBuilder('Shift4\Shift4Gateway')
            ->disableOriginalConstructor()
            ->onlyMethods(['createCharge'])
            ->getMock();

        $charge = new class {
            public function getId() { return 'char_ocZ5EqYFGfoiuoJSkXwC3Rm1'; }
            public function getCreated() { return 1415810511; }
            public function getAmount() { return 499; }
            public function getCurrency() { return 'USD'; }
            public function getCard() { return new class {
                public function getFirst6() { return '424242'; }
            }; }
        };

        $this->shift4Gateway->method('createCharge')->willReturn($charge);

        $result = $this->paymentService->makePayment('shift4', 499, 'USD', '4242424242424242', 12, 2025, '123');

        $this->assertNotEmpty($result['transactionId']);
        $this->assertNotEmpty($result['dateOfCreation']);
        $this->assertSame('499', $result['amount']);
        $this->assertSame('USD', $result['currency']);
        $this->assertSame('424242', $result['cardBin']);
    }
}
