<?php
// Ensure session is started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Initialize the cart if it doesn't exist
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

/**
 * Adds a product to the shopping cart.
 *
 * @param int $product_id The ID of the product to add.
 * @param int $quantity The quantity to add.
 * @return void
 */
function addToCart($product_id, $quantity = 1) {
    $product_id = intval($product_id);
    $quantity = intval($quantity);

    if ($product_id > 0 && $quantity > 0) {
        // If the product is already in the cart, increment the quantity
        if (isset($_SESSION['cart'][$product_id])) {
            $_SESSION['cart'][$product_id] += $quantity;
        } else {
            // Otherwise, add the product to the cart
            $_SESSION['cart'][$product_id] = $quantity;
        }
    }
}

/**
 * Updates the quantity of a product in the cart.
 *
 * @param int $product_id The ID of the product to update.
 * @param int $quantity The new quantity.
 * @return void
 */
function updateCartQuantity($product_id, $quantity) {
    $product_id = intval($product_id);
    $quantity = intval($quantity);

    if ($product_id > 0) {
        if ($quantity > 0) {
            $_SESSION['cart'][$product_id] = $quantity;
        } else {
            // If quantity is 0 or less, remove the item
            removeFromCart($product_id);
        }
    }
}

/**
 * Removes a product from the shopping cart.
 *
 * @param int $product_id The ID of the product to remove.
 * @return void
 */
function removeFromCart($product_id) {
    $product_id = intval($product_id);
    if (isset($_SESSION['cart'][$product_id])) {
        unset($_SESSION['cart'][$product_id]);
    }
}

/**
 * Gets the total number of items in the cart.
 *
 * @return int Total number of items.
 */
function getCartItemCount() {
    return array_sum($_SESSION['cart']);
}

/**
 * Gets the contents of the shopping cart with product details from the database.
 *
 * @param mysqli $conn The database connection object.
 * @return array An array of cart items with full details.
 */
function getCartContents($conn) {
    $cart = $_SESSION['cart'];
    $cart_items = [];

    if (empty($cart)) {
        return $cart_items;
    }

    $product_ids = array_keys($cart);
    $ids_string = implode(',', $product_ids);

    $sql = "SELECT id, name, price_usd, image FROM products WHERE id IN ($ids_string)";
    
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $product_id = $row['id'];
            $row['quantity'] = $cart[$product_id];
            $cart_items[] = $row;
        }
    }

    return $cart_items;
}
?> 