<?php

namespace App\Tests\Command;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class ProcessPaymentCommandTest extends KernelTestCase
{
    public function testExecuteAciPayment()
    {
        $kernel = static::createKernel();
        $application = new Application($kernel);

        $command = $application->find('app:process-payment');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'provider' => 'aci',
            '--amount' => 92.00,
            '--currency' => 'EUR',
            '--card_number' => '4200000000000000',
            '--card_exp_year' => 2034,
            '--card_exp_month' => 5,
            '--card_cvv' => '123',
        ]);

        $output = $commandTester->getDisplay();
        $this->assertJson($output);

        $responseData = json_decode($output, true);
        $this->assertArrayHasKey('transactionId', $responseData);
        $this->assertArrayHasKey('dateOfCreation', $responseData);
        $this->assertArrayHasKey('amount', $responseData);
        $this->assertArrayHasKey('currency', $responseData);
        $this->assertArrayHasKey('cardBin', $responseData);
    }

    public function testExecuteShift4Payment()
    {
        $kernel = static::createKernel();
        $application = new Application($kernel);

        $command = $application->find('app:process-payment');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'provider' => 'shift4',
            '--amount' => 499,
            '--currency' => 'USD',
            '--card_number' => '4242424242424242',
            '--card_exp_year' => 2025,
            '--card_exp_month' => 12,
            '--card_cvv' => '123',
        ]);

        $output = $commandTester->getDisplay();
        $this->assertJson($output);

        $responseData = json_decode($output, true);
        $this->assertArrayHasKey('transactionId', $responseData);
        $this->assertArrayHasKey('dateOfCreation', $responseData);
        $this->assertArrayHasKey('amount', $responseData);
        $this->assertArrayHasKey('currency', $responseData);
        $this->assertArrayHasKey('cardBin', $responseData);
    }
}
