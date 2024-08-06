<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <h1 class="my-4">Edit Product</h1>

        <!-- Form to Edit Product -->
        <form action="<?= site_url('products/update/' . $product['id']); ?>" method="post">
            <div class="form-group">
                <label for="title">Title</label>
                <input type="text" class="form-control" id="title" name="title" value="<?= $product['title']; ?>" required>
            </div>
            <div class="form-group">
                <label for="vendor">Vendor</label>
                <input type="text" class="form-control" id="vendor" name="vendor" value="<?= $product['vendor']; ?>" required>
            </div>
            <div class="form-group">
                <label for="product_type">Product Type</label>
                <input type="text" class="form-control" id="product_type" name="product_type" value="<?= $product['product_type']; ?>" required>
            </div>
            <div class="form-group">
                <label for="price">Price</label>
                <input type="number" step="0.01" class="form-control" id="price" name="price" value="<?= $product['variants'][0]['price']; ?>" required>
            </div>
            <button type="submit" class="btn btn-primary">Update Product</button>
            <a href="<?= site_url('products'); ?>" class="btn btn-secondary">Back to List</a>
        </form>
    </div>

    <!-- Include Bootstrap JavaScript -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
