<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Products extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Shopify_model');
    }

    public function index() {
        $data['products'] = $this->Shopify_model->get_products();
        $this->load->view('products_view', $data);
    }

    public function create() {
        if ($this->input->post()) {
            $product_data = [
                'title' => $this->input->post('title'),
                'vendor' => $this->input->post('vendor'),
                'product_type' => $this->input->post('product_type'),
                'variants' => [
                    [
                        'option1' => 'First',
                        'price' => $this->input->post('price'),
                        'sku' => '123'
                    ]
                ]
            ];
            $result = $this->Shopify_model->create_product($product_data);
            if ($result) {
                $this->session->set_flashdata('success_message', 'Product added successfully!');
            } else {
                $this->session->set_flashdata('error_message', 'Failed to add product.');
            }
            redirect('products');
        } else {
            $this->load->view('add_product');
        }
    }

    public function update($product_id) {
        if ($this->input->post()) {
            $product_data = [
                'title' => $this->input->post('title'),
                'vendor' => $this->input->post('vendor'),
                'product_type' => $this->input->post('product_type'),
                'variants' => [
                    [
                        'price' => $this->input->post('price')
                    ]
                ]
            ];
            $result = $this->Shopify_model->update_product($product_id, $product_data);
            if ($result) {
                $this->session->set_flashdata('success_message', 'Product updated successfully!');
            } else {
                $this->session->set_flashdata('error_message', 'Failed to update product.');
            }
            redirect('products');
        } else {
            $data['product'] = $this->Shopify_model->get_product($product_id); // Assume you have a method to get a single product
            $this->load->view('edit_product', $data);
        }
    }

    public function delete($product_id) {
        $result = $this->Shopify_model->delete_product($product_id);
        if ($result) {
            $this->session->set_flashdata('success_message', 'Product deleted successfully!');
        } else {
            $this->session->set_flashdata('error_message', 'Failed to delete product.');
        }
        redirect('products');
    }
}
