<?php

namespace App\Http\Controllers;

use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class MediawikiController extends Controller
{
    use ApiResponser;

    /**
     * Create a new controller instance.
     *
     * @return void
     */

    public function __construct()
    {
        //
    }

    public function page_listing(request $request)
    {

        $keyword = $request->get('keyword');
        $api_key = env('APP_KEY', true);

        // API Token from client get request
        $client_api_key = $request->header('api-key');

        if ($api_key == $client_api_key){
            $curl = curl_init();
            curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://pkc-lkpp.dev/api.php?action=query&format=json&list=search&srsearch='.$keyword,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            ));
    
            $response = curl_exec($curl);
            curl_close($curl);
            $outResponse = json_decode($response);
            
            if( is_null($outResponse->query)){
                return $this->successResponse("Data not found", Response::HTTP_OK);
            }
            else{
                return $this->successResponse($outResponse->query, Response::HTTP_OK);
            }
        }

        else {
            // wrong api-key
            return $this->errorResponse('API-KEY Invalid', Response::HTTP_UNAUTHORIZED);
        }

    }

    public function page_by_id(request $request)
    {
        $pageid = $request->get('pageid');
        $api_key = env('APP_KEY', true);

        // API Token from client get request
        $client_api_key = $request->header('api-key');
        if ($api_key == $client_api_key){

            $curl = curl_init();

            curl_setopt_array($curl, array(
              CURLOPT_URL => 'https://pkc-lkpp.dev/api.php?action=parse&format=json&pageid='.$pageid,
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_ENCODING => '',
              CURLOPT_MAXREDIRS => 10,
              CURLOPT_TIMEOUT => 0,
              CURLOPT_FOLLOWLOCATION => true,
              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
              CURLOPT_CUSTOMREQUEST => 'GET',
              CURLOPT_POSTFIELDS => array('page' => 'Test Smart Contract'),
              CURLOPT_HTTPHEADER => array(
                'Origin: https://qtux.pkc-dev.org'
              ),
            ));
            
            $response = curl_exec($curl);
            $outResponse = json_decode($response);
            curl_close($curl);

            // echo $response;

            if( is_null($outResponse->parse)){
                return $this->successResponse("Data not found", Response::HTTP_OK);
            }
            else{
                return $this->successResponse($outResponse->parse, Response::HTTP_OK);
            }            
            
        }
        else {
            // wrong api-key
            return $this->errorResponse('API-KEY Invalid', Response::HTTP_UNAUTHORIZED);
        }

    }

    public function page_by_title(request $request){
        $page = $request->get('page');
        $page = rawurlencode($page);
        $api_key = env('APP_KEY', true);

        // API Token from client get request
        $client_api_key = $request->header('api-key');
        if ($api_key == $client_api_key){

            $curl = curl_init();

            curl_setopt_array($curl, array(
              CURLOPT_URL => 'https://pkc-lkpp.dev/api.php?action=parse&format=json&page='.$page,
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_ENCODING => '',
              CURLOPT_MAXREDIRS => 10,
              CURLOPT_TIMEOUT => 0,
              CURLOPT_FOLLOWLOCATION => true,
              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
              CURLOPT_CUSTOMREQUEST => 'GET',
              CURLOPT_HTTPHEADER => array(
                'Origin: https://qtux.pkc-dev.org'
              ),
            ));
            
            $response = curl_exec($curl);
            curl_close($curl);
            $outResponse = json_decode($response);
            curl_close($curl);

            // echo $response;
            // return $this->successResponse($response, Response::HTTP_OK);

            if( is_null($outResponse->parse)){
                return $this->successResponse("Data not found", Response::HTTP_OK);
            }
            else{
                return $this->successResponse($outResponse->parse->text, Response::HTTP_OK);
            }            
            
        }
        else {
            // wrong api-key
            return $this->errorResponse('API-KEY Invalid', Response::HTTP_UNAUTHORIZED);
        }        
    }


}
