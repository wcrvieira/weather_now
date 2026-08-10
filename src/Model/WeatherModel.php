<?php

declare(strict_types=1);

namespace App\Model;

use App\Config\WeatherConfig;
use App\Service\HttpClient;
use InvalidArgumentException;
use RuntimeException;

/**
 * Model responsável por buscar e estruturar dados meteorológicos.
 */
class WeatherModel
{
    private readonly float $latitude;
    private readonly float $longitude;
    private readonly string $timezone;
    private readonly HttpClient $httpClient;

    private readonly array $wmoCodes;

    public function __construct(
        float $lat = WeatherConfig::DEFAULT_LAT,
        float $lon = WeatherConfig::DEFAULT_LON,
        string $tz = WeatherConfig::DEFAULT_TZ
    ) {
        if ($lat < -90.0 || $lat > 90.0) {
            throw new InvalidArgumentException("Latitude deve estar entre -90 e 90.");
        }
        if ($lon < -180.0 || $lon > 180.0) {
            throw new InvalidArgumentException("Longitude deve estar entre -180 e 180.");
        }

        $this->latitude = $lat;
        $this->longitude = $lon;
        $this->timezone = $tz;
        $this->httpClient = new HttpClient();

        // Tabela WMO completa (simplificada para os mais comuns)
        $this->wmoCodes = [
            0  => 'Céu limpo ☀️',
            1  => 'Principalmente limpo 🌤️',
            2  => 'Parcialmente nublado ⛅',
            3  => 'Nublado ☁️',
            45 => 'Nevoeiro 🌫️',
            48 => 'Nevoeiro com depósito de gelo 🌫️',
            51 => 'Garoa leve 🌧️',
            53 => 'Garoa moderada 🌧️',
            55 => 'Garoa densa 🌧️',
            56 => 'Garoa congelante leve 🌧️❄️',
            57 => 'Garoa congelante densa 🌧️❄️',
            61 => 'Chuva leve 🌧️',
            63 => 'Chuva moderada 🌧️',
            65 => 'Chuva forte 🌧️',
            66 => 'Chuva congelante leve 🌧️❄️',
            67 => 'Chuva congelante forte 🌧️❄️',
            71 => 'Neve leve ❄️',
            73 => 'Neve moderada ❄️',
            75 => 'Neve forte ❄️',
            77 => 'Grãos de neve ❄️',
            80 => 'Pancadas de chuva leves 🌦️',
            81 => 'Pancadas de chuva moderadas 🌦️',
            82 => 'Pancadas de chuva violentas 🌧️',
            85 => 'Nevoeiro de neve leve ❄️',
            86 => 'Nevoeiro de neve forte ❄️',
            95 => 'Tempestade ⛈️',
            96 => 'Tempestade com granizo leve ⛈️',
            99 => 'Tempestade com granizo forte ⛈️',
        ];
    }

    /**
     * Busca dados de previsão do tempo na Open-Meteo.
     *
     * @return array<string, mixed>
     * @throws RuntimeException
     */
    public function getWeatherData(): array
    {
        $url = sprintf(
            '%s?latitude=%s&longitude=%s&current_weather=true&daily=temperature_2m_max,temperature_2m_min&forecast_days=%d&timezone=%s',
            WeatherConfig::OPEN_METEO_URL,
            $this->latitude,
            $this->longitude,
            WeatherConfig::FORECAST_DAYS,
            urlencode($this->timezone)
        );

        $data = $this->httpClient->get($url, WeatherConfig::HTTP_TIMEOUT);

        if (!isset($data['current_weather'])) {
            throw new RuntimeException('Dados meteorológicos incompletos na resposta da API.');
        }

        return $data;
    }

    /**
     * Busca o nome da cidade via geocodificação reversa.
     *
     * @throws RuntimeException
     */
    public function getCityName(): string
    {
        $url = sprintf(
            '%s?latitude=%s&longitude=%s&localityLanguage=pt',
            WeatherConfig::GEO_URL,
            $this->latitude,
            $this->longitude
        );

        $data = $this->httpClient->get($url, WeatherConfig::GEO_TIMEOUT);

        $cidade = $data['city'] ?? $data['locality'] ?? null;
        $estado = $data['principalSubdivision'] ?? null;

        if (!empty($cidade) && !empty($estado)) {
            return "{$cidade} - {$estado}";
        }

        if (!empty($cidade)) {
            return $cidade;
        }

        return WeatherConfig::DEFAULT_CITY;
    }

    /**
     * Retorna a descrição textual para um código WMO.
     */
    public function getWeatherDescription(int $code): string
    {
        return $this->wmoCodes[$code] ?? 'Condição desconhecida';
    }
}