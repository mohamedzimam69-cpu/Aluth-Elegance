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
                            <td>Rs. <?php echo number_format((float)$product['price'], 2); ?></td>
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
                
                <div class="form-group">
                    <label>Price (LKR) *</label>
                    <input type="number" step="0.01" name="price" id="productPrice" placeholder="129900.00" required>
                    <small>Enter price in Sri Lankan Rupees (e.g., 129900.00)</small>
                </div>
                <div class="form-group">
                    <label>Stock Quantity *</label>
                    <input type="number" name="stock" id="productStock" value="0" required>
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
                    <label>Image Upload *</label>
                    <div class="image-upload-container">
                        <div class="image-preview" id="imagePreview">
                            <i class="fas fa-image" style="font-size: 48px; color: #ddd;"></i>
                            <p style="color: #999; margin-top: 10px;">No image selected</p>
                        </div>
                        <div class="upload-controls">
                            <input type="file" id="imageFile" accept="image/*" style="display: none;">
                            <button type="button" class="btn-upload" onclick="document.getElementById('imageFile').click()">
                                <i class="fas fa-upload"></i> Choose Image
                            </button>
                            <p style="font-size: 12px; color: #666; margin-top: 10px;">
                                Supported: JPG, PNG, GIF, WEBP (Max 5MB)
                            </p>
                        </div>
                    </div>
                    <input type="hidden" name="image" id="productImage" required>
                    <div id="uploadProgress" style="display: none; margin-top: 10px;">
                        <div style="background: #e0e0e0; border-radius: 10px; height: 20px; overflow: hidden;">
                            <div id="progressBar" style="background: #D4AF37; height: 100%; width: 0%; transition: width 0.3s;"></div>
                        </div>
                        <p id="uploadStatus" style="font-size: 12px; color: #666; margin-top: 5px;">Uploading...</p>
                    </div>
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
        // Image upload handling
        let uploadedImagePath = '';
        
        document.getElementById('imageFile').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (!file) return;
            
            // Show preview
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('imagePreview').innerHTML = `
                    <img src="${e.target.result}" style="max-width: 100%; max-height: 200px; border-radius: 8px;">
                `;
            };
            reader.readAsDataURL(file);
            
            // Upload file
            const formData = new FormData();
            formData.append('image', file);
            
            document.getElementById('uploadProgress').style.display = 'block';
            document.getElementById('uploadStatus').textContent = 'Uploading...';
            
            fetch('upload-image.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    uploadedImagePath = data.path;
                    document.getElementById('productImage').value = data.path;
                    document.getElementById('progressBar').style.width = '100%';
                    document.getElementById('uploadStatus').textContent = '✓ Upload successful!';
                    document.getElementById('uploadStatus').style.color = '#27ae60';
                    
                    setTimeout(() => {
                        document.getElementById('uploadProgress').style.display = 'none';
                    }, 2000);
                } else {
                    alert('Upload failed: ' + data.error);
                    document.getElementById('uploadProgress').style.display = 'none';
                }
            })
            .catch(error => {
                alert('Upload error: ' + error);
                document.getElementById('uploadProgress').style.display = 'none';
            });
        });
        
        function showAddModal() {
            document.getElementById('modalTitle').textContent = 'Add New Product';
            document.getElementById('formAction').value = 'add';
            document.getElementById('productForm').reset();
            document.getElementById('imagePreview').innerHTML = `
                <i class="fas fa-image" style="font-size: 48px; color: #ddd;"></i>
                <p style="color: #999; margin-top: 10px;">No image selected</p>
            `;
            uploadedImagePath = '';
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
            
            // Show existing image
            if (product.image) {
                document.getElementById('imagePreview').innerHTML = `
                    <img src="../${product.image}" style="max-width: 100%; max-height: 200px; border-radius: 8px;" onerror="this.src='data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 width=%22200%22 height=%22200%22><rect fill=%22%23ddd%22 width=%22200%22 height=%22200%22/><text x=%2250%25%22 y=%2250%25%22 text-anchor=%22middle%22 dy=%22.3em%22 fill=%22%23999%22>No Image</text></svg>'">
                `;
            }
            
            uploadedImagePath = product.image;
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
