<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopify Products</title>
    <link rel="stylesheet" href="https://unpkg.com/@shopify/polaris@latest/build/esm/styles.css">
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'San Francisco', Roboto, 'Segoe UI', 'Helvetica Neue', sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f4f6f8;
        }
        .Polaris-Page {
            max-width: 1200px;
            margin: auto;
            background: #ffffff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.12);
        }
        .Polaris-Page__Header {
            border-bottom: 1px solid #dfe3e8;
            padding-bottom: 20px;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table th, table td {
            padding: 10px;
            border: 1px solid #dfe3e8;
            text-align: left;
        }
        table th {
            background-color: #f4f6f8;
        }
    </style>
</head>
<body>
    <div class="Polaris-Page">
        <div class="Polaris-Page__Header">
            <h1 class="Polaris-DisplayText Polaris-DisplayText--sizeLarge">Shopify Products</h1>
        </div>
        <table class="Polaris-DataTable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Price</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $product): ?>
                    <tr>
                        <td><?= esc($product['id']) ?></td>
                        <td><?= esc($product['title']) ?></td>
                        <td><?= esc($product['body_html']) ?></td>
                        <td><?= esc($product['variants'][0]['price']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
