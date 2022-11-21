<?php

namespace App\Http\Controllers;

use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class MediawikiController extends Controller
{
    use ApiResponser;

    private $cookie_file;
    // private $var2 = 'Private';

    /**
     * Create a new controller instance.
     *
     * @return void
     */

    public function __construct()
    {
        //
        $cookie_file = "/tmp/cookie.txt";
    }

    public function mediawiki_getinfo(request $request){
        $keyword = $request->get('keyword');
        $api_url = env('APP_URL', true);
        $api_key = env('APP_KEY', true);

        // API Token from client get request
        $client_api_key = $request->header('api-key');
        if ($api_key == $client_api_key){

            // $cors_site = explode(',', env('ALLOWED_CORS'));
            $cors_site = $request->header();

            return $this->successResponse($cors_site, Response::HTTP_OK);
        }
        else {

            // wrong api-key
            return $this->errorResponse('API-KEY Invalid', Response::HTTP_UNAUTHORIZED);
        }
    }

    public function page_listing(request $request)
    {

        $keyword = $request->get('keyword');
        $api_url = env('APP_URL', true);
        $api_key = env('APP_KEY', true);

        // API Token from client get request
        $client_api_key = $request->header('api-key');

        if ($api_key == $client_api_key){
            $curl = curl_init();
            curl_setopt_array($curl, array(
            CURLOPT_URL => $api_url.'?action=query&format=json&list=search&srsearch='.$keyword,
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
        $api_url = env('APP_URL', true);

        // API Token from client get request
        $client_api_key = $request->header('api-key');
        if ($api_key == $client_api_key){

            $curl = curl_init();

            curl_setopt_array($curl, array(
              CURLOPT_URL => $api_url.'?action=parse&format=json&pageid='.$pageid,
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
        $api_url = env('APP_URL', true);

        // API Token from client get request
        $client_api_key = $request->header('api-key');
        if ($api_key == $client_api_key){

            $curl = curl_init();

            curl_setopt_array($curl, array(
              CURLOPT_URL => $api_url.'?action=parse&format=json&page='.$page,
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

    public function create_new_page(request $request){
        // sample bot user
        $api_key = env('APP_KEY', true);
        $api_url = env('APP_URL', true);

        // API Token from client get request
        $client_api_key = $request->header('api-key');

        // mediawiki bot user id
        $bot_user =  $request->input('bot-user');

        // mediawiki bot password
        $bot_pass = $request->input('bot-password');

        // text
        $page_text = $request->input('text');

        // title
        $page_title = $request->input('title');

        // creates cookie file
        $this->cookie_file = "/tmp/".$this->random_filename(32,"/tmp","txt");

        // start performing create page sequence
        $login_Token = $this->getLoginToken();                                           // Step 1
        $this->loginRequest( $login_Token, $bot_user, $bot_pass );                       // Step 2
        $csrf_Token = $this->getCSRFToken();                                             // Step 3
        $response = $this->addRequest($csrf_Token, $page_title, $page_text);             // Step 4, create the page

        // remove cookie file
        unlink($this->cookie_file);
        
        $outResponse = json_decode($response);
        return $this->successResponse($outResponse, Response::HTTP_OK);
    }

    public function create_new_product(request $request){
        // sample bot user
        $api_key = env('APP_KEY', true);
        $api_url = env('APP_URL', true);

        // API Token from client get request
        $client_api_key = $request->header('api-key');

        // mediawiki bot user id
        $bot_user =  $request->input('bot-user');

        // mediawiki bot password
        $bot_pass = $request->input('bot-password');

        // prepare text from template
        $s_page_text = '{{LKPP/Product';
        $s_page_text = $s_page_text.'|product-name='.$request->input('product-name');
        $s_page_text = $s_page_text.'|product-stock-keep-unit='.$request->input('product-stock-keep-unit');
        $s_page_text = $s_page_text.'|unit-price='.$request->input('unit-price');
        $s_page_text = $s_page_text.'|stock-on-hand='.$request->input('stock-on-hand');
        $s_page_text = $s_page_text.'|picture-name='.$request->input('picture-name');
        $s_page_text = $s_page_text.'|product-picture-caption='.$request->input('product-picture-caption');
        $s_page_text = $s_page_text.'|end-of-product-date='.$request->input('end-of-product-date');
        $s_page_text = $s_page_text.'|brand='.$request->input('brand');
        $s_page_text = $s_page_text.'|product-vendor-code='.$request->input('product-vendor-code');
        $s_page_text = $s_page_text.'|uom='.$request->input('uom');
        $s_page_text = $s_page_text.'|kind-of-product='.$request->input('kind-of-product');
        $s_page_text = $s_page_text.'|kbki-code='.$request->input('kbki-code');
        $s_page_text = $s_page_text.'|tkdn-value='.$request->input('tkdn-value');
        $s_page_text = $s_page_text.'|bmp-value='.$request->input('bmp-value');
        $s_page_text = $s_page_text.'|sum-of-tkdn-bmp='.$request->input('sum-of-tkdn-bmp');
        $s_page_text = $s_page_text.'|certificate-owner-identifier='.$request->input('certificate-owner-identifier');
        $s_page_text = $s_page_text.'|kind-of-tkdn-product='.$request->input('kind-of-tkdn-product');
        $s_page_text = $s_page_text.'|product-specific-info='.$request->input('product-specific-info');        
        $s_page_text = $s_page_text.'|}}';

        // title
        $page_title = 'LKPP:Product:'.$request->input('title');

        // creates cookie file
        $this->cookie_file = "/tmp/".$this->random_filename(32,"/tmp","txt");

        // start performing create page sequence
        $login_Token = $this->getLoginToken();                                           // Step 1
        $this->loginRequest( $login_Token, $bot_user, $bot_pass );                       // Step 2
        $csrf_Token = $this->getCSRFToken();                                             // Step 3
        $response = $this->addRequest($csrf_Token, $page_title, $s_page_text);             // Step 4, create the page

        // remove cookie file
        unlink($this->cookie_file);
        
        $outResponse = json_decode($response);
        return $this->successResponse($outResponse, Response::HTTP_OK);        
    }

    public function replicate_page(request $request){

        $source_url;
        $source_page_title;
        // https://pkc-lkpp.dev/api.php?action=parse&format=json&page=ECatalogue Smart Contract Logic Model
        $url = $source_url.'?action=parse&format=json&page='.$source_page_title;

        $ch = curl_init($url);
        curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
        $c = curl_exec($ch);
        echo $c->parse->text;

    }

    /*
        ================================================================================
        Private functions

        ================================================================================
    */

    function random_filename($length, $directory = '', $extension = '')
    {
        // default to this files directory if empty...
        $dir = !empty($directory) && is_dir($directory) ? $directory : dirname(__FILE__);

        do {
            $key = '';
            $keys = array_merge(range(0, 9), range('a', 'z'));

            for ($i = 0; $i < $length; $i++) {
                $key .= $keys[array_rand($keys)];
            }
        } while (file_exists($dir . '/' . $key . (!empty($extension) ? '.' . $extension : '')));

        return $key . (!empty($extension) ? '.' . $extension : '');
    }    

    // Step 1: GET request to fetch login token
    function getLoginToken() {

        // var_dump($this->cookie_file); die(); 

        $endPoint = env('APP_URL', true);

        $params1 = [
            "action" => "query",
            "meta" => "tokens",
            "type" => "login",
            "format" => "json"
        ];

        $url = $endPoint . "?" . http_build_query( $params1 );

        $ch = curl_init( $url );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $ch, CURLOPT_COOKIEJAR, $this->cookie_file );
        curl_setopt( $ch, CURLOPT_COOKIEFILE, $this->cookie_file );

        $output = curl_exec( $ch );
        curl_close( $ch );

        $result = json_decode( $output, true );
        return $result["query"]["tokens"]["logintoken"];
    }

    function loginRequest( $logintoken, $lgname, $lgpass ) {
     // $this->loginRequest( $login_Token, $bot_user, $bot_pass );  

        $endPoint = env('APP_URL', true);;

        $params2 = [
            "action" => "login",
            "lgname" => $lgname,
            "lgpassword" => $lgpass,
            "lgtoken" => $logintoken,
            "format" => "json"
        ];

        $ch = curl_init();

        curl_setopt( $ch, CURLOPT_URL, $endPoint );
        curl_setopt( $ch, CURLOPT_POST, true );
        curl_setopt( $ch, CURLOPT_POSTFIELDS, http_build_query( $params2 ) );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $ch, CURLOPT_COOKIEJAR,  $this->cookie_file );
        curl_setopt( $ch, CURLOPT_COOKIEFILE,  $this->cookie_file );

        $output = curl_exec( $ch );
        curl_close( $ch );

        // echo( $output );
    }

    // Step 3: Get CSRF Token
    function getCSRFToken() {
        $endPoint = env('APP_URL', true);
    
        $params3 = [
            "action" => "query",
            "meta" => "tokens",
            "format" => "json"
        ];
    
        $url = $endPoint . "?" . http_build_query( $params3 );
    
        $ch = curl_init( $url );
    
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $ch, CURLOPT_COOKIEJAR, $this->cookie_file);
        curl_setopt( $ch, CURLOPT_COOKIEFILE, $this->cookie_file );
    
        $output = curl_exec( $ch );
        curl_close( $ch );
    
        $result = json_decode( $output, true );

        return $result["query"]["tokens"]["csrftoken"];
    }

    // Step 4: POST request to edit a page
    function editRequest( $csrftoken ) {
        $endPoint = env('APP_URL', true);;

        $params4 = [
            "action" => "edit",
            "title" => "Test-api-page-new",
            "appendtext" => "<br> Hello from API",
            "token" => $csrftoken,
            "format" => "json"
        ];

        $ch = curl_init();

        curl_setopt( $ch, CURLOPT_URL, $endPoint );
        curl_setopt( $ch, CURLOPT_POST, true );
        curl_setopt( $ch, CURLOPT_POSTFIELDS, http_build_query( $params4 ) );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $ch, CURLOPT_COOKIEJAR, $this->cookie_file );
        curl_setopt( $ch, CURLOPT_COOKIEFILE, $this->cookie_file );

        $output = curl_exec( $ch );
        curl_close( $ch );

        echo ( $output );
    }

    // Step 4: POST request to add a page
    function addRequest( $csrftoken, $page_title, $page_text ) {
        $endPoint = env('APP_URL', true);;

        $params4 = [
            "action" => "edit",
            "title" => $page_title,
            "appendtext" => $page_text,
            "token" => $csrftoken,
            "format" => "json"
        ];

        $ch = curl_init();

        curl_setopt( $ch, CURLOPT_URL, $endPoint );
        curl_setopt( $ch, CURLOPT_POST, true );
        curl_setopt( $ch, CURLOPT_POSTFIELDS, http_build_query( $params4 ) );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $ch, CURLOPT_COOKIEJAR, $this->cookie_file );
        curl_setopt( $ch, CURLOPT_COOKIEFILE, $this->cookie_file );

        $output = curl_exec( $ch );
        curl_close( $ch );

        // echo ( $output );
        return $output;
    }


}
