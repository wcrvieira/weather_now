<?php

declare(strict_types=1);

namespace App\Config;

/**
 * Configurações centralizadas da aplicação.
 */
class WeatherConfig
{
    public const DEFAULT_LAT = -20.9057;
    public const DEFAULT_LON = -48.4795;
    public const DEFAULT_TZ = 'America/Sao_Paulo';
    public const DEFAULT_CITY = 'Bebedouro/SP';

    public const OPEN_METEO_URL = 'https://api.open-meteo.com/v1/forecast';
    public const GEO_URL = 'https://api.bigdatacloud.net/data/reverse-geocode-client';
    
    public const HTTP_TIMEOUT = 10;
    public const GEO_TIMEOUT = 5;
    public const FORECAST_DAYS = 3;
}