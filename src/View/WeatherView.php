<?php

declare(strict_types=1);

namespace App\View;

/**
 * View encapsulada que renderiza o template com dados sanitizados.
 */
class WeatherView
{
    public function __construct(
        private readonly ?array $weatherData,
        private readonly ?string $cityName,
        private readonly ?string $weatherDesc,
        private readonly ?string $error
    ) {}

    public function render(): void
    {
        // Sanitiza os dados antes de passar ao template
        $data = [
            'weatherData' => $this->weatherData,
            'cityName' => $this->cityName !== null ? htmlspecialchars($this->cityName, ENT_QUOTES, 'UTF-8') : null,
            'weatherDesc' => $this->weatherDesc !== null ? htmlspecialchars($this->weatherDesc, ENT_QUOTES, 'UTF-8') : null,
            'error' => $this->error !== null ? htmlspecialchars($this->error, ENT_QUOTES, 'UTF-8') : null,
        ];

        // Extrai as variáveis para o template (isolado)
        extract($data, EXTR_SKIP);
        require __DIR__ . '/../templates/weather_template.php';
    }
}