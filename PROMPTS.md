Actúa como analista de sistemas. Analiza el caso del Minimarket
"La Esquina" y genera el archivo D0.md en la raíz del proyecto.

CONTEXTO:
El minimarket registra ventas manualmente en un cuaderno y
controla el inventario observando los estantes. Esto ocasiona
desconocimiento de las ventas diarias y falta de productos.

El sistema deberá:
RF1. Registrar productos con código, nombre, categoría,
precio de venta, stock actual y stock mínimo.
RF2. Listar productos y resaltar los que tengan stock
menor o igual al mínimo.
RF3. Registrar ventas de un producto, tomando su precio
vigente, calculando el total, registrando fecha y hora,
y descontando el stock.
RF4. Listar las ventas del día y calcular el total vendido.

INSTRUCCIONES:
1. Describe el problema central en una frase.
2. Identifica los usuarios, sus responsabilidades
y el proceso actual con sus dificultades.
3. Documenta RF1 a RF4 y los requisitos no funcionales
RNF1 en adelante, incluyendo seguridad e integridad.
4. Identifica las cinco reglas de negocio:
   - Cantidad vendida mayor que cero.
   - Prohibido vender más unidades que el stock.
   - Precio de venta no negativo.
   - Código de producto único.
   - Venta y descuento de stock atómicos.
5. Identifica lo que queda fuera del alcance:
   login, roles, facturación electrónica, clientes,
   agentes conversacionales y despliegue público.
6. Marca cualquier supuesto como SUPUESTO.

No agregues funcionalidades ajenas al caso.
No generes código PHP ni archivos de diseño todavía.
Presenta el documento para revisión.