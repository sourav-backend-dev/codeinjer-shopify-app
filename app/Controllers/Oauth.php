<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Oauth extends CI_Controller {

    private $shopify_api_key = '0d5956f22de6e2cb4c296df486864dcb';
    private $shopify_api_secret = 'f67e6b86d973696b47a42b48ab73b865';
    private $scopes = 'read_products,write_products'; // Adjust scopes as needed
    private $redirect_uri = 'https://093b-122-160-255-97.ngrok-free.app/oauth/callback';

    public function index() {
        $shop = $this->input->get('shop');
        if (!$shop) {
            exit('Shop parameter missing');
        }
        $shop = urlencode($shop);
        $install_url = "https://{$shop}/admin/oauth/authorize?client_id={$this->shopify_api_key}&scope={$this->scopes}&redirect_uri={$this->redirect_uri}";
        redirect($install_url);
    }

    public function callback()
    {
        $shop = $this->request->getGet('shop');
        $code = $this->request->getGet('code');
        $state = $this->request->getGet('state');
        $hmac = $this->request->getGet('hmac');
        
        // Validate HMAC
        $query_params = $_GET;
        unset($query_params['hmac']);
        ksort($query_params);
        $query_string = http_build_query($query_params);
        $calculated_hmac = hash_hmac('sha256', $query_string, $this->client_secret);
    
        if (!hash_equals($hmac, $calculated_hmac)) {
            // HMAC is invalid, possible security issue
            log_message('error', 'Invalid HMAC: ' . $hmac . ' vs ' . $calculated_hmac);
            return redirect()->to('/')->with('error', 'Invalid authentication');
        }
    
        try {
            // HMAC is valid, proceed with exchanging the code for an access token
            $token_url = "https://{$shop}/admin/oauth/access_token";
            $response = $this->httpClient()->post($token_url, [
                'form_params' => [
                    'client_id' => $this->client_id,
                    'client_secret' => $this->client_secret,
                    'code' => $code
                ]
            ]);
            $data = json_decode($response->getBody(), true);
            
            if (isset($data['access_token'])) {
                $access_token = $data['access_token'];
                // Store the access token securely (e.g., in a database)
                // Redirect to a logged-in area of your application
                log_message('info', 'Access token received: ' . $access_token);
                return redirect()->to('/dashboard');
            } else {
                log_message('error', 'Access token not received: ' . json_encode($data));
                return redirect()->to('/')->with('error', 'Failed to receive access token');
            }
        } catch (\Exception $e) {
            log_message('error', 'Exception: ' . $e->getMessage());
            return redirect()->to('/')->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }
    
}
