<?php

namespace App\Services;

class PhoneValidationService
{
    /**
     * Valide un numéro de téléphone camerounais
     * 
     * @param string $phone Numéro de téléphone à valider
     * @return array ['valid' => bool, 'formatted' => string, 'operator' => string, 'error' => string|null]
     */
    public static function validate($phone)
    {
        $cameroonConfig = config('cameroon');
        
        // Nettoyer le numéro
        $cleaned = self::cleanPhone($phone);
        
        // Vérifier la longueur
        if (strlen($cleaned) !== $cameroonConfig['phone_length']) {
            return [
                'valid' => false,
                'formatted' => null,
                'operator' => null,
                'error' => __('validation.invalid_phone'),
            ];
        }
        
        // Vérifier le préfixe et identifier l'opérateur
        $operator = self::detectOperator($cleaned);
        
        if (!$operator) {
            return [
                'valid' => false,
                'formatted' => null,
                'operator' => null,
                'error' => __('validation.invalid_phone'),
            ];
        }
        
        // Formater le numéro
        $formatted = self::format($cleaned);
        
        return [
            'valid' => true,
            'formatted' => $formatted,
            'operator' => $operator,
            'error' => null,
        ];
    }
    
    /**
     * Formate un numéro de téléphone camerounais
     * 
     * @param string $phone Numéro à formater
     * @return string Numéro formaté
     */
    public static function format($phone)
    {
        $cleaned = self::cleanPhone($phone);
        
        if (strlen($cleaned) !== 9) {
            return $phone;
        }
        
        // Format: +237 XXX XXX XXX
        return sprintf(
            '+237 %s %s %s',
            substr($cleaned, 0, 3),
            substr($cleaned, 3, 3),
            substr($cleaned, 6, 3)
        );
    }
    
    /**
     * Nettoie un numéro de téléphone
     * 
     * @param string $phone Numéro à nettoyer
     * @return string Numéro nettoyé (9 chiffres)
     */
    public static function cleanPhone($phone)
    {
        // Supprimer les espaces, tirets, parenthèses
        $cleaned = preg_replace('/[^\d+]/', '', $phone);
        
        // Supprimer le +237 ou 00237 au début
        if (strpos($cleaned, '+237') === 0) {
            $cleaned = substr($cleaned, 4);
        } elseif (strpos($cleaned, '00237') === 0) {
            $cleaned = substr($cleaned, 5);
        } elseif (strpos($cleaned, '237') === 0) {
            $cleaned = substr($cleaned, 3);
        }
        
        // Retirer les zéros au début si présents
        $cleaned = ltrim($cleaned, '0');
        
        return $cleaned;
    }
    
    /**
     * Détecte l'opérateur à partir du préfixe
     * 
     * @param string $phone Numéro de téléphone (9 chiffres)
     * @return string|null Code de l'opérateur ou null
     */
    public static function detectOperator($phone)
    {
        $cameroonConfig = config('cameroon');
        $prefix = substr($phone, 0, 3);
        
        foreach ($cameroonConfig['mobile_operators'] as $operator) {
            if (in_array($prefix, $operator['prefixes'])) {
                return $operator['code'];
            }
        }
        
        return null;
    }
    
    /**
     * Formate un numéro interne à +237 XXXXXXXXX
     * 
     * @param string $phone Numéro à convertir
     * @return string Numéro formaté
     */
    public static function toInternational($phone)
    {
        $cleaned = self::cleanPhone($phone);
        
        if (strlen($cleaned) === 9) {
            return '+237' . $cleaned;
        }
        
        return $phone;
    }
    
    /**
     * Vérifie si un numéro est valide pour un opérateur spécifique
     * 
     * @param string $phone Numéro à vérifier
     * @param string $operatorCode Code de l'opérateur
     * @return bool
     */
    public static function isForOperator($phone, $operatorCode)
    {
        $operator = self::detectOperator(self::cleanPhone($phone));
        return $operator === $operatorCode;
    }
    
    /**
     * Obtient tous les opérateurs disponibles
     * 
     * @return array
     */
    public static function getOperators()
    {
        $cameroonConfig = config('cameroon');
        $operators = [];
        
        foreach ($cameroonConfig['mobile_operators'] as $key => $operator) {
            $operators[$operator['code']] = $operator['name'];
        }
        
        return $operators;
    }
}
