<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CameroonianPaymentService
{
    /**
     * Prépare une transaction de paiement pour une plateforme mobile money camerounaise
     * 
     * @param array $data Données de la transaction
     * @return array
     */
    public static function prepareTransaction($data)
    {
        $paymentMethod = $data['payment_method'] ?? null;
        $amount = $data['amount'] ?? 0;
        $phone = $data['phone'] ?? null;
        $orderId = $data['order_id'] ?? null;
        
        // Valider le téléphone
        $phoneValidation = PhoneValidationService::validate($phone);
        if (!$phoneValidation['valid']) {
            return [
                'success' => false,
                'message' => $phoneValidation['error'],
            ];
        }
        
        $transaction = [
            'reference' => self::generateReference($orderId),
            'amount' => $amount,
            'currency' => 'XAF',
            'phone' => $phoneValidation['formatted'],
            'operator' => $phoneValidation['operator'],
            'payment_method' => $paymentMethod,
            'timestamp' => now(),
            'status' => 'pending',
        ];
        
        return [
            'success' => true,
            'transaction' => $transaction,
        ];
    }
    
    /**
     * Génère une référence de transaction unique
     * 
     * @param string $orderId ID de la commande
     * @return string
     */
    public static function generateReference($orderId)
    {
        return 'TXN_' . strtoupper($orderId) . '_' . time();
    }
    
    /**
     * Formate un montant en FCFA
     * 
     * @param float $amount Montant
     * @return string
     */
    public static function formatAmount($amount)
    {
        return number_format($amount, 0, ',', ' ') . ' FCFA';
    }
    
    /**
     * Récupère les informations de paiement pour un opérateur
     * 
     * @param string $operator Code de l'opérateur
     * @return array|null
     */
    public static function getOperatorInfo($operator)
    {
        $cameroonConfig = config('cameroon');
        
        foreach ($cameroonConfig['mobile_operators'] as $op) {
            if ($op['code'] === $operator) {
                return $op;
            }
        }
        
        return null;
    }
    
    /**
     * Valide que la devise/montant est approprié pour le Cameroun
     * 
     * @param float $amount Montant
     * @param string $currency Code devise (par défaut XAF)
     * @return bool
     */
    public static function isValidAmount($amount, $currency = 'XAF')
    {
        $cameroonConfig = config('cameroon');
        
        if ($currency !== $cameroonConfig['currency']) {
            return false;
        }
        
        // Le montant minimum est généralement 100 FCFA
        if ($amount < 100) {
            return false;
        }
        
        return true;
    }
    
    /**
     * Récupère les méthodes de paiement disponibles
     * 
     * @return array
     */
    public static function getPaymentMethods()
    {
        $cameroonConfig = config('cameroon');
        $methods = [];
        
        foreach ($cameroonConfig['payment_methods'] as $key => $method) {
            if ($method['enabled']) {
                $methods[$key] = [
                    'label' => $method['label'],
                    'code' => $method['code'],
                    'provider' => $method['provider'] ?? null,
                ];
            }
        }
        
        return $methods;
    }
    
    /**
     * Convertit les montants pour différentes devises
     * 
     * @param float $amount Montant en FCFA
     * @param string $targetCurrency Devise cible
     * @return float
     */
    public static function convertCurrency($amount, $targetCurrency = 'USD')
    {
        // Taux de change approximatif (à mettre à jour avec API en production)
        $rates = [
            'USD' => 0.0017, // 1 FCFA ≈ 0.0017 USD
            'EUR' => 0.0015, // 1 FCFA ≈ 0.0015 EUR
        ];
        
        if (!isset($rates[$targetCurrency])) {
            return $amount;
        }
        
        return round($amount * $rates[$targetCurrency], 2);
    }

    /**
     * Récupère un jeton d'accès auprès de Campay (OAuth client credentials).
     */
    protected static function getCampayToken(): ?string
    {
        $response = Http::asForm()->post(config('services.campay.base_url') . '/token/', [
            'username' => config('services.campay.app_username'),
            'password' => config('services.campay.app_password'),
        ]);

        if (!$response->successful()) {
            Log::error('Échec authentification Campay : ' . $response->body());
            return null;
        }

        return $response->json('token');
    }

    /**
     * Lance une collecte Mobile Money (MTN/Orange) via Campay.
     * En local (PAYMENT_DRIVER=log dans .env), la transaction est simulée et journalisée.
     *
     * @return array{success: bool, reference: ?string, message: ?string}
     */
    public static function collectMobileMoney(string $phone, float $amount, string $orderNumber): array
    {
        $phoneValidation = PhoneValidationService::validate($phone);
        if (!$phoneValidation['valid']) {
            return ['success' => false, 'reference' => null, 'message' => $phoneValidation['error']];
        }

        $reference = self::generateReference($orderNumber);
        $driver = config('services.campay.driver', 'log');

        if ($driver === 'log') {
            Log::info("[PAIEMENT SIMULÉ] {$reference} : " . self::formatAmount($amount) . " depuis {$phoneValidation['formatted']}");
            return ['success' => true, 'reference' => $reference, 'message' => 'Paiement simulé (mode développement).'];
        }

        try {
            $token = self::getCampayToken();
            if (!$token) {
                return ['success' => false, 'reference' => null, 'message' => 'Impossible de contacter la passerelle de paiement.'];
            }

            $response = Http::withToken($token)->post(config('services.campay.base_url') . '/collect/', [
                'amount' => (string) round($amount),
                'currency' => 'XAF',
                'from' => PhoneValidationService::cleanPhone($phone),
                'description' => "Commande {$orderNumber}",
                'external_reference' => $reference,
            ]);

            if (!$response->successful()) {
                Log::error("Échec collecte Campay pour {$orderNumber} : " . $response->body());
                return ['success' => false, 'reference' => null, 'message' => "La demande de paiement n'a pas pu être envoyée."];
            }

            return [
                'success' => true,
                'reference' => $response->json('reference') ?? $reference,
                'message' => 'Une demande de paiement a été envoyée sur votre téléphone. Validez-la pour finaliser la commande.',
            ];
        } catch (\Throwable $e) {
            Log::error("Erreur Campay pour {$orderNumber} : " . $e->getMessage());
            return ['success' => false, 'reference' => null, 'message' => 'Une erreur est survenue avec la passerelle de paiement.'];
        }
    }

    /**
     * Vérifie le statut d'une transaction Campay (utilisé par le webhook ou en polling).
     */
    public static function checkTransactionStatus(string $reference): string
    {
        if (config('services.campay.driver', 'log') === 'log') {
            return 'SUCCESSFUL';
        }

        $token = self::getCampayToken();
        if (!$token) {
            return 'PENDING';
        }

        $response = Http::withToken($token)->post(config('services.campay.base_url') . '/transaction/check/', [
            'reference' => $reference,
        ]);

        return $response->successful() ? ($response->json('status') ?? 'PENDING') : 'PENDING';
    }
}
