<?php
declare(strict_types=1);
?>
<h1>Sistema de ventas e inventario</h1>
<p>Administra el catálogo, registra ventas y consulta el resultado del día.</p>
<div class="actions">
    <a class="button" href="<?= $e($url('/productos')) ?>">Ver productos</a>
    <a class="button" href="<?= $e($url('/productos/crear')) ?>">Registrar producto</a>
    <a class="button" href="<?= $e($url('/ventas/crear')) ?>">Registrar venta</a>
    <a class="button" href="<?= $e($url('/ventas')) ?>">Consultar ventas del día</a>
</div>
