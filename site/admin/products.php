<?php
require_once 'auth.php'; // Authentication check
require_once '../config/database.php';

$database = new Database();
$db = $database->connect();

// Handle product actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'add') {
            $stmt = $db->prepare("INSERT INTO products (name, description, price, category, image, featured, stock) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $_POST['name'],
                $_POST['description'],
                $_POST['price'],
                $_POST['category'],
                $_POST['image'],
                isset($_POST['featured']) ? 1 : 0,
                $_POST['stock']
            ]);
            $message = "Product added successfully!";
        } elseif ($_POST['action'] === 'edit') {
            $stmt = $db->prepare("UPDATE products SET name=?, description=?, price=?, category=?, image=?, featured=?, stock=? WHERE id=?");
            $stmt->execute([
                $_POST['name'],
                $_POST['description'],
                $_POST['price'],
                $_POST['category'],
                $_POST['image'],
                isset($_POST['featured']) ? 1 : 0,
                $_POST['stock'],
                $_POST['id']
            ]);
            $message = "Product updated successfully!";
        } elseif ($_POST['action'] === 'delete') {
            $stmt = $db->prepare("DELETE FROM products WHERE id = ?");
            $stmt->execute([$_POST['id']]);
            $message = "Product deleted successfully!";
        }
    }
}

$products = $db->query("SELECT * FROM products ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products - Admin Panel</title>
    <link rel="stylesheet" href="../css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="admin-container">
        <aside class="sidebar">
            <div class="logo">
                <h2>Aluth<span>Elegance</span></h2>
                <p>Admin Panel</p>
            </div>
            <nav class="admin-nav">
                <a href="index.php"><i class="fas fa-home"></i> Dashboard</a>
                <a href="products.php" class="active"><i class="fas fa-box"></i> Products</a>
                <a href="orders.php"><i class="fas fa-shopping-cart"></i> Orders</a>
                <a href="settings.php"><i class="fas fa-cog"></i> Settings</a>
                <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </nav>
        </aside>
        
        <main class="main-content">
            <header class="admin-header">
                <h1>Products Management</h1>
                <button class="btn-primary" onclick="showAddModal()"><i class="fas fa-plus"></i> Add New Product</button>
            </header>
            
            <?php if (isset($message)): ?>
                <div class="alert-success"><?php echo $message; ?></div>
            <?php endif; ?>
            
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Image</th>
                            <th>Name</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th>Stock</th>
                            <th>Featured</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        if (count($products) > 0) {
                            foreach ($products as $product): 
                        ?>
                        <tr>
                            <td><?php echo $product['id']; ?></td>
                            <td><img src="../<?php echo htmlspecialchars($product['image']); ?>" width="50" style="border-radius:5px;" onerror="this.src='../images/placeholder.jpg'"></td>
                            <td><?php echo htmlspecialchars($product['name']); ?></td>
                            <td><?php echo htmlspecialchars($product['category']); ?></td>
                            <td>$<?php echo number_format((float)$product['price'], 2); ?></td>
                            <td><?php echo $product['stock']; ?></td>
                            <td><?php echo $product['featured'] ? '⭐ Yes' : 'No'; ?></td>
                            <td>
                                <button class="btn-edit" onclick='editProduct(<?php echo json_encode($product); ?>)'><i class="fas fa-edit"></i></button>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?php echo $product['id']; ?>">
                                    <button type="submit" class="btn-delete" onclick="return confirm('Delete this product?')"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        <?php 
                            endforeach;
                        } else {
                            echo "<tr><td colspan='8' style='text-align:center;'>No products yet. Click 'Add New Product' to get started!</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <!-- Add/Edit Product Modal -->
    <div id="productModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h2 id="modalTitle">Add New Product</h2>
            <form method="POST" id="productForm">
                <input type="hidden" name="action" id="formAction" value="add">
                <input type="hidden" name="id" id="productId">
                
                <div class="form-group">
                    <label>Product Name *</label>
                    <input type="text" name="name" id="productName" required>
                </div>
                
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" id="productDescription" rows="4"></textarea>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Price ($) *</label>
                        <input type="number" step="0.01" name="price" id="productPrice" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Stock Quantity *</label>
                        <input type="number" name="stock" id="productStock" value="0" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Category *</label>
                    <select name="category" id="productCategory" required>
                        <option value="">Select Category</option>
                        <option value="Tile Basins">Tile Basins</option>
                        <option value="Accessories">Bathroom Accessories</option>
                        <option value="Home Accents">Home Accents</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Image Path *</label>
                    <input type="text" name="image" id="productImage" placeholder="images/product.jpg" required>
                    <small>Upload image to images/ folder first, then enter path</small>
                </div>
                
                <div class="form-group">
                    <label class="checkbox-label">
                        <input type="checkbox" name="featured" id="productFeatured">
                        <span>Featured Product (Show on homepage)</span>
                    </label>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn-primary"><i class="fas fa-save"></i> Save Product</button>
                    <button type="button" class="btn-secondary" onclick="closeModal()">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function showAddModal() {
            document.getElementById('modalTitle').textContent = 'Add New Product';
            document.getElementById('formAction').value = 'add';
            document.getElementById('productForm').reset();
            document.getElementById('productModal').style.display = 'block';
        }
        
        function editProduct(product) {
            document.getElementById('modalTitle').textContent = 'Edit Product';
            document.getElementById('formAction').value = 'edit';
            document.getElementById('productId').value = product.id;
            document.getElementById('productName').value = product.name;
            document.getElementById('productDescription').value = product.description || '';
            document.getElementById('productPrice').value = product.price;
            document.getElementById('productStock').value = product.stock;
            document.getElementById('productCategory').value = product.category;
            document.getElementById('productImage').value = product.image;
            document.getElementById('productFeatured').checked = product.featured == 1;
            document.getElementById('productModal').style.display = 'block';
        }
        
        function closeModal() {
            document.getElementById('productModal').style.display = 'none';
        }
        
        window.onclick = function(event) {
            const modal = document.getElementById('productModal');
            if (event.target == modal) {
                closeModal();
            }
        }
    </script>
</body>
</html>
