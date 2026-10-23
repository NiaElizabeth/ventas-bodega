<?php
declare(strict_types=1);
$datos = $datos ?? [];
$errores = $errores ?? [];
?>
<h1>Registrar venta</h1>
<?php if ($errores !== []): ?>
    <div class="errors" role="alert"><ul><?php foreach ($errores as $error): ?><li><?= $e($error) ?></li><?php endforeach; ?></ul></div>
<?php endif; ?>
<?php if ($productos === []): ?>
    <p>No hay productos registrados. Registre un producto antes de vender.</p>
<?php else: ?>
    <form method="post" action="<?= $e($url('/ventas/guardar')) ?>">
        <input type="hidden" name="_csrf" value="<?= $e($token) ?>">
        <label>Producto
            <select name="producto_id" required>
                <option value="">Seleccione un producto</option>
                <?php foreach ($productos as $producto): ?>
                    <option value="<?= $e($producto->id) ?>" <?= (string) ($datos['producto_id'] ?? '') === (string) $producto->id ? 'selected' : '' ?>>
                        <?= $e($producto->codigo) ?> — <?= $e($producto->nombre) ?> (stock: <?= $e($producto->stockActual) ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </label>
        <label>Cantidad <input type="number" name="cantidad" min="1" step="1" required value="<?= $e($datos['cantidad'] ?? '1') ?>"></label>
        <button type="submit">Registrar venta</button>
    </form>
<?php endif; ?>
