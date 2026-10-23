<?php
declare(strict_types=1);
?>
<h1>Ventas del día</h1>
<p class="total">Total vendido: S/ <?= $e(\App\Models\Producto::centsToDecimal($totalCentavos)) ?></p>
<div class="table-wrap">
    <table>
        <thead>
        <tr><th>Fecha y hora</th><th>Producto</th><th>Cantidad</th><th>Precio unitario</th><th>Total</th></tr>
        </thead>
        <tbody>
        <?php if ($ventas === []): ?>
            <tr><td colspan="5">No hay ventas registradas hoy.</td></tr>
        <?php endif; ?>
        <?php foreach ($ventas as $venta): ?>
            <tr>
                <td><?= $e($venta->fechaHora) ?></td>
                <td><?= $e($venta->productoNombre) ?></td>
                <td><?= $e($venta->cantidad) ?></td>
                <td>S/ <?= $e(\App\Models\Producto::centsToDecimal($venta->precioUnitarioCentavos)) ?></td>
                <td>S/ <?= $e(\App\Models\Producto::centsToDecimal($venta->totalCentavos)) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<a class="button" href="<?= $e($url('/ventas/crear')) ?>">Registrar venta</a>
