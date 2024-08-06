<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products List</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <h1 class="my-4">Products List</h1>
        
        <!-- Display Success or Error Messages -->
        <?php if (isset($success_message)): ?>
            <div class="alert alert-success"><?= $success_message; ?></div>
        <?php endif; ?>
        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger"><?= $error_message; ?></div>
        <?php endif; ?>

        <!-- Products Table -->
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Vendor</th>
                    <th>Type</th>
                    <th>Price</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($products)): ?>
                    <?php foreach ($products as $product): ?>
                        <tr>
                            <td><?= $product['id']; ?></td>
                            <td><?= $product['title']; ?></td>
                            <td><?= $product['vendor']; ?></td>
                            <td><?= $product['product_type']; ?></td>
                            <td>
                                <?php if (!empty($product['variants'])): ?>
                                    <?= $product['variants'][0]['price']; ?>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="<?= site_url('products/update/' . $product['id']); ?>" class="btn btn-primary btn-sm">Edit</a>
                                <a href="<?= site_url('products/delete/' . $product['id']); ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center">No products found</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Link to Create New Product -->
        <a href="<?= site_url('products/create'); ?>" class="btn btn-success">Add New Product</a>
    </div>

    <!-- Include Bootstrap JavaScript -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
