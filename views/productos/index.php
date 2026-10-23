<?php
declare(strict_types=1);
?>
<div class="page-heading">
    <h1>Productos</h1>
    <a class="button" href="<?= $e($url('/productos/crear')) ?>">Registrar producto</a>
</div>
<div class="table-wrap">
    <table>
        <thead>
        <tr><th>Código</th><th>Nombre</th><th>Categoría</th><th>Precio</th><th>Stock actual</th><th>Stock mínimo</th></tr>
        </thead>
        <tbody>
        <?php if ($productos === []): ?>
            <tr><td colspan="6">Aún no hay productos registrados.</td></tr>
        <?php endif; ?>
        <?php foreach ($productos as $producto): ?>
            <?php $alerta = \App\Services\VentaService::debeResaltar($producto->stockActual, $producto->stockMinimo); ?>
            <tr class="<?= $alerta ? 'stock-alert' : '' ?>">
                <td><?= $e($producto->codigo) ?></td>
                <td><?= $e($producto->nombre) ?></td>
                <td><?= $e($producto->categoria ?? '—') ?></td>
                <td>S/ <?= $e(\App\Models\Producto::centsToDecimal($producto->precioVentaCentavos)) ?></td>
                <td><?= $e($producto->stockActual) ?><?= $alerta ? ' · Reabastecer' : '' ?></td>
                <td><?= $e($producto->stockMinimo) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
