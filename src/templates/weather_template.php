<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Previsão do Tempo</title>
    <link rel="stylesheet" href="assets/style.css">
</head>

<body>

    <div class="container">

        <?php if (!empty($error)): ?>
            <div class="error" role="alert">
                <strong>Ops!</strong> <?= $error ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($weatherData)): ?>
            <?php
            $current = $weatherData['current_weather'] ?? [];
            $daily = $weatherData['daily'] ?? [];
            ?>

            <div class="current-weather">
                <h2>Clima em: <?= $cityName ?? 'Localização atual' ?></h2>

                <div class="temp-huge">
                    <?= isset($current['temperature']) ? floatval($current['temperature']) : '--' ?>°C
                </div>

                <p class="meta">
                    Vento: <?= isset($current['windspeed']) ? floatval($current['windspeed']) : '--' ?> km/h |
                    Clima: <?= $weatherDesc ?? '--' ?>
                </p>

                <!-- Botão para buscar localização atual novamente -->
                <button id="btn-location" class="btn-location" type="button">
                    📍 Atualizar com minha localização
                </button>
            </div>

            <?php if (!empty($daily['time']) && is_array($daily['time'])): ?>
                <h3>Previsão para os próximos <?= count($daily['time']) ?> dias</h3>
                <div class="forecast-grid">
                    <?php for ($i = 0; $i < count($daily['time']); $i++): ?>
                        <?php
                        $dateStr = $daily['time'][$i] ?? null;
                        $maxTemp = $daily['temperature_2m_max'][$i] ?? null;
                        $minTemp = $daily['temperature_2m_min'][$i] ?? null;

                        if ($dateStr === null) continue;

                        $dateObj = DateTime::createFromFormat('Y-m-d', $dateStr);
                        $formattedDate = $dateObj ? $dateObj->format('d/m/Y') : $dateStr;
                        ?>
                        <div class="forecast-card">
                            <div class="forecast-date"><?= htmlspecialchars($formattedDate) ?></div>
                            <div class="forecast-temps">
                                <span class="max-temp">
                                    Máx: <?= $maxTemp !== null ? floatval($maxTemp) : '--' ?>°C
                                </span>
                                <span class="min-temp">
                                    Mín: <?= $minTemp !== null ? floatval($minTemp) : '--' ?>°C
                                </span>
                            </div>
                        </div>
                    <?php endfor; ?>
                </div>
            <?php endif; ?>

        <?php else: ?>
            <div class="welcome">
                <h2>Previsão do Tempo</h2>
                <p>Informe sua localização ou use o botão abaixo para detectar automaticamente.</p>

                <button id="btn-location" class="btn-primary" type="button">
                    📍 Usar minha localização
                </button>

                <p class="hint">
                    Ou adicione manualmente: <code>?lat=-20.90&lon=-48.47</code>
                </p>
            </div>
        <?php endif; ?>

    </div>

    <script src="assets/script.js"></script>
</body>

</html>