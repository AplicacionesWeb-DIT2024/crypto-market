<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Criptomoneda;
use Illuminate\Support\Facades\Http;

class criptomonedaController extends Controller
{

    private function obtenerDatosDeApi($apiKey)
    {
        $baseUrl = 'https://api.coingecko.com/api/v3/coins/markets?vs_currency=usd&ids=';
        $simbolos = Criptomoneda::all()->pluck('simbolo')->implode(',');

        $response = Http::withHeaders([
            'x_cg_demo_api_key' => $apiKey,
        ])->withOptions([
            'verify' => false,
        ])->get($baseUrl . $simbolos);

        if ($response->failed()) {
            return null;
        }

        $datos = $response->json();

        $datosFiltrados = collect($datos)->map(function ($item) {
            return [
                'id_api' => $item['id'],
                'simbolo' => $item['symbol'],
                'nombre' => $item['name'],
                'imagen' => $item['image'],
                'precio' => $item['current_price'],
                'ranking' => $item['market_cap_rank'],
                'variacion' => $item['price_change_percentage_24h'],
            ];
        });

        return $datosFiltrados;
    }

    private function obtenerGraficoDeApi($apiKey, $dias)
    {
        $simbolos = Criptomoneda::all()->pluck('simbolo');

        $datosGraficos = [];
        foreach ($simbolos as $simbolo) {
            $graficoCriptomoneda = $this->obtenerGrafico($apiKey, $dias, $simbolo);
            $datosGraficos[$simbolo] = $graficoCriptomoneda;
        }

        return $datosGraficos;
    }

    private function obtenerGrafico($apiKey, $dias, $simbolo)
    {
        $baseUrl = 'https://api.coingecko.com/api/v3/coins/';
        $url = $baseUrl . $simbolo . '/market_chart?vs_currency=usd&days=' . $dias;

        $response = Http::withHeaders([
            'x_cg_demo_api_key' => $apiKey,
        ])->withOptions([
            'verify' => false,
        ])->get($url);

        if ($response->successful()) {
            $precios = $response->json()['prices'];

            $preciosFormateados = array_map(function ($precio) {
                $fecha = date('d/m/Y', $precio[0] / 1000);
                $hora = date('H:i:s', $precio[0] / 1000);

                return [
                    'fecha' => $fecha,
                    'hora' => $hora,
                    'precio' => round($precio[1], 2),
                ];
            }, $precios);

            return $preciosFormateados;
        } else {
            return null;
        }
    }


    public function index()
    {
        $apiKey = env('API_KEY');
        $response = $this->obtenerDatosDeApi($apiKey);
        $graficos = $this->obtenerGraficoDeApi($apiKey, 1);

        if ($response === null) {
            return response()->json(['message' => 'Error al obtener los datos de la API o los gráficos'], 500);
        }

        $datosCombinados = [
            'datos' => $response,
            'graficos' => $graficos,
        ];

        return response()->json($datosCombinados, 200);
    }

    private function obtenerInfoDeApi($apiKey, $simbolo)
    {
        $baseUrl = 'https://api.coingecko.com/api/v3/coins/';
        $url = $baseUrl . $simbolo;

        $response = Http::withHeaders([
            'x_cg_demo_api_key' => $apiKey,
        ])->withOptions([
            'verify' => false,
        ])->get($url);

        if ($response->failed()) {
            return null;
        }

        $datos = $response->json();

        $datosFormateados = [
            'id_api' => $datos['id'],
            'simbolo' => $datos['symbol'],
            'nombre' => $datos['name'],
            'imagen' => $datos['image']['large'],
            'descripcion' => !empty($datos['description']['es']) ? $datos['description']['es'] : $datos['description']['en'],
            'web' => $datos['links']['homepage'][0],
            'genesis' => $datos['genesis_date'],
            'sentiment_up' => $datos['sentiment_votes_up_percentage'],
            'sentiment_down' => $datos['sentiment_votes_down_percentage'],
            'ranking' => $datos['market_cap_rank'],
            'precio' => $datos['market_data']['current_price']['usd'],
            'variacion' => $datos['market_data']['price_change_percentage_24h'],
            'maximo_historico' => $datos['market_data']['ath']['usd'],
            'maximo_historico_fecha' => !empty($datos['market_data']['ath_date']['usd']) ?
                date('d/m/Y', strtotime($datos['market_data']['ath_date']['usd'])) : null,
            'maximo_historico_cambio' => $datos['market_data']['ath_change_percentage']['usd'],
            'capitalizacion' => $datos['market_data']['market_cap']['usd'],
            'cambio_24h' => $datos['market_data']['price_change_24h_in_currency']['usd'],
            'cambio_7d' => $datos['market_data']['price_change_percentage_7d_in_currency']['usd'],
            'cambio_30d' => $datos['market_data']['price_change_percentage_30d_in_currency']['usd'],
            'cambio_1y' => $datos['market_data']['price_change_percentage_1y_in_currency']['usd'],
            'circulacion' => $datos['market_data']['circulating_supply'],
            'total' => $datos['market_data']['total_supply'],
        ];


        return $datosFormateados;
    }


    public function show($id)
    {
        $criptomoneda = Criptomoneda::where('simbolo', $id)->first();

        if (!$criptomoneda) {
            return response()->json(['message' => 'Criptomoneda no encontrada'], 404);
        }

        $apiKey = env('API_KEY');
        $response = $this->obtenerInfoDeApi($apiKey, $criptomoneda->simbolo);

        if ($response === null) {
            return response()->json(['message' => 'Error al obtener los datos de la API'], 500);
        }

        return response()->json($response, 200);
    }
}
