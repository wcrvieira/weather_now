<?php

declare(strict_types=1);

namespace App\Controller;

use App\Model\WeatherModel;
use App\View\WeatherView;
use InvalidArgumentException;
use RuntimeException;
use Throwable;

/**
 * Controller responsável por orquestrar Model e View.
 */
class WeatherController
{
    public function render(): void
    {
        $weatherData = null;
        $cityName = null;
        $weatherDesc = null;
        $error = null;

        try {
            $lat = $this->getFloatParam('lat');
            $lon = $this->getFloatParam('lon');

            $model = new WeatherModel(lat: $lat ?? -20.9057, lon: $lon ?? -48.4795);

            $weatherData = $model->getWeatherData();
            $cityName = $model->getCityName();

            $code = (int) ($weatherData['current_weather']['weathercode'] ?? -1);
            $weatherDesc = $model->getWeatherDescription($code);

        } catch (InvalidArgumentException $e) {
            $error = "Dados inválidos: " . $e->getMessage();
        } catch (RuntimeException $e) {
            $error = "Erro na comunicação: " . $e->getMessage();
        } catch (Throwable $e) {
            $error = "Erro inesperado: " . $e->getMessage();
            // Em produção, logar o erro ao invés de exibir
        }

        $view = new WeatherView(
            weatherData: $weatherData,
            cityName: $cityName,
            weatherDesc: $weatherDesc,
            error: $error
        );

        $view->render();
    }

    /**
     * Obtém e valida um parâmetro float do GET.
     */
    private function getFloatParam(string $key): ?float
    {
        if (!isset($_GET[$key])) {
            return null;
        }

        $value = filter_input(INPUT_GET, $key, FILTER_VALIDATE_FLOAT);
        if ($value === false) {
            throw new InvalidArgumentException("O parâmetro '{$key}' deve ser um número válido.");
        }

        return $value;
    }
}