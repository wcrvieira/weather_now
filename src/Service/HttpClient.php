<?php

declare(strict_types=1);

namespace App\Service;

use RuntimeException;
use InvalidArgumentException;

/**
 * Cliente HTTP reutilizável usando cURL.
 */
class HttpClient
{
    /**
     * Executa uma requisição GET e retorna o array JSON decodificado.
     *
     * @throws RuntimeException
     * @throws InvalidArgumentException
     */
    public function get(string $url, int $timeout = 10): array
    {
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            throw new InvalidArgumentException("URL inválida: {$url}");
        }

        $ch = curl_init();
        if ($ch === false) {
            throw new RuntimeException('Falha ao inicializar cURL.');
        }

        try {
            curl_setopt_array($ch, [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => $timeout,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTPHEADER => ['Accept: application/json'],
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => 2,
            ]);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);

            if ($response === false || !empty($curlError)) {
                throw new RuntimeException("Erro cURL: {$curlError}");
            }

            if ($httpCode < 200 || $httpCode >= 300) {
                throw new RuntimeException("HTTP Status inesperado: {$httpCode}");
            }

            $data = json_decode($response, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new RuntimeException(
                    'Erro ao decodificar JSON: ' . json_last_error_msg()
                );
            }

            return $data;
        } finally {
            
        }
    }
}