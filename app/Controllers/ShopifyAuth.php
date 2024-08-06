<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use Config\Services;
use App\Models\ShopifyStoreModel;

class ShopifyAuth extends Controller
{
    protected $client_id;
    protected $client_secret;
    protected $scopes;
    protected $redirect_uri;
    protected $shopifyStoreModel;

    public function __construct()
    {
        $this->client_id = '0d5956f22de6e2cb4c296df486864dcb';
        $this->client_secret = 'f67e6b86d973696b47a42b48ab73b865';
        $this->scopes = 'read_products,write_products'; // Modify scopes as needed
        $this->redirect_uri = 'https://093b-122-160-255-97.ngrok-free.app/shopify/callback'; // Use ngrok or production URL
        $this->shopifyStoreModel = new ShopifyStoreModel(); // Initialize the model
    }

    public function authorize()
    {
        $shop = $this->request->getGet('shop');
        if (!$shop) {
            return redirect()->to('/'); // Redirect to home if shop parameter is missing
        }

        // Build the Shopify authorization URL
        $url = "https://{$shop}/admin/oauth/authorize";
        $params = [
            'client_id' => $this->client_id,
            'scope' => $this->scopes,
            'redirect_uri' => $this->redirect_uri,
            'state' => bin2hex(random_bytes(16)) // Generate a random state parameter
        ];
        
        // Redirect the user to Shopify for authorization
        return redirect()->to($url . '?' . http_build_query($params));
    }

    public function callback()
{
    $shop = $this->request->getGet('shop');
    $code = $this->request->getGet('code');
    $state = $this->request->getGet('state');
    $hmac = $this->request->getGet('hmac');
    
    log_message('debug', 'Shop: ' . $shop);
    log_message('debug', 'Code: ' . $code);
    log_message('debug', 'State: ' . $state);
    log_message('debug', 'HMAC: ' . $hmac);
    
    // Validate HMAC
    $query_params = $_GET;
    unset($query_params['hmac']);
    ksort($query_params);
    $query_string = http_build_query($query_params);
    $calculated_hmac = hash_hmac('sha256', $query_string, $this->client_secret);
    
    log_message('debug', 'Calculated HMAC: ' . $calculated_hmac);
    
    if (hash_equals($hmac, $calculated_hmac)) {
        // HMAC is valid, proceed with exchanging the code for an access token
        try {
            $token_url = "https://{$shop}/admin/oauth/access_token";
            $response = $this->httpClient()->post($token_url, [
                'form_params' => [
                    'client_id' => $this->client_id,
                    'client_secret' => $this->client_secret,
                    'code' => $code
                ]
            ]);
            $data = json_decode($response->getBody(), true);
    
            log_message('debug', 'Access token response: ' . json_encode($data));
    
            if (isset($data['access_token'])) {
                $access_token = $data['access_token'];
                
                // Check if the shop already exists
                $existingStore = $this->shopifyStoreModel->where('shop', $shop)->first();
                
                if ($existingStore) {
                    // Update existing record
                    $this->shopifyStoreModel->update($existingStore['id'], [
                        'access_token' => $access_token
                    ]);
                } else {
                    // Insert new record
                    $this->shopifyStoreModel->save([
                        'shop' => $shop,
                        'access_token' => $access_token
                    ]);
                }

                // Redirect to products page
                return redirect()->to('/shopify/products');
            } else {
                log_message('error', 'Access token not received: ' . json_encode($data));
                return redirect()->to('/')->with('error', 'Failed to receive access token');
            }
        } catch (\Exception $e) {
            log_message('error', 'Exception: ' . $e->getMessage());
            return redirect()->to('/')->with('error', 'An error occurred: ' . $e->getMessage());
        }
    } else {
        // HMAC is invalid, possible security issue
        log_message('error', 'Invalid HMAC: ' . $hmac . ' vs ' . $calculated_hmac);
        return redirect()->to('/')->with('error', 'Invalid authentication');
    }
}

    public function products()
    {
        $shop = $this->shopifyStoreModel->findAll()[0]['shop']; // Assuming only one shop is saved
        $access_token = $this->shopifyStoreModel->findAll()[0]['access_token']; // Assuming only one token is saved
        
        $url = "https://{$shop}/admin/api/2024-01/products.json"; // Adjust API version as needed
        $response = $this->httpClient()->get($url, [
            'headers' => [
                'X-Shopify-Access-Token' => $access_token
            ]
        ]);
        $data = json_decode($response->getBody(), true);
        
        // Extract products
        $products = $data['products'];
        
        // Pass products to the view
        return view('products', ['products' => $products]);
    }

    private function httpClient()
    {
        return Services::curlrequest();
    }
}
