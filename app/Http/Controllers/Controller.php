<?php

namespace App\Http\Controllers;

use App\CurrencyPair;
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
     * @return Collection|bool current pairs or false if fail
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
        $pairs = collect($pairs->rates)->map(function ($rate,$currency) use ($pairs){
            if ($pairs->base!=$currency) return [
                'in'=>$pairs->base,
                'out'=>$currency,
                'rate'=>$rate,
                'created_at'=>Carbon::now(),
                'updated_at'=>Carbon::now()
            ];
        })->filter()->values();
        return CurrencyPair::insert($pairs->toArray())?$pairs:false;
    }

    public static function getHistory(){
        return CurrencyPair::all()
            ->groupBy('created_at')
            ->makeHidden(['id','created_at','updated_at']);
    }
}
