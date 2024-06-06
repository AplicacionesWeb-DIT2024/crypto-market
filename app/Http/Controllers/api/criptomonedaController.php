<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Criptomoneda;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class criptomonedaController extends Controller
{

    private function obtenerDatosDeApi($apiKey)
    {
        $baseUrl = 'https://pro-api.coinmarketcap.com/v2/cryptocurrency/quotes/latest?skip_invalid=true&id=';
        $simbolos = Criptomoneda::all()->pluck('simbolo')->implode(',');

        $response = Http::withHeaders([
            'X-CMC_PRO_API_KEY' => $apiKey,
        ])->withOptions([
            'verify' => false,
        ])->get($baseUrl . $simbolos);

        if ($response->failed()) {
            return null;
        }

        $datos = $response->json();

        return $datos;
    }

    public function index()
    {
        $apiKey = env('API_KEY');
        $response = $this->obtenerDatosDeApi($apiKey);

        if ($response === null) {
            return response()->json(['message' => 'Error al obtener los datos de la API'], 500);
        }

        return response()->json($response, 200);
    }

    private function obtenerInfoDeApi($apiKey, $simbolo)
    {
        $baseUrl = 'https://pro-api.coinmarketcap.com/v2/cryptocurrency/info?id=';
        $url = $baseUrl . $simbolo;

        $response = Http::withHeaders([
            'X-CMC_PRO_API_KEY' => $apiKey,
        ])->withOptions([
            'verify' => false,
        ])->get($url);

        if ($response->failed()) {
            return null;
        }

        $datos = $response->json();

        return $datos;
    }

    private function getPrecio($apiKey, $simbolo)
    {
        $url = 'https://pro-api.coinmarketcap.com/v2/cryptocurrency/quotes/latest?skip_invalid=true&id=' . $simbolo;

        $response = Http::withHeaders([
            'X-CMC_PRO_API_KEY' => $apiKey,
        ])->withOptions([
            'verify' => false,
        ])->get($url);

        if ($response->failed()) {
            return null;
        }

        $datos = $response->json();

        return $datos['data'][$simbolo]['quote']['USD']['price'];
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

    public function precio($id)
    {
        $criptomoneda = Criptomoneda::where('simbolo', $id)->first();

        if (!$criptomoneda) {
            return response()->json(['message' => 'Criptomoneda no encontrada'], 404);
        }

        $apiKey = env('API_KEY');
        $response = $this->getPrecio($apiKey, $criptomoneda->simbolo);

        if ($response === null) {
            return response()->json(['message' => 'Error al obtener los datos de la API'], 500);
        }

        $precio = $response;
        $comision = $criptomoneda->comision;
        $precio_final = $precio + ($precio * ($comision / 100));

        return response()->json(['precio' => $precio_final], 200);
    }

    public function admin_index() {
        $criptomonedas = Criptomoneda::all();

        return response()->json($criptomonedas, 200);
    }

    public function admin_store() {
        $validator = Validator::make(request()->all(), [
            'simbolo' => 'required',
            'comision' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $criptomoneda = new Criptomoneda();
        $criptomoneda->simbolo = request('simbolo');
        $criptomoneda->comision = request('comision');

        $criptomoneda->save();

        return response()->json($criptomoneda, 201);
    }

    public function admin_update($id) {
        $criptomoneda = Criptomoneda::find($id);
    
        if (!$criptomoneda) {
            return response()->json(['message' => 'Criptomoneda no encontrada'], 404);
        }
    
        $validator = Validator::make(request()->all(), [
            'simbolo' => 'required',
            'comision' => 'required',
        ]);
    
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
    
        $criptomoneda->simbolo = request('simbolo');
        $criptomoneda->comision = request('comision');
    
        $criptomoneda->save();
    
        return response()->json($criptomoneda, 200);
    }

    public function admin_destroy($id) {
        $criptomoneda = Criptomoneda::find($id);
    
        if (!$criptomoneda) {
            return response()->json(['message' => 'Criptomoneda no encontrada'], 404);
        }
    
        $criptomoneda->delete();
    
        return response()->json(['message' => 'Criptomoneda eliminada'], 200);
    }

    public function admin_show($id) {
        $criptomoneda = Criptomoneda::find($id);
    
        if (!$criptomoneda) {
            return response()->json(['message' => 'Criptomoneda no encontrada'], 404);
        }
    
        return response()->json($criptomoneda, 200);
    }
}