<?php
declare(strict_types=1);

require dirname(__DIR__) . '/app/autoload.php';

use App\Models\Venta;
use App\Core\ViewRenderer;
use App\Services\ProductoService;
use App\Services\VentaService;

$tests = 0;
$failures = [];

$assert = static function (bool $condition, string $message) use (&$tests, &$failures): void {
    $tests++;
    if (!$condition) {
        $failures[] = $message;
    }
};

$assert(ProductoService::precioValido('0'), 'Cero debe ser precio válido.');
$assert(ProductoService::precioValido('12.50'), 'Precio con centavos debe ser válido.');
$assert(ProductoService::precioValido('99999999.99'), 'Precio máximo de DECIMAL(10,2) debe aceptarse.');
$assert(!ProductoService::precioValido('-1'), 'Precio negativo debe rechazarse.');
$assert(!ProductoService::precioValido('12.345'), 'Precio con más de dos decimales debe rechazarse.');
$assert(ProductoService::enteroNoNegativo('0'), 'Stock cero debe ser válido.');
$assert(!ProductoService::enteroNoNegativo('-1'), 'Stock negativo debe rechazarse.');
$assert(!ProductoService::enteroNoNegativo('1.5'), 'Stock decimal debe rechazarse.');
$assert(ProductoService::validar([
    'codigo' => 'P001',
    'nombre' => 'Arroz',
    'precio_venta' => '5.25',
    'stock_actual' => '10',
    'stock_minimo' => '2',
]) === [], 'Producto válido debe pasar validación.');
$assert(ProductoService::validar([
    'codigo' => ' ',
    'nombre' => '',
    'precio_venta' => 'abc',
    'stock_actual' => '-1',
    'stock_minimo' => '2.2',
]) !== [], 'Producto inválido debe producir errores.');
$assert(ProductoService::validar([
    'codigo' => ['malformado'],
    'nombre' => 'Producto',
    'categoria' => ['malformado'],
    'precio_venta' => '1.00',
    'stock_actual' => '1',
    'stock_minimo' => '0',
]) !== [], 'Campos enviados como arreglos deben rechazarse sin excepciones.');
$assert(ProductoService::precioACentavos('12.50') === 1250, 'El precio debe convertirse a centavos sin coma flotante.');
$assert(VentaService::cantidadValida('1'), 'Cantidad positiva entera debe aceptarse.');
$assert(!VentaService::cantidadValida('0'), 'Cantidad cero debe rechazarse.');
$assert(!VentaService::cantidadValida('1.5'), 'Cantidad decimal debe rechazarse.');
$assert(!VentaService::cantidadValida('abc'), 'Cantidad no numérica debe rechazarse.');
$assert(VentaService::hayStockSuficiente(4, 4), 'Venta igual al stock debe ser válida.');
$assert(!VentaService::hayStockSuficiente(5, 4), 'Venta mayor al stock debe rechazarse.');
$assert(VentaService::debeResaltar(3, 3), 'Stock igual al mínimo debe alertar.');
$assert(!VentaService::debeResaltar(4, 3), 'Stock sobre el mínimo no debe alertar.');
$assert(VentaService::calcularTotal(125, 3) === 375, 'Total debe calcularse en centavos exactos.');
$assert(VentaService::calcularTotal(9999999999, 1) === 9999999999, 'Total máximo de DECIMAL(10,2) debe aceptarse.');
$totalFueraDeRango = false;
try {
    VentaService::calcularTotal(9999999999, 2);
} catch (InvalidArgumentException) {
    $totalFueraDeRango = true;
}
$assert($totalFueraDeRango, 'Total fuera del rango de DECIMAL(10,2) debe rechazarse.');
$assert(VentaService::totalVendido([]) === 0, 'Lista vacía debe sumar cero.');
$sampleSales = [
    new Venta(1, 1, 'A', 2, 120, 240, '2026-10-23 10:00:00'),
    new Venta(2, 2, 'B', 1, 355, 355, '2026-10-23 10:01:00'),
];
$assert(VentaService::totalVendido($sampleSales) === 595, 'Total del día debe sumar totales en centavos.');
$assert(VentaService::totalVendido(array_reverse($sampleSales)) === 595, 'El total debe ser independiente del orden.');

for ($iteration = 0; $iteration < 100; $iteration++) {
    $priceCents = $iteration * 173;
    $quantity = $iteration + 1;
    $assert(
        VentaService::calcularTotal($priceCents, $quantity) === $priceCents * $quantity,
        'El total debe coincidir con precio por cantidad en centavos.'
    );
    $stock = ($iteration * 2) + 1;
    $assert(
        VentaService::hayStockSuficiente($stock, $stock) && !VentaService::hayStockSuficiente($stock + 1, $stock),
        'El stock debe aceptar cantidades disponibles y rechazar las que excedan.'
    );
    $assert(
        VentaService::debeResaltar($iteration, $iteration) && !VentaService::debeResaltar($iteration + 1, $iteration),
        'La alerta debe activarse exactamente cuando stock actual no supera al mínimo.'
    );
}

$_SESSION = [];
ob_start();
(new ViewRenderer(dirname(__DIR__) . '/views'))->render('ventas/index', [
    'ventas' => [new Venta(1, 1, '<script>alert(1)</script>', 1, 100, 100, '2026-10-23 10:00:00')],
    'totalCentavos' => 100,
]);
$renderedHtml = (string) ob_get_clean();
$assert(
    !str_contains($renderedHtml, '<script>alert(1)</script>')
        && str_contains($renderedHtml, '&lt;script&gt;alert(1)&lt;/script&gt;'),
    'La vista debe escapar texto de producto para prevenir XSS.'
);

if ($failures !== []) {
    fwrite(STDERR, "Fallaron " . count($failures) . " de {$tests} pruebas:\n");
    foreach ($failures as $failure) {
        fwrite(STDERR, "- {$failure}\n");
    }
    exit(1);
}

echo "OK: {$tests} pruebas nativas pasaron.\n";
