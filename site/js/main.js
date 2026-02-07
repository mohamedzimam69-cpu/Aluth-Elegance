// Cart functionality
let cart = JSON.parse(localStorage.getItem('cart')) || [];
let wishlist = JSON.parse(localStorage.getItem('wishlist')) || [];

function updateCartCount() {
    const cartCount = document.querySelector('.cart-count');
    if (cartCount) {
        const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
        cartCount.textContent = totalItems;
        cartCount.style.display = totalItems > 0 ? 'flex' : 'none';
    }
}

function addToCart(product) {
    const existingItem = cart.find(item => item.id === product.id);
    if (existingItem) {
        existingItem.quantity++;
    } else {
        cart.push({ ...product, quantity: 1 });
    }
    localStorage.setItem('cart', JSON.stringify(cart));
    updateCartCount();
    showNotification('✓ Product added to cart!', 'success');
}

function toggleWishlist(productId) {
    const index = wishlist.indexOf(productId);
    if (index > -1) {
        wishlist.splice(index, 1);
        showNotification('Removed from wishlist', 'info');
    } else {
        wishlist.push(productId);
        showNotification('✓ Added to wishlist!', 'success');
    }
    localStorage.setItem('wishlist', JSON.stringify(wishlist));
    updateWishlistIcons();
}

function updateWishlistIcons() {
    // Update wishlist icon color if item is in wishlist
    document.querySelectorAll('[data-product-id]').forEach(icon => {
        const productId = parseInt(icon.dataset.productId);
        if (wishlist.includes(productId)) {
            icon.style.color = '#D4AF37';
        } else {
            icon.style.color = '';
        }
    });
}

function showNotification(message, type = 'success') {
    const notification = document.createElement('div');
    const bgColor = type === 'success' ? '#27ae60' : type === 'error' ? '#e74c3c' : '#3498db';
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: ${bgColor};
        color: white;
        padding: 15px 25px;
        border-radius: 5px;
        z-index: 9999;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        animation: slideIn 0.3s ease;
    `;
    notification.textContent = message;
    document.body.appendChild(notification);
    setTimeout(() => {
        notification.style.animation = 'slideOut 0.3s ease';
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

// Search functionality
function toggleSearch() {
    const searchModal = document.getElementById('searchModal');
    if (searchModal) {
        searchModal.style.display = searchModal.style.display === 'block' ? 'none' : 'block';
        if (searchModal.style.display === 'block') {
            document.getElementById('searchInput').focus();
        }
    } else {
        createSearchModal();
    }
}

function createSearchModal() {
    const modal = document.createElement('div');
    modal.id = 'searchModal';
    modal.innerHTML = `
        <div style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.8); z-index: 9999; display: flex; align-items: flex-start; justify-content: center; padding-top: 100px;">
            <div style="background: white; padding: 30px; border-radius: 10px; width: 90%; max-width: 600px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <h2 style="margin: 0;">Search Products</h2>
                    <button onclick="toggleSearch()" style="background: none; border: none; font-size: 24px; cursor: pointer;">&times;</button>
                </div>
                <input type="text" id="searchInput" placeholder="Search for products..." style="width: 100%; padding: 12px; border: 2px solid #ddd; border-radius: 5px; font-size: 16px;">
                <div id="searchResults" style="margin-top: 20px;"></div>
            </div>
        </div>
    `;
    document.body.appendChild(modal);
    document.getElementById('searchInput').focus();
    
    // Close on outside click
    modal.addEventListener('click', (e) => {
        if (e.target === modal) toggleSearch();
    });
}

// View Cart
function viewCart() {
    if (cart.length === 0) {
        showNotification('Your cart is empty', 'info');
        return;
    }
    
    const cartModal = document.createElement('div');
    cartModal.innerHTML = `
        <div style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.8); z-index: 9999; display: flex; align-items: center; justify-content: center; padding: 20px;">
            <div style="background: white; padding: 30px; border-radius: 10px; width: 90%; max-width: 600px; max-height: 80vh; overflow-y: auto;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <h2 style="margin: 0;">Shopping Cart</h2>
                    <button onclick="this.closest('div').parentElement.parentElement.remove()" style="background: none; border: none; font-size: 24px; cursor: pointer;">&times;</button>
                </div>
                <div id="cartItems">
                    ${cart.map(item => `
                        <div style="display: flex; justify-content: space-between; align-items: center; padding: 15px; border-bottom: 1px solid #eee;">
                            <div>
                                <h4 style="margin: 0 0 5px 0;">${item.name}</h4>
                                <p style="margin: 0; color: #666;">$${item.price} x ${item.quantity}</p>
                            </div>
                            <div style="display: flex; gap: 10px; align-items: center;">
                                <button onclick="updateQuantity(${item.id}, -1)" style="padding: 5px 10px; background: #e74c3c; color: white; border: none; border-radius: 3px; cursor: pointer;">-</button>
                                <span>${item.quantity}</span>
                                <button onclick="updateQuantity(${item.id}, 1)" style="padding: 5px 10px; background: #27ae60; color: white; border: none; border-radius: 3px; cursor: pointer;">+</button>
                                <button onclick="removeFromCart(${item.id})" style="padding: 5px 10px; background: #95a5a6; color: white; border: none; border-radius: 3px; cursor: pointer;">Remove</button>
                            </div>
                        </div>
                    `).join('')}
                </div>
                <div style="margin-top: 20px; padding-top: 20px; border-top: 2px solid #ddd;">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 15px;">
                        <strong>Total:</strong>
                        <strong style="color: #D4AF37; font-size: 24px;">$${calculateTotal()}</strong>
                    </div>
                    <button onclick="checkout()" style="width: 100%; padding: 15px; background: #D4AF37; color: white; border: none; border-radius: 5px; font-size: 16px; font-weight: 600; cursor: pointer;">Proceed to Checkout</button>
                </div>
            </div>
        </div>
    `;
    document.body.appendChild(cartModal);
}

function updateQuantity(productId, change) {
    const item = cart.find(item => item.id === productId);
    if (item) {
        item.quantity += change;
        if (item.quantity <= 0) {
            removeFromCart(productId);
        } else {
            localStorage.setItem('cart', JSON.stringify(cart));
            updateCartCount();
            // Refresh cart view
            document.querySelector('[onclick*="viewCart"]').click();
            viewCart();
        }
    }
}

function removeFromCart(productId) {
    cart = cart.filter(item => item.id !== productId);
    localStorage.setItem('cart', JSON.stringify(cart));
    updateCartCount();
    showNotification('Item removed from cart', 'info');
    // Close and reopen cart to refresh
    const cartModal = document.querySelector('[style*="z-index: 9999"]');
    if (cartModal) {
        cartModal.remove();
        if (cart.length > 0) {
            viewCart();
        }
    }
}

function calculateTotal() {
    return cart.reduce((sum, item) => sum + (item.price * item.quantity), 0).toFixed(2);
}

function checkout() {
    showNotification('Checkout feature coming soon!', 'info');
    // In a real implementation, this would redirect to checkout page
}

// Load featured products
async function loadFeaturedProducts() {
    try {
        const response = await fetch('api/products.php?featured=true');
        const products = await response.json();
        displayProducts(products);
    } catch (error) {
        console.error('Error loading products:', error);
        // Show sample products if API fails
        displayProducts([
            {id: 1, name: 'Venetian Marble Basin', price: 1299, category: 'Tile Basins', image: 'images/product1.jpg'},
            {id: 2, name: 'Azure Mosaic Sink', price: 899, category: 'Tile Basins', image: 'images/product2.jpg'},
            {id: 3, name: 'Golden Soap Dispenser', price: 149, category: 'Accessories', image: 'images/product3.jpg'},
            {id: 4, name: 'Terracotta Vanity Set', price: 349, category: 'Home Accents', image: 'images/product4.jpg'}
        ]);
    }
}

function displayProducts(products) {
    const container = document.getElementById('featured-products');
    if (!container) return;
    
    container.innerHTML = products.map(product => `
        <div class="product-card">
            <div class="product-image">
                <img src="${product.image}" alt="${product.name}" onerror="this.style.display='none'">
                <span class="product-badge">${product.category}</span>
            </div>
            <div class="product-info">
                <h3>${product.name}</h3>
                <p class="product-price">$${product.price}</p>
                <button class="btn btn-primary" onclick='addToCart(${JSON.stringify(product)})'>
                    <i class="fas fa-shopping-cart"></i> Add to Cart
                </button>
            </div>
        </div>
    `).join('');
}

// Navigation functions
function navigateToShop(event) {
    event.preventDefault();
    showNotification('Loading shop...', 'info');
    setTimeout(() => {
        window.location.href = 'shop.html';
    }, 300);
}

function navigateToCollections(event) {
    event.preventDefault();
    showNotification('Loading collections...', 'info');
    setTimeout(() => {
        window.location.href = 'collections.html';
    }, 300);
}

// Add CSS animations
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    @keyframes slideOut {
        from { transform: translateX(0); opacity: 1; }
        to { transform: translateX(100%); opacity: 0; }
    }
    .btn {
        position: relative;
        overflow: hidden;
    }
    .btn::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 0;
        height: 0;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.3);
        transform: translate(-50%, -50%);
        transition: width 0.6s, height 0.6s;
    }
    .btn:active::before {
        width: 300px;
        height: 300px;
    }
`;
document.head.appendChild(style);

// Initialize
document.addEventListener('DOMContentLoaded', () => {
    updateCartCount();
    if (document.getElementById('featured-products')) {
        loadFeaturedProducts();
    }
    
    // Add click handlers to header icons
    const searchIcon = document.querySelector('.header-icons .fa-search');
    if (searchIcon) {
        searchIcon.style.cursor = 'pointer';
        searchIcon.addEventListener('click', toggleSearch);
    }
    
    const heartIcon = document.querySelector('.header-icons .fa-heart');
    if (heartIcon) {
        heartIcon.style.cursor = 'pointer';
        heartIcon.addEventListener('click', () => {
            showNotification('Wishlist feature - Click heart on products to add!', 'info');
        });
    }
    
    const userIcon = document.querySelector('.header-icons .fa-user');
    if (userIcon) {
        userIcon.style.cursor = 'pointer';
        userIcon.addEventListener('click', () => {
            window.location.href = 'admin/login.php';
        });
    }
    
    const cartIcon = document.querySelector('.cart-icon');
    if (cartIcon) {
        cartIcon.style.cursor = 'pointer';
        cartIcon.addEventListener('click', viewCart);
    }
});
