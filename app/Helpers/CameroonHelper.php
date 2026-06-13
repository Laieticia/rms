<?php

namespace App\Helpers;

use App\Services\PhoneValidationService;
use App\Services\CameroonianPaymentService;

class CameroonHelper
{
    /**
     * Formate un montant en devise camerounaise (FCFA)
     * 
     * @param float $amount Montant en FCFA
     * @param bool $showCurrency Afficher le symbole de devise
     * @return string
     */
    public static function formatCurrency($amount, $showCurrency = true)
    {
        $formatted = number_format($amount, 0, ',', ' ');
        
        return $showCurrency ? $formatted . ' FCFA' : $formatted;
    }
    
    /**
     * Formate un numéro de téléphone camerounais
     * 
     * @param string $phone Numéro à formater
     * @return string
     */
    public static function formatPhone($phone)
    {
        return PhoneValidationService::format($phone);
    }
    
    /**
     * Valide un numéro de téléphone camerounais
     * 
     * @param string $phone Numéro à valider
     * @return bool
     */
    public static function isValidPhone($phone)
    {
        $result = PhoneValidationService::validate($phone);
        return $result['valid'];
    }
    
    /**
     * Récupère le nom de la région à partir du code
     * 
     * @param string $code Code de la région
     * @return string
     */
    public static function getRegionName($code)
    {
        $regions = config('cameroon.regions');
        return $regions[$code] ?? $code;
    }
    
    /**
     * Récupère toutes les régions
     * 
     * @return array
     */
    public static function getRegions()
    {
        return config('cameroon.regions');
    }
    
    /**
     * Récupère les villes pour une région
     * 
     * @param string $regionCode Code de la région
     * @return array
     */
    public static function getCitiesByRegion($regionCode)
    {
        $cities = config('cameroon.cities');
        return $cities[$regionCode] ?? [];
    }
    
    /**
     * Formate une date à la manière camerounaise
     * 
     * @param \DateTime|string $date Date à formater
     * @param string $format Format de date
     * @return string
     */
    public static function formatDate($date, $format = 'd/m/Y')
    {
        if (is_string($date)) {
            $date = \Carbon\Carbon::parse($date);
        }
        
        return $date->format($format);
    }
    
    /**
     * Formate une date et heure à la manière camerounaise
     * 
     * @param \DateTime|string $date Date à formater
     * @return string
     */
    public static function formatDateTime($date)
    {
        return self::formatDate($date, 'd/m/Y H:i');
    }
    
    /**
     * Formate un temps
     * 
     * @param \DateTime|string $time Heure à formater
     * @return string
     */
    public static function formatTime($time)
    {
        return self::formatDate($time, 'H:i');
    }
    
    /**
     * Récupère le label du mode de livraison
     * 
     * @param string $type Type de livraison
     * @return string
     */
    public static function getDeliveryTypeLabel($type)
    {
        $labels = [
            'dine_in' => __('app.dine_in'),
            'takeaway' => __('app.takeaway'),
            'delivery' => __('app.delivery'),
        ];
        
        return $labels[$type] ?? $type;
    }
    
    /**
     * Récupère les zones de livraison
     * 
     * @return array
     */
    public static function getDeliveryZones()
    {
        return config('cameroon.delivery_zones');
    }
    
    /**
     * Calcule les frais de livraison basés sur la zone
     * 
     * @param string $zone Code de la zone
     * @return float|null
     */
    public static function getDeliveryFee($zone)
    {
        $zones = self::getDeliveryZones();
        return $zones[$zone]['fee'] ?? null;
    }
    
    /**
     * Récupère le temps de livraison estimé pour une zone
     * 
     * @param string $zone Code de la zone
     * @return array ['min' => int, 'max' => int] en minutes
     */
    public static function getDeliveryTime($zone)
    {
        $zones = self::getDeliveryZones();
        $zoneData = $zones[$zone] ?? null;
        
        if (!$zoneData) {
            return null;
        }
        
        return [
            'min' => $zoneData['min_time'],
            'max' => $zoneData['max_time'],
        ];
    }
    
    /**
     * Récupère les opérateurs de téléphonie mobiles
     * 
     * @return array
     */
    public static function getMobileOperators()
    {
        return config('cameroon.mobile_operators');
    }
    
    /**
     * Détecte l'opérateur à partir d'un numéro de téléphone
     * 
     * @param string $phone Numéro de téléphone
     * @return string|null
     */
    public static function detectOperator($phone)
    {
        return PhoneValidationService::detectOperator(
            PhoneValidationService::cleanPhone($phone)
        );
    }
    
    /**
     * Récupère les méthodes de paiement disponibles
     * 
     * @return array
     */
    public static function getPaymentMethods()
    {
        return CameroonianPaymentService::getPaymentMethods();
    }
    
    /**
     * Vérifie si une méthode de paiement est disponible
     * 
     * @param string $method Code de la méthode
     * @return bool
     */
    public static function isPaymentMethodAvailable($method)
    {
        $methods = config('cameroon.payment_methods');
        return isset($methods[$method]) && $methods[$method]['enabled'];
    }
    
    /**
     * Récupère les types de cuisine populaires
     * 
     * @return array
     */
    public static function getCuisineTypes()
    {
        return config('cameroon.cuisine_types');
    }
    
    /**
     * Récupère le code pays
     * 
     * @return string
     */
    public static function getCountryCode()
    {
        return config('cameroon.country_code');
    }
    
    /**
     * Récupère l'indicatif téléphonique
     * 
     * @return string
     */
    public static function getPhoneCode()
    {
        return config('cameroon.phone_code');
    }
    
    /**
     * Récupère le code devise
     * 
     * @return string
     */
    public static function getCurrencyCode()
    {
        return config('cameroon.currency');
    }
    
    /**
     * Vérifie si une date est un jour férié camerounais
     * 
     * @param \DateTime|string $date Date à vérifier
     * @return bool
     */
    public static function isPublicHoliday($date)
    {
        if (is_string($date)) {
            $date = \Carbon\Carbon::parse($date)->format('Y-m-d');
        } else {
            $date = $date->format('Y-m-d');
        }
        
        $holidays = config('cameroon.public_holidays');
        return isset($holidays[$date]);
    }
    
    /**
     * Récupère le nom du jour férié
     * 
     * @param \DateTime|string $date Date du jour férié
     * @return string|null
     */
    public static function getPublicHolidayName($date)
    {
        if (is_string($date)) {
            $date = \Carbon\Carbon::parse($date)->format('Y-m-d');
        } else {
            $date = $date->format('Y-m-d');
        }
        
        $holidays = config('cameroon.public_holidays');
        return $holidays[$date] ?? null;
    }
}
