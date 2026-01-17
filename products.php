<?php
require_once 'includes/auth.php';
require_once 'config/database.php';

requireAdmin();

$database = new Database();
$db = $database->getConnection();

$success = '';
$error = '';

if (isset($_GET['success'])) {
    switch ($_GET['success']) {
        case 'product_added':
            $success = 'Producto agregado correctamente.';
            break;
        case 'product_updated':
            $success = 'Producto actualizado correctamente.';
            break;
        case 'product_deleted':
            $success = 'Producto eliminado correctamente.';
            break;
    }
}

// Agregar producto
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_product'])) {
    if (!validateCSRFToken($_POST['csrf_token'])) {
        $error = 'Token de seguridad inválido.';
    } else {
        $name = sanitizeInput($_POST['name']);
        $description = sanitizeInput($_POST['description']);
        $price = floatval($_POST['price']);
        $category = sanitizeInput($_POST['category']);
        $size = sanitizeInput($_POST['size']);
        $color = sanitizeInput($_POST['color']);
        $stock = intval($_POST['stock']);
        $image_url = sanitizeInput($_POST['image_url']);

        if (empty($name) || empty($price) || empty($category)) {
            $error = 'Nombre, precio y categoría son obligatorios.';
        } else {
            $query = "INSERT INTO products (name, description, price, category, size, color, stock, image_url) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $db->prepare($query);
            
            if ($stmt->execute([$name, $description, $price, $category, $size, $color, $stock, $image_url])) {
                header('Location: products.php?success=product_added');
                exit();
            } else {
                $error = 'Error al agregar el producto.';
            }
        }
    }
}

// Actualizar producto
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_product'])) {
    if (!validateCSRFToken($_POST['csrf_token'])) {
        $error = 'Token de seguridad inválido.';
    } else {
        $id = intval($_POST['product_id']);
        $name = sanitizeInput($_POST['name']);
        $description = sanitizeInput($_POST['description']);
        $price = floatval($_POST['price']);
        $category = sanitizeInput($_POST['category']);
        $size = sanitizeInput($_POST['size']);
        $color = sanitizeInput($_POST['color']);
        $stock = intval($_POST['stock']);
        $image_url = sanitizeInput($_POST['image_url']);

        if (empty($name) || empty($price) || empty($category)) {
            $error = 'Nombre, precio y categoría son obligatorios.';
        } else {
            $query = "UPDATE products SET name = ?, description = ?, price = ?, category = ?, size = ?, color = ?, stock = ?, image_url = ? WHERE id = ?";
            $stmt = $db->prepare($query);
            
            if ($stmt->execute([$name, $description, $price, $category, $size, $color, $stock, $image_url, $id])) {
                header('Location: products.php?success=product_updated');
                exit();
            } else {
                $error = 'Error al actualizar el producto.';
            }
        }
    }
}

// Eliminar producto
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $query = "DELETE FROM products WHERE id = ?";
    $stmt = $db->prepare($query);
    
    if ($stmt->execute([$id])) {
        header('Location: products.php?success=product_deleted');
        exit();
    } else {
        $error = 'Error al eliminar el producto.';
    }
}

// Obtener todos los productos
$query = "SELECT * FROM products ORDER BY created_at DESC";
$stmt = $db->prepare($query);
$stmt->execute();
$products = $stmt->fetchAll();

// Obtener producto para editar
$edit_product = null;
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $id = intval($_GET['edit']);
    $query = "SELECT * FROM products WHERE id = ?";
    $stmt = $db->prepare($query);
    $stmt->execute([$id]);
    $edit_product = $stmt->fetch();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestionar Productos - Pink Fashion Store</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header class="header">
        <nav class="navbar">
            <div class="nav-container">
                <div class="nav-logo">
                    <h1><a href="index.php">Pink Fashion</a></h1>
                </div>
                <ul class="nav-menu">
                    <li><a href="index.php">Inicio</a></li>
                    <li><a href="dashboard.php">Mi Cuenta</a></li>
                    <li><a href="products.php" class="active">Gestionar Productos</a></li>
                    <li><a href="logout.php">Cerrar Sesión</a></li>
                </ul>
                <div class="hamburger">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            </div>
        </nav>
    </header>

    <main class="products-admin-main">
        <div class="container">
            <div class="admin-header">
                <h1>Gestión de Productos</h1>
                <button class="btn-primary" onclick="toggleProductForm()">
                    <?php echo $edit_product ? 'Cancelar Edición' : 'Agregar Producto'; ?>
                </button>
            </div>

            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>

            <div id="productForm" class="product-form-container <?php echo $edit_product ? 'active' : ''; ?>">
                <h2><?php echo $edit_product ? 'Editar Producto' : 'Agregar Nuevo Producto'; ?></h2>
                <form method="POST" class="product-form">
                    <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                    <?php if ($edit_product): ?>
                        <input type="hidden" name="update_product" value="1">
                        <input type="hidden" name="product_id" value="<?php echo $edit_product['id']; ?>">
                    <?php else: ?>
                        <input type="hidden" name="add_product" value="1">
                    <?php endif; ?>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="name">Nombre del Producto</label>
                            <input type="text" id="name" name="name" 
                                   value="<?php echo $edit_product ? htmlspecialchars($edit_product['name']) : ''; ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="category">Categoría</label>
                            <select id="category" name="category" required>
                                <option value="">Seleccionar categoría</option>
                                <option value="vestidos" <?php echo ($edit_product && $edit_product['category'] === 'vestidos') ? 'selected' : ''; ?>>Vestidos</option>
                                <option value="blusas" <?php echo ($edit_product && $edit_product['category'] === 'blusas') ? 'selected' : ''; ?>>Blusas</option>
                                <option value="faldas" <?php echo ($edit_product && $edit_product['category'] === 'faldas') ? 'selected' : ''; ?>>Faldas</option>
                                <option value="chaquetas" <?php echo ($edit_product && $edit_product['category'] === 'chaquetas') ? 'selected' : ''; ?>>Chaquetas</option>
                                <option value="accesorios" <?php echo ($edit_product && $edit_product['category'] === 'accesorios') ? 'selected' : ''; ?>>Accesorios</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="description">Descripción</label>
                        <textarea id="description" name="description" rows="3"><?php echo $edit_product ? htmlspecialchars($edit_product['description']) : ''; ?></textarea>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="price">Precio</label>
                            <input type="number" id="price" name="price" step="0.01" min="0" 
                                   value="<?php echo $edit_product ? $edit_product['price'] : ''; ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="stock">Stock</label>
                            <input type="number" id="stock" name="stock" min="0" 
                                   value="<?php echo $edit_product ? $edit_product['stock'] : ''; ?>">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="size">Talla</label>
                            <select id="size" name="size">
                                <option value="">Seleccionar talla</option>
                                <option value="XS" <?php echo ($edit_product && $edit_product['size'] === 'XS') ? 'selected' : ''; ?>>XS</option>
                                <option value="S" <?php echo ($edit_product && $edit_product['size'] === 'S') ? 'selected' : ''; ?>>S</option>
                                <option value="M" <?php echo ($edit_product && $edit_product['size'] === 'M') ? 'selected' : ''; ?>>M</option>
                                <option value="L" <?php echo ($edit_product && $edit_product['size'] === 'L') ? 'selected' : ''; ?>>L</option>
                                <option value="XL" <?php echo ($edit_product && $edit_product['size'] === 'XL') ? 'selected' : ''; ?>>XL</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="color">Color</label>
                            <input type="text" id="color" name="color" 
                                   value="<?php echo $edit_product ? htmlspecialchars($edit_product['color']) : ''; ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="image_url">URL de la Imagen</label>
                        <input type="url" id="image_url" name="image_url" 
                               value="<?php echo $edit_product ? htmlspecialchars($edit_product['image_url']) : '/placeholder.svg?height=300&width=250'; ?>">
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn-primary">
                            <?php echo $edit_product ? 'Actualizar Producto' : 'Agregar Producto'; ?>
                        </button>
                        <?php if ($edit_product): ?>
                            <a href="products.php" class="btn-secondary">Cancelar</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>

            <div class="products-table-container">
                <h2>Lista de Productos</h2>
                <div class="table-responsive">
                    <table class="products-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Imagen</th>
                                <th>Nombre</th>
                                <th>Categoría</th>
                                <th>Precio</th>
                                <th>Stock</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($products as $product): ?>
                                <tr>
                                    <td><?php echo $product['id']; ?></td>
                                    <td>
                                        <img src="<?php echo htmlspecialchars($product['image_url']); ?>" 
                                             alt="<?php echo htmlspecialchars($product['name']); ?>" 
                                             class="product-thumbnail">
                                    </td>
                                    <td><?php echo htmlspecialchars($product['name']); ?></td>
                                    <td><?php echo htmlspecialchars($product['category']); ?></td>
                                    <td>$<?php echo number_format($product['price'], 2); ?></td>
                                    <td><?php echo $product['stock']; ?></td>
                                    <td class="actions">
                                        <a href="products.php?edit=<?php echo $product['id']; ?>" class="btn-edit">Editar</a>
                                        <a href="products.php?delete=<?php echo $product['id']; ?>" 
                                           class="btn-delete" 
                                           onclick="return confirm('¿Estás seguro de que quieres eliminar este producto?')">Eliminar</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <script src="js/script.js"></script>
</body>
</html>
