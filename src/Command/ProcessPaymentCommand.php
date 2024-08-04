<?php

namespace App\Command;

use App\Service\PaymentService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:process-payment',
    description: 'Process payment via CLI'
)]
class ProcessPaymentCommand extends Command
{
    private $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->addArgument('provider', InputArgument::REQUIRED, 'The payment provider (aci or shift4)')
            ->addOption('amount', null, InputOption::VALUE_REQUIRED, 'The amount to be charged')
            ->addOption('currency', null, InputOption::VALUE_REQUIRED, 'The currency')
            ->addOption('card_number', null, InputOption::VALUE_REQUIRED, 'The card number')
            ->addOption('card_exp_month', null, InputOption::VALUE_REQUIRED, 'The card expiration month')
            ->addOption('card_exp_year', null, InputOption::VALUE_REQUIRED, 'The card expiration year')
            ->addOption('card_cvv', null, InputOption::VALUE_REQUIRED, 'The card CVV');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $provider = $input->getArgument('provider');
        $amount = (float) $input->getOption('amount');
        $currency = $input->getOption('currency');
        $cardNumber = $input->getOption('card_number');
        $cardExpMonth = (int) $input->getOption('card_exp_month');
        $cardExpYear = (int) $input->getOption('card_exp_year');
        $cardCvv = $input->getOption('card_cvv');

        if (!$amount) {
            $output->writeln(json_encode([
                'error' => 'Invalid input parameter',
                'message' => 'Missing amount parameter'
            ]));
            return Command::FAILURE;
        }
        if (!$currency) {
            $output->writeln(json_encode([
                'error' => 'Invalid input parameter',
                'message' => 'Missing currency parameter'
            ]));
            return Command::FAILURE;
        }
        if (!$cardNumber) {
            $output->writeln(json_encode([
                'error' => 'Invalid input parameter',
                'message' => 'Missing card_number parameter'
            ]));
            return Command::FAILURE;
        }
        if (!$cardExpMonth) {
            $output->writeln(json_encode([
                'error' => 'Invalid input parameter',
                'message' => 'Missing card_exp_month parameter'
            ]));
            return Command::FAILURE;
        }
        if (!$cardExpYear) {
            $output->writeln(json_encode([
                'error' => 'Invalid input parameter',
                'message' => 'Missing card_exp_year parameter'
            ]));
            return Command::FAILURE;
        }
        if (!$cardCvv) {
            $output->writeln(json_encode([
                'error' => 'Invalid input parameter',
                'message' => 'Missing card_cvv parameter'
            ]));
            return Command::FAILURE;
        }

        $response = $this->paymentService->makePayment($provider, $amount, $currency, $cardNumber, $cardExpMonth, $cardExpYear, $cardCvv);

        $output->writeln(json_encode($response));

        return Command::SUCCESS;
    }
}
