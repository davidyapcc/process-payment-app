<?php

namespace App\Controller;

use App\Service\PaymentService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class PaymentController extends AbstractController
{
    private $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    #[Route('/payment/process/{provider}', name: 'process_payment', methods: ['GET'])]
    public function processPayment(Request $request, string $provider): JsonResponse
    {
        $amount = (float) $request->query->get('amount');
        $currency = $request->query->get('currency');
        $cardNumber = $request->query->get('card_number');
        $cardExpMonth = (int) $request->query->get('card_exp_month');
        $cardExpYear = (int) $request->query->get('card_exp_year');
        $cardCvv = $request->query->get('card_cvv');

        if (!$amount) {
            return new JsonResponse([
                'error' => 'Invalid input parameter',
                'message' => 'Missing amount parameter'
            ], 400);
        }
        if (!$currency) {
            return new JsonResponse([
                'error' => 'Invalid input parameter',
                'message' => 'Missing currency parameter'
            ], 400);
        }
        if (!$cardNumber) {
            return new JsonResponse([
                'error' => 'Invalid input parameter',
                'message' => 'Missing card_number parameter'
            ], 400);
        }
        if (!$cardExpMonth) {
            return new JsonResponse([
                'error' => 'Invalid input parameter',
                'message' => 'Missing card_exp_month parameter'
            ], 400);
        }
        if (!$cardExpYear) {
            return new JsonResponse([
                'error' => 'Invalid input parameter',
                'message' => 'Missing card_exp_year parameter'
            ], 400);
        }
        if (!$cardCvv) {
            return new JsonResponse([
                'error' => 'Invalid input parameter',
                'message' => 'Missing card_cvv parameter'
            ], 400);
        }

        $response = $this->paymentService->makePayment($provider, $amount, $currency, $cardNumber, $cardExpMonth, $cardExpYear, $cardCvv);

        return new JsonResponse($response);
    }
}
