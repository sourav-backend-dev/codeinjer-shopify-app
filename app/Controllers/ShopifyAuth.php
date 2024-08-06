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
        $base_url = $_ENV['APP_BASEURL'];
        $this->client_id = $_ENV['API_KEY'];
        $this->client_secret = $_ENV['APP_SECRET'];
        $this->scopes = $_ENV['ACCESS_SCOPES']; 
        $this->redirect_uri = $base_url . '/shopify/callback'; 
        $this->shopifyStoreModel = new ShopifyStoreModel(); 
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
        
        // Validate HMAC
        $query_params = $_GET;
        unset($query_params['hmac']);
        ksort($query_params);
        $query_string = http_build_query($query_params);
        $calculated_hmac = hash_hmac('sha256', $query_string, $this->client_secret);
        
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
                    return redirect()->to('/')->with('error', 'Failed to receive access token');
                }
            } catch (\Exception $e) {
                return redirect()->to('/')->with('error', 'An error occurred: ' . $e->getMessage());
            }
        } else {
            // HMAC is invalid, possible security issue
            return redirect()->to('/')->with('error', 'Invalid authentication');
        }
    }

    public function products()
    {
        $shop = $this->shopifyStoreModel->findAll()[0]['shop']; // Assuming only one shop is saved
        $access_token = $this->shopifyStoreModel->findAll()[0]['access_token']; // Assuming only one token is saved
        
        // Build the URL to fetch products
        $url = "https://{$shop}/admin/api/2024-01/products.json"; // Adjust API version as needed
        $response = $this->httpClient()->get($url, [
            'headers' => [
                'X-Shopify-Access-Token' => $access_token
            ]
        ]);
        $data = json_decode($response->getBody(), true);
        
        // Extract products from the response
        $products = $data['products'];
        
        // Pass products to the view
        return view('products', ['products' => $products]);
    }

    private function httpClient()
    {
        return Services::curlrequest();
    }
}
