<?php

namespace App\Models;

use CodeIgniter\Model;

class ShopifyStoreModel extends Model
{
    protected $table = 'shopify_stores';
    protected $primaryKey = 'id';
    protected $allowedFields = ['shop', 'access_token'];
    protected $useTimestamps = true;
}
