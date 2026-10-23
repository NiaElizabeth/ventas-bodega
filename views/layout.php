<?php
declare(strict_types=1);
$flashMessage = $_SESSION['flash_message'] ?? null;
unset($_SESSION['flash_message']);
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Minimarket La Esquina</title>
    <link rel="stylesheet" href="css/app.css">
</head>
<body>
    <header class="site-header">
        <a href="<?= $e($url('/')) ?>">Minimarket La Esquina</a>
        <nav>
            <a href="<?= $e($url('/productos')) ?>">Productos</a>
            <a href="<?= $e($url('/ventas')) ?>">Ventas del día</a>
            <a href="<?= $e($url('/ventas/crear')) ?>">Registrar venta</a>
        </nav>
    </header>
    <main class="container">
        <?php if (is_string($flashMessage)): ?>
            <p class="notice" role="status"><?= $e($flashMessage) ?></p>
        <?php endif; ?>
        <?= $content ?>
    </main>
</body>
</html>
