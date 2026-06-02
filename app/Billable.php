<?php

namespace App;

use App\Models\Payment;
use Stripe\StripeClient;

trait Billable
{
    protected $stripe;

    public function initializeBillable(): void
    {
        $this->stripe = new StripeClient(config('services.stripe.secret'));
    }

    public function processPayment(string $paymentMethodId, float $amount, string $currency = 'eur'): Payment
    {
        try {
            // Créer l'intention de paiement Stripe
            $paymentIntent = $this->stripe->paymentIntents->create([
                'amount' => (int)($amount * 100), // Conversion en centimes
                'currency' => $currency,
                'payment_method' => $paymentMethodId,
                'confirmation_method' => 'manual',
                'confirm' => true,
                'metadata' => [
                    'order_id' => $this->id,
                    'order_number' => $this->order_number,
                    'customer_id' => $this->user_id,
                ],
            ]);

            // Enregistrer le paiement
            $payment = Payment::create([
                'order_id' => $this->id,
                'user_id' => $this->user_id,
                'amount' => $amount,
                'currency' => $currency,
                'status' => $paymentIntent->status === 'succeeded' ? 'completed' : 'pending',
                'gateway' => 'stripe',
                'transaction_id' => $paymentIntent->id,
                'metadata' => [
                    'payment_intent_id' => $paymentIntent->id,
                    'client_secret' => $paymentIntent->client_secret,
                ],
            ]);

            // Mettre à jour le statut de la commande
            if ($payment->status === 'completed') {
                $this->update([
                    'payment_status' => 'paid',
                    'payment_id' => $paymentIntent->id,
                    'payment_gateway' => 'stripe',
                    'paid_at' => now(),
                ]);
            }

            return $payment;
        } catch (\Exception $e) {
            // Enregistrer l'échec
            Payment::create([
                'order_id' => $this->id,
                'user_id' => $this->user_id,
                'amount' => $amount,
                'currency' => $currency,
                'status' => 'failed',
                'gateway' => 'stripe',
                'metadata' => [
                    'error' => $e->getMessage(),
                ],
            ]);

            throw $e;
        }
    }

    public function refund(float $amount = null, string $reason = ''): Payment
    {
        if ($this->payment_status !== 'paid') {
            throw new \Exception('Order has not been paid');
        }

        try {
            $refundAmount = $amount ?? $this->total;
            
            $refund = $this->stripe->refunds->create([
                'payment_intent' => $this->payment_id,
                'amount' => (int)($refundAmount * 100),
                'reason' => 'requested_by_customer',
                'metadata' => [
                    'order_id' => $this->id,
                    'reason' => $reason,
                ],
            ]);

            $payment = Payment::create([
                'order_id' => $this->id,
                'user_id' => $this->user_id,
                'amount' => -$refundAmount,
                'currency' => $this->currency ?? 'eur',
                'status' => $refund->status === 'succeeded' ? 'completed' : 'pending',
                'type' => 'refund',
                'gateway' => 'stripe',
                'transaction_id' => $refund->id,
                'metadata' => [
                    'refund_id' => $refund->id,
                    'reason' => $reason,
                ],
            ]);

            // Mettre à jour le statut
            $newStatus = $refundAmount >= $this->total ? 'refunded' : 'partially_refunded';
            $this->update(['payment_status' => $newStatus]);

            return $payment;
        } catch (\Exception $e) {
            throw new \Exception('Refund failed: ' . $e->getMessage());
        }
    }
}
