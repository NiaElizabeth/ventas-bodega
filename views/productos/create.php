<?php
declare(strict_types=1);
$datos = $datos ?? [];
$errores = $errores ?? [];
?>
<h1>Registrar producto</h1>
<?php if ($errores !== []): ?>
    <div class="errors" role="alert"><ul><?php foreach ($errores as $error): ?><li><?= $e($error) ?></li><?php endforeach; ?></ul></div>
<?php endif; ?>
<form method="post" action="<?= $e($url('/productos/guardar')) ?>">
    <input type="hidden" name="_csrf" value="<?= $e($token) ?>">
    <label>Código <input name="codigo" maxlength="64" required value="<?= $e($datos['codigo'] ?? '') ?>"></label>
    <label>Nombre <input name="nombre" maxlength="150" required value="<?= $e($datos['nombre'] ?? '') ?>"></label>
    <label>Categoría <input name="categoria" maxlength="80" value="<?= $e($datos['categoria'] ?? '') ?>"></label>
    <label>Precio de venta <input type="number" name="precio_venta" min="0" max="99999999.99" step="0.01" required value="<?= $e($datos['precio_venta'] ?? '') ?>"></label>
    <label>Stock actual <input type="number" name="stock_actual" min="0" step="1" required value="<?= $e($datos['stock_actual'] ?? '0') ?>"></label>
    <label>Stock mínimo <input type="number" name="stock_minimo" min="0" step="1" required value="<?= $e($datos['stock_minimo'] ?? '0') ?>"></label>
    <button type="submit">Guardar producto</button>
</form>
