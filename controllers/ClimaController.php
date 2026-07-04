<?php
/**
 * =========================================
 * SIG-GCM — Sistema Integrado de Gestão
 * Guarda Civil Municipal
 * =========================================
 * SISTEMA DE TESTE — Projeto acadêmico (TG/TC)
 * Desenvolvido 100% por:
 * Autor   : Rafaela Cristina Alcântara da Silva
 * GitHub  : https://github.com/rafaela13c
 * LinkedIn: https://www.linkedin.com/in/rafaela-cas1308/
 * =========================================
 * Este sistema NÃO é oficial e NÃO será
 * utilizado pela GCM. Uso exclusivo para
 * fins de estudo acadêmico.
 * =========================================
 *//**
 * ClimaController — Módulo de Clima e Tempo
 * Cidade: Eldorado dos Ventos - SP | Lat: -23.5505 | Lon: -46.6333
 *
 * Usa Open-Meteo API (gratuita, sem chave) + Open-Meteo Air Quality
 * + Astronomy API (sunrise/sunset) direto por cálculo PHP
 */
class ClimaController extends Controller {

    private const LAT      = -23.5505;
    private const LON      = -46.6333;
    private const CIDADE   = 'Eldorado dos Ventos';
    private const ESTADO   = 'SP';
    private const TIMEZONE = 'America/Sao_Paulo';

    /**
     * Chave da API OpenWeatherMap (gratuita).
     * Cadastre em: https://openweathermap.org/api
     * Substitua 'SUA_CHAVE_OWM' pela sua chave após cadastro.
     */
    private const OWM_KEY  = 'SUA_CHAVE_OWM';

    public function index(): void {
        Auth::requireAuth();
        $this->view('clima/index', [
            'lat'    => self::LAT,
            'lon'    => self::LON,
            'cidade' => self::CIDADE,
            'estado' => self::ESTADO,
            'owmKey' => self::OWM_KEY,
        ], 'main');
    }

    /** Endpoint JSON para forecast atual + horário + diário */
    public function dados(): void {
        Auth::requireAuth();
        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-cache');

        $lat = self::LAT;
        $lon = self::LON;
        $tz  = urlencode(self::TIMEZONE);

        // Open-Meteo Current + Hourly + Daily
        $weatherUrl = "https://api.open-meteo.com/v1/forecast?"
            . "latitude=$lat&longitude=$lon"
            . "&current=temperature_2m,relative_humidity_2m,apparent_temperature,is_day,precipitation,rain,showers,snowfall,weather_code,cloud_cover,pressure_msl,surface_pressure,wind_speed_10m,wind_direction_10m,wind_gusts_10m,uv_index,visibility"
            . "&hourly=temperature_2m,precipitation_probability,weather_code,wind_speed_10m,uv_index"
            . "&daily=weather_code,temperature_2m_max,temperature_2m_min,apparent_temperature_max,apparent_temperature_min,sunrise,sunset,uv_index_max,precipitation_sum,precipitation_probability_max,wind_speed_10m_max,wind_direction_10m_dominant,shortwave_radiation_sum"
            . "&timezone=$tz"
            . "&forecast_days=7"
            . "&wind_speed_unit=kmh"
            . "&temperature_unit=celsius";

        // Open-Meteo Air Quality
        $aqUrl = "https://air-quality-api.open-meteo.com/v1/air-quality?"
            . "latitude=$lat&longitude=$lon"
            . "&current=pm10,pm2_5,carbon_monoxide,nitrogen_dioxide,ozone,european_aqi"
            . "&timezone=$tz";

        $weatherData = $this->fetchJson($weatherUrl);
        $aqData      = $this->fetchJson($aqUrl);

        if (!$weatherData) {
            http_response_code(503);
            echo json_encode(['error' => 'Não foi possível obter os dados meteorológicos.']);
            return;
        }

        echo json_encode([
            'weather'     => $weatherData,
            'air_quality' => $aqData,
            'cidade'      => self::CIDADE,
            'estado'      => self::ESTADO,
            'timestamp'   => date('Y-m-d H:i:s'),
        ]);
    }

    private function fetchJson(string $url): ?array {
        $ctx = stream_context_create(['http' => [
            'method'  => 'GET',
            'timeout' => 8,
            'header'  => "User-Agent: SIG-GCM/1.0\r\n",
        ]]);
        $raw = @file_get_contents($url, false, $ctx);
        if (!$raw) return null;
        $decoded = json_decode($raw, true);
        return is_array($decoded) ? $decoded : null;
    }
}
