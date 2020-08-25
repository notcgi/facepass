<?php

namespace App\Http\Controllers;

use App\CurrencyPair;
use App\Http\Resources\CurrencyPairsCollection;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * @return CurrencyPairsCollection|bool current pairs or false if fail
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function updateCurrencyPairs()
    {
        $client = new \GuzzleHttp\Client();
        try {
            $pairs = $client->request('GET','https://api.exchangeratesapi.io/latest?base=USD');
        } catch (ClientException $e) {
            logger('exception mail: \n response: '. $e->getResponse() .
                '\n error: '.$e->getMessage());
            return false;
        }
        $pairs=\GuzzleHttp\json_decode($pairs->getBody()->getContents());
        $now=Carbon::now();
        $pairs = collect($pairs->rates)->map(function ($rate,$currency) use ($pairs, $now){
            if ($pairs->base!=$currency) return [
                'in'=>$pairs->base,
                'out'=>$currency,
                'rate'=>$rate,
                'created_at'=>$now,
                'updated_at'=>$now
            ];
        })->filter()->values();
        return CurrencyPair::insert($pairs->toArray())? new CurrencyPairsCollection (CurrencyPair::where('created_at',$now)->get()):false;
    }

    /**
     * @return CurrencyPairsCollection
     */
    public static function getHistory(){
        return new CurrencyPairsCollection(CurrencyPair::all());
    }
}
