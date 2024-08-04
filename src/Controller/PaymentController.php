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

    #[Route('/payment/process/{provider}', name: 'process_payment', methods: ['POST'])]
    public function processPayment(Request $request, string $provider): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $amount = (float) $data['amount'];
        $currency = $data['currency'];
        $cardNumber = $data['card_number'];
        $cardExpYear = (int) $data['card_exp_year'];
        $cardExpMonth = (int) $data['card_exp_month'];
        $cardCvv = $data['card_cvv'];

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
