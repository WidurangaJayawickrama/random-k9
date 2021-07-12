<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;

class ApiController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest');
    }


    public function __invoke(): JsonResponse
    {
       $result = collect();

        factEvent:{
        $response = Http::acceptJson()->get('https://dog-facts-api.herokuapp.com/api/v1/resources/dogs?number=10');
        if ($response->failed()) {
            return response()->json($response->throw(), $response->serverError());
        }

        $data = collect($response->json())->filter(function ($fact) {
            if (preg_match('~[0-9]+~', $fact['fact'])) {
                return $fact['fact'];
            }
        });

        if (count($data) > 0) {
            $result->put('fact',$data->first()['fact']);
        } else {
            goto factEvent;
        }
    }

        imageEvent:{
        $response = Http::acceptJson()->get('https://random.dog/woof.json');
        if ($response->failed()) {
            return response()->json($response->throw(), $response->serverError());
        }

        $data = $response->json();
        $kb = ($data['fileSizeBytes'] / 1000);
        if ($kb > 300) {
            goto imageEvent;
        }

        if (!preg_match('/.jpg/i', $data['url'])) {
            goto imageEvent;
        }
        $result->put('url',$data['url']);

    }
        return response()->json($result,200);
    }

}
