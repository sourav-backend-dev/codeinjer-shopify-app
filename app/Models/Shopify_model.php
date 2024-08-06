<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use Shopify\Context;
use Shopify\Clients\Rest;
use Shopify\Auth\OAuth;
use Shopify\Exception\ApiException;

class Shopify_model extends CI_Model {

    private $api_key;
    private $api_secret;
    private $access_token;
    private $shop_domain;
    private $client;

    public function __construct() {
        parent::__construct();

        // Load the Shopify API configuration
        $this->api_key = 'your_api_key';
        $this->api_secret = 'your_api_secret';
        $this->access_token = $this->session->userdata('access_token'); // Assuming you store the token in session
        $this->shop_domain = $this->session->userdata('shop_domain'); // Assuming you store the shop domain in session

        // Initialize the Shopify API client
        Context::initialize(
            $this->api_key,
            $this->api_secret,
            $this->access_token
        );
        $this->client = new Rest($this->shop_domain, $this->access_token);
    }

    public function get_products() {
        try {
            $response = $this->client->get('/admin/api/2024-01/products.json');
            return $response['body']['products'];
        } catch (ApiException $e) {
            // Handle the API exception
            log_message('error', 'Shopify API Error: ' . $e->getMessage());
            return [];
        }
    }

    public function create_product($product_data) {
        try {
            $response = $this->client->post('/admin/api/2024-01/products.json', [
                'json' => ['product' => $product_data]
            ]);
            return $response['body']['product'];
        } catch (ApiException $e) {
            // Handle the API exception
            log_message('error', 'Shopify API Error: ' . $e->getMessage());
            return [];
        }
    }

    public function update_product($product_id, $product_data) {
        try {
            $response = $this->client->put('/admin/api/2024-01/products/' . $product_id . '.json', [
                'json' => ['product' => $product_data]
            ]);
            return $response['body']['product'];
        } catch (ApiException $e) {
            // Handle the API exception
            log_message('error', 'Shopify API Error: ' . $e->getMessage());
            return [];
        }
    }

    public function delete_product($product_id) {
        try {
            $this->client->delete('/admin/api/2024-01/products/' . $product_id . '.json');
            return true;
        } catch (ApiException $e) {
            // Handle the API exception
            log_message('error', 'Shopify API Error: ' . $e->getMessage());
            return false;
        }
    }
}
