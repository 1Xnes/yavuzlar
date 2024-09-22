<?php

function registerUser($pdo, $name, $surname, $username, $password, $role = 'customer') {
    $hashedPassword = password_hash($password, PASSWORD_ARGON2ID);
    
    $sql = "INSERT INTO users (name, surname, username, password, role) VALUES (?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([$name, $surname, $username, $hashedPassword, $role]);
}

function getAllUsers($pdo) {
    $sql = "SELECT * FROM users ORDER BY role, name";
    $stmt = $pdo->query($sql);
    return $stmt->fetchAll();
}

function createDefaultAdminUser($pdo) {
    // Önce admin kullanıcısının var olup olmadığını kontrol edelim
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = 'admin' AND role = 'admin'");
    $stmt->execute();
    $count = $stmt->fetchColumn();

    // Eğer admin kullanıcısı yoksa, oluşturalım
    if ($count == 0) {
        $sql = "INSERT INTO users (role, name, surname, username, password, balance)
                VALUES (?, ?, ?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE username = VALUES(username)";
    
        $stmt = $pdo->prepare($sql);
        $password = password_hash('admin', PASSWORD_ARGON2ID);
        return $stmt->execute(['admin', 'Admin', 'User', 'admin', $password, 5000]);
    }

    return false; // Zaten admin kullanıcısı var, bir şey yapmadık
}
function isAdminUserExists($pdo) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = 'admin' AND role = 'admin'");
    $stmt->execute();
    return $stmt->fetchColumn() > 0;
}
function loginUser($pdo, $username, $password) {
    $sql = "SELECT u.*, c.deleted_at as company_deleted_at 
            FROM users u 
            LEFT JOIN company c ON u.company_id = c.id 
            WHERE u.username = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        // Kullanıcı silinmiş mi kontrol et
        if ($user['deleted_at'] !== null) {
            return false; // Kullanıcı silinmiş, giriş engellendi
        }

        // Eğer kullanıcı bir şirket ise, şirketin silinip silinmediğini kontrol et
        if ($user['role'] === 'company' && $user['company_deleted_at'] !== null) {
            return false; // Şirket silinmiş, giriş engellendi
        }

        // Giriş başarılı
        return $user;
    }
    return false; // Kullanıcı bulunamadı veya şifre yanlış
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: login.php");
        exit();
    }
}

function requireAdmin() {
    if (!isAdmin()) {
        header("Location: index.php");
        exit();
    }
}

function getCustomers($pdo, $filter = 'all') {
    $sql = "SELECT * FROM users WHERE role = 'customer'";
    if ($filter === 'active') {
        $sql .= " AND deleted_at IS NULL";
    } elseif ($filter === 'banned') {
        $sql .= " AND deleted_at IS NOT NULL";
    }
    $stmt = $pdo->query($sql);
    return $stmt->fetchAll();
}
function searchCustomers($pdo, $search) {
    $sql = "SELECT * FROM users WHERE role = 'customer' AND (name LIKE ? OR surname LIKE ? OR username LIKE ?)";
    $stmt = $pdo->prepare($sql);
    $searchTerm = "%$search%";
    $stmt->execute([$searchTerm, $searchTerm, $searchTerm]);
    return $stmt->fetchAll();
}

function banCustomer($pdo, $customerId) {
    $sql = "UPDATE users SET deleted_at = CURRENT_TIMESTAMP WHERE id = ? AND role = 'customer'";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([$customerId]);
}

function unbanCustomer($pdo, $customerId) {
    $sql = "UPDATE users SET deleted_at = NULL WHERE id = ? AND role = 'customer'";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([$customerId]);
}

function addUser($pdo, $name, $surname, $username, $password, $role) {
    $hashedPassword = password_hash($password, PASSWORD_ARGON2ID);
    $sql = "INSERT INTO users (name, surname, username, password, role) VALUES (?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([$name, $surname, $username, $hashedPassword, $role]);
}

function addCompanyUser($pdo, $name, $surname, $username, $password, $companyName, $companyDescription, $companyLogo) {
    $pdo->beginTransaction();
    
    try {
        // Şirket oluştur
        $sql = "INSERT INTO company (name, description) VALUES (?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$companyName, $companyDescription]);
        $companyId = $pdo->lastInsertId();
        
        // Logo yükle
        if ($companyLogo && $companyLogo['error'] === UPLOAD_ERR_OK) {
            $logoPath = uploadImage($companyLogo, 'company_logos');
            $sql = "UPDATE company SET logo_path = ? WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$logoPath, $companyId]);
        }
        
        // Kullanıcı oluştur
        $hashedPassword = password_hash($password, PASSWORD_ARGON2ID);
        $sql = "INSERT INTO users (name, surname, username, password, role, company_id) VALUES (?, ?, ?, ?, 'company', ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$name, $surname, $username, $hashedPassword, $companyId]);
        
        $pdo->commit();
        return true;
    } catch (Exception $e) {
        $pdo->rollBack();
        return false;
    }
}

function uploadImage($file, $directory) {
    $targetDir = "/var/www/html/uploads/" . $directory . "/";
    if (!file_exists($targetDir)) {
        mkdir($targetDir, 0755, true);
    }
    $fileName = uniqid() . "_" . basename($file["name"]);
    $targetFile = $targetDir . $fileName;
    
    if (move_uploaded_file($file["tmp_name"], $targetFile)) {
        return "/uploads/" . $directory . "/" . $fileName;
    } else {
        throw new Exception("Dosya yüklenirken bir hata oluştu: " . error_get_last()['message']);
    }
}

function getCompanies($pdo, $filter = 'all') {
    $sql = "SELECT c.*, u.name as owner_name, u.surname as owner_surname 
            FROM company c 
            LEFT JOIN users u ON c.id = u.company_id 
            WHERE u.role = 'company'";
    
    if ($filter === 'active') {
        $sql .= " AND c.deleted_at IS NULL";
    } elseif ($filter === 'deleted') {
        $sql .= " AND c.deleted_at IS NOT NULL";
    }
    
    $stmt = $pdo->query($sql);
    return $stmt->fetchAll();
}

function getCompanyDetails($pdo, $companyId) {
    $sql = "SELECT c.*, u.name as owner_name, u.surname as owner_surname,
                   (SELECT COUNT(*) FROM restaurant WHERE company_id = c.id) as restaurant_count
            FROM company c 
            LEFT JOIN users u ON c.id = u.company_id 
            WHERE c.id = ? AND u.role = 'company'";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$companyId]);
    return $stmt->fetch();
}

function updateCompany($pdo, $companyId, $name, $description, $logo = null) {
    $sql = "UPDATE company SET name = ?, description = ? WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute([$name, $description, $companyId]);

    if ($logo && $logo['error'] === UPLOAD_ERR_OK) {
        $logoPath = uploadImage($logo, 'company_logos');
        $sql = "UPDATE company SET logo_path = ? WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$logoPath, $companyId]);
    }

    return $result;
}

function deleteCompany($pdo, $companyId) {
    $sql = "UPDATE company SET deleted_at = CURRENT_TIMESTAMP WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([$companyId]);
}

function getCustomerDetails($pdo, $customerId) {
    $sql = "SELECT u.*, 
                   (SELECT COUNT(*) FROM `order` WHERE user_id = u.id) as order_count,
                   (SELECT SUM(total_price) FROM `order` WHERE user_id = u.id) as total_spent
            FROM users u 
            WHERE u.id = ? AND u.role = 'customer'";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$customerId]);
    return $stmt->fetch();
}

function getUserById($pdo, $userId) {
    $sql = "SELECT * FROM users WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$userId]);
    return $stmt->fetch();
}

function updateUser($pdo, $userId, $name, $surname, $username, $role) {
    $sql = "UPDATE users SET name = ?, surname = ?, username = ?, role = ? WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([$name, $surname, $username, $role, $userId]);
}

function deleteUser($pdo, $userId) {
    $sql = "UPDATE users SET deleted_at = CURRENT_TIMESTAMP WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([$userId]);
}

function searchCompanies($pdo, $search) {
    $sql = "SELECT c.*, u.name as owner_name, u.surname as owner_surname 
            FROM company c 
            LEFT JOIN users u ON c.id = u.company_id 
            WHERE c.name LIKE ? AND u.role = 'company'";
    $stmt = $pdo->prepare($sql);
    $searchTerm = "%$search%";
    $stmt->execute([$searchTerm]);
    return $stmt->fetchAll();
}

function restoreCompany($pdo, $companyId) {
    $sql = "UPDATE company SET deleted_at = NULL WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([$companyId]);
}

function getCoupons($pdo) {
    $sql = "SELECT c.*, r.name as restaurant_name 
            FROM coupon c 
            LEFT JOIN restaurant r ON c.restaurant_id = r.id";
    $stmt = $pdo->query($sql);
    return $stmt->fetchAll();
}

function getRestaurants($pdo) {
    $sql = "SELECT id, name FROM restaurant";
    $stmt = $pdo->query($sql);
    return $stmt->fetchAll();
}

function addCoupon($pdo, $name, $discount, $restaurantId) {
    $sql = "INSERT INTO coupon (name, discount, restaurant_id) VALUES (?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([$name, $discount, $restaurantId]);
}

function getCouponById($pdo, $couponId) {
    $sql = "SELECT * FROM coupon WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$couponId]);
    return $stmt->fetch();
}

function updateCoupon($pdo, $couponId, $name, $discount, $restaurantId) {
    $sql = "UPDATE coupon SET name = ?, discount = ?, restaurant_id = ? WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([$name, $discount, $restaurantId, $couponId]);
}

function deleteCoupon($pdo, $couponId) {
    $sql = "DELETE FROM coupon WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([$couponId]);
}

function getCustomersWithActiveOrders($pdo) {
    $sql = "SELECT u.*, 
                   (SELECT COUNT(*) FROM `order` o WHERE o.user_id = u.id AND o.order_status != 'Teslim Edildi') as active_order_count
            FROM users u 
            WHERE u.role = 'customer'
            ORDER BY active_order_count DESC";
    $stmt = $pdo->query($sql);
    return $stmt->fetchAll();
}

function getActiveOrdersForCustomer($pdo, $customerId) {
    $sql = "SELECT DISTINCT o.*, oi.note
            FROM `order` o
            JOIN order_items oi ON o.id = oi.order_id
            WHERE o.user_id = ? AND o.order_status != 'Teslim Edildi'";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$customerId]);
    return $stmt->fetchAll();
}
function requireRole($role) {
    if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== $role) {
        header("Location: index.php");
        exit();
    }
}

function getCompanyInfoByUserId($pdo, $userId) {
    $sql = "SELECT c.* FROM company c
            JOIN users u ON c.id = u.company_id
            WHERE u.id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$userId]);
    return $stmt->fetch();
}

function getRestaurantsByCompanyId($pdo, $companyId) {
    $sql = "SELECT * FROM restaurant WHERE company_id = ? ORDER BY name";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$companyId]);
    return $stmt->fetchAll();
}

function getOrdersByCompanyId($pdo, $companyId, $status = '', $dateFrom = '', $dateTo = '') {
    $sql = "SELECT o.id, o.user_id, o.order_status, o.total_price, o.created_at,
                   u.name as customer_name, r.name as restaurant_name
            FROM `order` o
            JOIN order_items oi ON o.id = oi.order_id
            JOIN food f ON oi.food_id = f.id
            JOIN restaurant r ON f.restaurant_id = r.id
            JOIN users u ON o.user_id = u.id
            WHERE r.company_id = ?";
    $params = [$companyId];

    if ($status) {
        $sql .= " AND o.order_status = ?";
        $params[] = $status;
    }

    if ($dateFrom) {
        $sql .= " AND o.created_at >= ?";
        $params[] = $dateFrom . ' 00:00:00';
    }

    if ($dateTo) {
        $sql .= " AND o.created_at <= ?";
        $params[] = $dateTo . ' 23:59:59';
    }

    $sql .= " GROUP BY o.id, o.user_id, o.order_status, o.total_price, o.created_at, u.name, r.name
              ORDER BY o.created_at DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}


function isCompany() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'company';
}


function addRestaurant($pdo, $companyId, $name, $description, $image = null) {
    $sql = "INSERT INTO restaurant (company_id, name, description) VALUES (?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute([$companyId, $name, $description]);

    if ($result && $image && $image['error'] === UPLOAD_ERR_OK) {
        $restaurantId = $pdo->lastInsertId();
        $imagePath = uploadImage($image, 'restaurant_images');
        $sql = "UPDATE restaurant SET image_path = ? WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$imagePath, $restaurantId]);
    }

    return $result;
}

function getRestaurantById($pdo, $restaurantId) {
    $sql = "SELECT * FROM restaurant WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$restaurantId]);
    return $stmt->fetch();
}

function updateRestaurant($pdo, $restaurantId, $name, $description, $image = null) {
    $sql = "UPDATE restaurant SET name = ?, description = ? WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute([$name, $description, $restaurantId]);

    if ($result && $image && $image['error'] === UPLOAD_ERR_OK) {
        $imagePath = uploadImage($image, 'restaurant_images');
        $sql = "UPDATE restaurant SET image_path = ? WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$imagePath, $restaurantId]);
    }

    return $result;
}

function deleteRestaurant($pdo, $restaurantId) {
    // Önce ilişkili yemekleri silelim
    $sql = "UPDATE food SET deleted_at = CURRENT_TIMESTAMP WHERE restaurant_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$restaurantId]);

    // Şimdi restoranı silelim
    $sql = "DELETE FROM restaurant WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([$restaurantId]);
}

function getFoodsByCompanyId($pdo, $companyId, $restaurantId = null, $search = '', $minPrice = null, $maxPrice = null) {
    $sql = "SELECT f.*, r.name as restaurant_name FROM food f
            JOIN restaurant r ON f.restaurant_id = r.id
            WHERE r.company_id = ? AND f.deleted_at IS NULL";
    $params = [$companyId];

    if ($restaurantId !== '') {
        $sql .= " AND r.id = ?";
        $params[] = $restaurantId;
    }

    if ($search) {
        $sql .= " AND f.name LIKE ?";
        $params[] = "%$search%";
    }

    if ($minPrice !== null && $minPrice !== '') {
        $sql .= " AND f.price >= ?";
        $params[] = $minPrice;
    }

    if ($maxPrice !== null && $maxPrice !== '') {
        $sql .= " AND f.price <= ?";
        $params[] = $maxPrice;
    }

    $sql .= " ORDER BY r.name, f.name";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $foods = $stmt->fetchAll();

    // İndirimli fiyatı hesapla
    foreach ($foods as &$food) {
        $food['discounted_price'] = $food['price'] * (1 - $food['discount'] / 100);
    }

    return $foods;
}

function addFood($pdo, $restaurantId, $name, $description, $price, $discount = 0, $image = null) {
    $sql = "INSERT INTO food (restaurant_id, name, description, price, discount) VALUES (?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute([$restaurantId, $name, $description, $price, $discount]);

    if ($result && $image && $image['error'] === UPLOAD_ERR_OK) {
        $foodId = $pdo->lastInsertId();
        $imagePath = uploadImage($image, 'food_images');
        $sql = "UPDATE food SET image_path = ? WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$imagePath, $foodId]);
    }

    return $result;
}

function getFoodById($pdo, $foodId) {
    $sql = "SELECT * FROM food WHERE id = ? AND deleted_at IS NULL";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$foodId]);
    return $stmt->fetch();
}

function isCompanyOwnFood($pdo, $companyId, $foodId) {
    $sql = "SELECT 1 FROM food f
            JOIN restaurant r ON f.restaurant_id = r.id
            WHERE f.id = ? AND r.company_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$foodId, $companyId]);
    return $stmt->fetchColumn() !== false;
}

function updateFood($pdo, $foodId, $restaurantId, $name, $description, $price, $discount = 0, $image = null) {
    $sql = "UPDATE food SET restaurant_id = ?, name = ?, description = ?, price = ?, discount = ? WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute([$restaurantId, $name, $description, $price, $discount, $foodId]);

    if ($result && $image && $image['error'] === UPLOAD_ERR_OK) {
        $imagePath = uploadImage($image, 'food_images');
        $sql = "UPDATE food SET image_path = ? WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$imagePath, $foodId]);
    }

    return $result;
}

function deleteFood($pdo, $foodId) {
    $sql = "UPDATE food SET deleted_at = CURRENT_TIMESTAMP WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([$foodId]);
}



function updateOrderStatus($pdo, $orderId, $newStatus, $companyId) {
    // Önce siparişin bu şirkete ait olduğunu kontrol et
    $sql = "SELECT 1 FROM `order` o
            JOIN order_items oi ON o.id = oi.order_id
            JOIN food f ON oi.food_id = f.id
            JOIN restaurant r ON f.restaurant_id = r.id
            WHERE o.id = ? AND r.company_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$orderId, $companyId]);
    if (!$stmt->fetchColumn()) {
        return false; // Sipariş bu şirkete ait değil
    }

    // Sipariş durumunu güncelle
    $sql = "UPDATE `order` SET order_status = ? WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([$newStatus, $orderId]);
}

function getOrderDetails($pdo, $orderId, $companyId) {
    $sql = "SELECT o.*, u.name as customer_name, r.name as restaurant_name
            FROM `order` o
            JOIN order_items oi ON o.id = oi.order_id
            JOIN food f ON oi.food_id = f.id
            JOIN restaurant r ON f.restaurant_id = r.id
            JOIN users u ON o.user_id = u.id
            WHERE o.id = ? AND r.company_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$orderId, $companyId]);
    $orderDetails = $stmt->fetch();

    if (!$orderDetails) {
        return null;
    }

    $sql = "SELECT oi.*, f.name as food_name
            FROM order_items oi
            JOIN food f ON oi.food_id = f.id
            WHERE oi.order_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$orderId]);
    $orderDetails['items'] = $stmt->fetchAll();

    return $orderDetails;
}

function getRecentOrdersByUserId($pdo, $userId, $limit = 5) {
    $sql = "SELECT o.id, o.user_id, o.order_status, o.total_price, o.created_at,
                   r.name as restaurant_name
            FROM `order` o
            JOIN order_items oi ON o.id = oi.order_id
            JOIN food f ON oi.food_id = f.id
            JOIN restaurant r ON f.restaurant_id = r.id
            WHERE o.user_id = ?
            GROUP BY o.id, o.user_id, o.order_status, o.total_price, o.created_at, r.name
            ORDER BY o.created_at DESC
            LIMIT ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$userId, $limit]);
    return $stmt->fetchAll();
}

function getPopularRestaurants($pdo, $limit = 5) {
    $sql = "SELECT r.id, r.name, r.description, r.image_path, COUNT(DISTINCT o.id) as order_count
            FROM restaurant r
            JOIN food f ON r.id = f.restaurant_id
            JOIN order_items oi ON f.id = oi.food_id
            JOIN `order` o ON oi.order_id = o.id
            GROUP BY r.id, r.name, r.description, r.image_path
            ORDER BY order_count DESC
            LIMIT ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$limit]);
    return $stmt->fetchAll();
}

function searchRestaurants($pdo, $search = '') {
    $sql = "SELECT r.*, AVG(c.score) as average_score
            FROM restaurant r
            LEFT JOIN comments c ON r.id = c.restaurant_id
            WHERE r.name LIKE ?
            GROUP BY r.id, r.name, r.description, r.image_path
            ORDER BY average_score DESC, r.name";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(["%$search%"]);
    return $stmt->fetchAll();
}



function getRestaurantMenu($pdo, $restaurantId) {
    $sql = "SELECT * FROM food WHERE restaurant_id = ? AND deleted_at IS NULL ORDER BY name";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$restaurantId]);
    return $stmt->fetchAll();
}

function getRestaurantCoupons($pdo, $restaurantId) {
    $sql = "SELECT * FROM coupon WHERE restaurant_id = ? OR restaurant_id IS NULL";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$restaurantId]);
    return $stmt->fetchAll();
}

function addToCart($pdo, $userId, $foodId, $quantity, $note) {
    $sql = "INSERT INTO basket (user_id, food_id, quantity, note) VALUES (?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([$userId, $foodId, $quantity, $note]);
}

function getCartItems($pdo, $userId) {
    $sql = "SELECT b.id, b.quantity, b.note, f.id as food_id, f.name as food_name, f.price, f.discount, r.id as restaurant_id, r.name as restaurant_name
            FROM basket b
            JOIN food f ON b.food_id = f.id
            JOIN restaurant r ON f.restaurant_id = r.id
            WHERE b.user_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$userId]);
    $cartItems = $stmt->fetchAll();

    // İndirimli fiyatı hesapla
    foreach ($cartItems as &$item) {
        $item['discounted_price'] = $item['price'] * (1 - $item['discount'] / 100);
    }

    return $cartItems;
}

function calculateCartTotal($cartItems, $discount = 0) {
    $total = 0;
    foreach ($cartItems as $item) {
        $total += $item['price'] * $item['quantity'] * (1 - $item['discount'] / 100);
    }
    return $total * (1 - $discount / 100);
}

function updateCartItemQuantity($pdo, $basketId, $quantity) {
    $sql = "UPDATE basket SET quantity = ? WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([$quantity, $basketId]);
}

function updateCartItemNote($pdo, $basketId, $note) {
    $sql = "UPDATE basket SET note = ? WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([$note, $basketId]);
}

function removeFromCart($pdo, $basketId) {
    $sql = "DELETE FROM basket WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([$basketId]);
}

function createOrder($pdo, $userId, $total) {
    $sql = "INSERT INTO `order` (user_id, order_status, total_price) VALUES (?, 'Hazırlanıyor', ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$userId, $total]);
    return $pdo->lastInsertId();
}


function addOrderItem($pdo, $orderId, $foodId, $quantity, $price, $note) {
    $sql = "INSERT INTO order_items (order_id, food_id, quantity, price, note) VALUES (?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([$orderId, $foodId, $quantity, $price, $note]);
}

function updateUserBalance($pdo, $userId, $amount) {
    $sql = "UPDATE users SET balance = balance + ? WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([$amount, $userId]);
}

function clearCart($pdo, $userId) {
    $sql = "DELETE FROM basket WHERE user_id = ?";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([$userId]);
}

function getUserOrders($pdo, $userId) {
    $sql = "SELECT * FROM `order` WHERE user_id = ? ORDER BY created_at DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$userId]);
    $orders = $stmt->fetchAll();

    foreach ($orders as &$order) {
        $sql = "SELECT oi.*, f.name as food_name 
                FROM order_items oi 
                JOIN food f ON oi.food_id = f.id 
                WHERE oi.order_id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$order['id']]);
        $order['items'] = $stmt->fetchAll();
    }

    return $orders;
}

function updateUserProfile($pdo, $userId, $name, $surname, $username) {
    $sql = "UPDATE users SET name = ?, surname = ?, username = ? WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([$name, $surname, $username, $userId]);
}

function changeUserPassword($pdo, $userId, $currentPassword, $newPassword) {
    $user = getUserById($pdo, $userId);
    if (password_verify($currentPassword, $user['password'])) {
        $hashedPassword = password_hash($newPassword, PASSWORD_ARGON2ID);
        $sql = "UPDATE users SET password = ? WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([$hashedPassword, $userId]);
    }
    return false;
}

function addUserBalance($pdo, $userId, $amount) {
    $sql = "UPDATE users SET balance = balance + ? WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([$amount, $userId]);
}

function getCouponDiscount($pdo, $couponCode) {
    $sql = "SELECT discount FROM coupon WHERE name = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$couponCode]);
    $result = $stmt->fetch();
    return $result ? $result['discount'] : 0;
}

function isValidCoupon($pdo, $couponCode) {
    $sql = "SELECT 1 FROM coupon WHERE name = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$couponCode]);
    return $stmt->fetchColumn() !== false;
}

function searchFoods($pdo, $search = '', $minPrice = null, $maxPrice = null) {
    $sql = "SELECT f.*, r.name as restaurant_name 
            FROM food f
            JOIN restaurant r ON f.restaurant_id = r.id
            WHERE f.deleted_at IS NULL";
    $params = [];

    if ($search) {
        $sql .= " AND f.name LIKE ?";
        $params[] = "%$search%";
    }

    if ($minPrice !== null && $minPrice !== '') {
        $sql .= " AND f.price >= ?";
        $params[] = $minPrice;
    }

    if ($maxPrice !== null && $maxPrice !== '') {
        $sql .= " AND f.price <= ?";
        $params[] = $maxPrice;
    }

    $sql .= " ORDER BY r.name, f.name";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

function isCartFromDifferentRestaurant($pdo, $userId, $newFoodId) {
    $sql = "SELECT DISTINCT f.restaurant_id 
            FROM basket b
            JOIN food f ON b.food_id = f.id
            WHERE b.user_id = ?
            UNION
            SELECT restaurant_id FROM food WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$userId, $newFoodId]);
    $restaurantIds = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    return count($restaurantIds) > 1;
}

function getActiveOrdersByUserId($pdo, $userId) {
    $sql = "SELECT o.*, 
                   (SELECT COUNT(*) FROM order_items oi WHERE oi.order_id = o.id) as item_count,
                   (SELECT SUM(oi.price * oi.quantity) FROM order_items oi WHERE oi.order_id = o.id) as total_price
            FROM `order` o
            WHERE o.user_id = ? AND o.order_status != 'Teslim Edildi'
            ORDER BY o.created_at DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$userId]);
    $orders = $stmt->fetchAll();

    foreach ($orders as &$order) {
        $sql = "SELECT oi.*, f.name as food_name
                FROM order_items oi
                JOIN food f ON oi.food_id = f.id
                WHERE oi.order_id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$order['id']]);
        $order['items'] = $stmt->fetchAll();
    }

    return $orders;
}

function getPastOrdersByUserId($pdo, $userId) {
    $sql = "SELECT o.*, 
                   (SELECT COUNT(*) FROM order_items oi WHERE oi.order_id = o.id) as item_count,
                   (SELECT SUM(oi.price * oi.quantity) FROM order_items oi WHERE oi.order_id = o.id) as total_price
            FROM `order` o
            WHERE o.user_id = ? AND o.order_status = 'Teslim Edildi'
            ORDER BY o.created_at DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$userId]);
    $orders = $stmt->fetchAll();

    foreach ($orders as &$order) {
        $sql = "SELECT oi.*, f.name as food_name
                FROM order_items oi
                JOIN food f ON oi.food_id = f.id
                WHERE oi.order_id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$order['id']]);
        $order['items'] = $stmt->fetchAll();
    }

    return $orders;
}
function addComment($pdo, $userId, $restaurantId, $title, $description, $score) {
    $sql = "INSERT INTO comments (user_id, restaurant_id, title, description, score) VALUES (?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([$userId, $restaurantId, $title, $description, $score]);
}
function getAverageScore($pdo, $restaurantId) {
    $sql = "SELECT AVG(score) as average_score FROM comments WHERE restaurant_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$restaurantId]);
    $result = $stmt->fetch();
    return $result ? $result['average_score'] : 0;
}

function getRestaurantComments($pdo, $restaurantId) {
    $sql = "SELECT c.*, u.name as user_name, u.surname as user_surname
            FROM comments c
            JOIN users u ON c.user_id = u.id
            WHERE c.restaurant_id = ?
            ORDER BY c.created_at DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$restaurantId]);
    return $stmt->fetchAll();
}

function getAllRestaurantComments($pdo) {
    $sql = "SELECT c.*, r.name as restaurant_name, u.name as user_name, u.surname as user_surname
            FROM comments c
            JOIN restaurant r ON c.restaurant_id = r.id
            JOIN users u ON c.user_id = u.id
            ORDER BY c.created_at DESC";
    $stmt = $pdo->query($sql);
    return $stmt->fetchAll();
}

function getCouponsByCompanyId($pdo, $companyId) {
    $sql = "SELECT c.*, r.name as restaurant_name 
            FROM coupon c
            LEFT JOIN restaurant r ON c.restaurant_id = r.id
            WHERE r.company_id = ? OR c.restaurant_id IS NULL
            ORDER BY c.name";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$companyId]);
    return $stmt->fetchAll();
}

function getCouponByCode($pdo, $couponCode) {
    $sql = "SELECT * FROM coupon WHERE name = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$couponCode]);
    return $stmt->fetch();
}

function getFoodsByCompanyIdAdmin($pdo, $companyId) {
    $sql = "SELECT f.*, r.name as restaurant_name 
            FROM food f
            JOIN restaurant r ON f.restaurant_id = r.id
            WHERE r.company_id = ? AND f.deleted_at IS NULL
            ORDER BY r.name, f.name";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$companyId]);
    return $stmt->fetchAll();
}

function getCompanyDetailsAdmin($pdo, $companyId) {
    $sql = "SELECT c.*, u.name as owner_name, u.surname as owner_surname
            FROM company c
            JOIN users u ON c.id = u.company_id
            WHERE c.id = ? AND u.role = 'company'";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$companyId]);
    return $stmt->fetch();
}

function deleteComment($pdo, $commentId) {
    $sql = "DELETE FROM comments WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([$commentId]);
}

function getAllComments($pdo) {
    $sql = "SELECT c.*, u.name as user_name, u.surname as user_surname, r.name as restaurant_name
            FROM comments c
            JOIN users u ON c.user_id = u.id
            JOIN restaurant r ON c.restaurant_id = r.id
            ORDER BY c.created_at DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll();
}

function softDeleteFood($pdo, $foodId) {
    $sql = "UPDATE food SET deleted_at = NOW() WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([$foodId]);
}

function getFoodsByCompanyIdWithDeleted($pdo, $companyId, $restaurantId = null, $search = '', $minPrice = null, $maxPrice = null) {
    $sql = "SELECT f.*, r.name as restaurant_name FROM food f
            JOIN restaurant r ON f.restaurant_id = r.id
            WHERE r.company_id = ?";
    $params = [$companyId];

    if ($restaurantId !== '') {
        $sql .= " AND r.id = ?";
        $params[] = $restaurantId;
    }

    if ($search) {
        $sql .= " AND f.name LIKE ?";
        $params[] = "%$search%";
    }

    if ($minPrice !== null && $minPrice !== '') {
        $sql .= " AND f.price >= ?";
        $params[] = $minPrice;
    }

    if ($maxPrice !== null && $maxPrice !== '') {
        $sql .= " AND f.price <= ?";
        $params[] = $maxPrice;
    }

    $sql .= " ORDER BY r.name, f.name";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}


function uploadProfilePhoto($pdo, $file, $userId) {
    $targetDir = "/var/www/html/uploads/profile_pictures/";
    if (!file_exists($targetDir)) {
        mkdir($targetDir, 0755, true);
    }
    $fileName = uniqid() . "_" . basename($file["name"]);
    $targetFile = $targetDir . $fileName;
    
    if (move_uploaded_file($file["tmp_name"], $targetFile)) {
        $photoPath = "/uploads/profile_pictures/" . $fileName;
        saveProfilePhoto($pdo, $userId, $photoPath);
        return $photoPath;
    } else {
        throw new Exception("Dosya yüklenirken bir hata oluştu: " . error_get_last()['message']);
    }
}

function saveProfilePhoto($pdo, $userId, $photoPath) {
    $sql = "INSERT INTO user_profile_photos (user_id, photo_path) VALUES (?, ?)";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([$userId, $photoPath]);
}

function getProfilePhoto($pdo, $userId) {
    $sql = "SELECT photo_path FROM user_profile_photos WHERE user_id = ? ORDER BY created_at DESC LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$userId]);
    return $stmt->fetchColumn();
}