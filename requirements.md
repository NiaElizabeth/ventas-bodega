# Requirements Document

## Introduction

El Sistema de ventas e inventario para el minimarket "La Esquina" permite registrar productos en un catálogo, registrar las ventas de productos con descuento automático del stock, listar los productos con alertas visuales de stock mínimo y consultar las ventas del día con el total vendido. El Sistema está diseñado para uso local en el punto de venta, sin autenticación de usuarios, y persiste los datos en MySQL/MariaDB sobre XAMPP. El alcance excluye login, roles y permisos, facturación electrónica, gestión de clientes y acceso remoto.

## Glossary

- **Sistema**: Aplicación de ventas e inventario para el minimarket "La Esquina".
- **Administrador**: Usuario responsable de registrar productos, consultar el listado de productos e identificar alertas de stock mínimo.
- **Vendedor**: Usuario responsable de registrar ventas y consultar las ventas del día y el total vendido.
- **Producto**: Artículo del catálogo identificado por código, nombre, categoría, precio de venta, stock actual y stock mínimo.
- **Código**: Identificador único de un Producto dentro del catálogo.
- **Nombre**: Descripción del Producto.
- **Categoría**: Clasificación del Producto.
- **Precio de venta**: Monto unitario vigente de un Producto.
- **Stock actual**: Cantidad de unidades disponibles de un Producto.
- **Stock mínimo**: Umbral que activa la alerta de reabastecimiento de un Producto.
- **Alerta de stock**: Resaltado visual de los Productos cuyo stock actual es menor o igual al stock mínimo.
- **Venta**: Registro de la venta de un Producto con cantidad, precio unitario, total, fecha y hora.
- **Cantidad vendida**: Número de unidades vendidas en una Venta.
- **Total vendido**: Suma de los totales de todas las Ventas del día actual.
- **Día actual**: Día calendario natural desde 00:00 a 23:59.
- **Base de datos**: Motor MySQL/MariaDB disponible en el entorno XAMPP donde se persisten Productos y Ventas.

## Requirements

### Requirement 1: RF1 — Registrar productos

**User Story:** Como Administrador, quiero registrar productos con sus datos y precios, para mantener un catálogo de los artículos del minimarket.

#### Acceptance Criteria

1. CUANDO el Administrador registra un nuevo Producto, EL SISTEMA DEBERÁ almacenar el Producto con código, nombre, categoría, precio de venta, stock actual y stock mínimo.
2. SI el Administrador intenta registrar un Producto con un Código ya existente en el catálogo, ENTONCES EL SISTEMA DEBERÁ rechazar el registro e informar al Administrador del código duplicado. *(RN4)*
3. CUANDO el Administrador registra un Producto, EL SISTEMA DEBERÁ validar que el precio de venta sea un valor numérico mayor o igual a cero. *(RN3)*
4. SI el Administrador ingresa un Código vacío o un valor no numérico para el precio de venta, ENTONCES EL SISTEMA DEBERÁ rechazar el registro con un mensaje de error comprensible.

### Requirement 2: RF2 — Listar productos y resaltar alertas de stock

**User Story:** Como Administrador, quiero listar los Productos y ver resaltadas las alertas de stock mínimo, para identificar los Productos que requieren reabastecimiento.

#### Acceptance Criteria

1. CUANDO el Administrador consulta el listado de Productos, EL SISTEMA DEBERÁ mostrar todos los Productos registrados con código, nombre, categoría, precio de venta, stock actual y stock mínimo.
2. MIENTRAS un Producto tenga stock actual menor o igual al stock mínimo, EL SISTEMA DEBERÁ resaltar el Producto visualmente en el listado para identificar la alerta de reabastecimiento.

### Requirement 3: RF3 — Registrar ventas de un producto

**User Story:** Como Vendedor, quiero registrar las ventas de Productos, para llevar un control confiable de las ventas y del stock disponible.

#### Acceptance Criteria

1. CUANDO el Vendedor registra una Venta, EL SISTEMA DEBERÁ permitir seleccionar un Producto por código o nombre e ingresar la cantidad vendida.
2. CUANDO el Vendedor registra una Venta, EL SISTEMA DEBERÁ tomar automáticamente el precio de venta vigente del Producto y calcular el total como precio de venta multiplicado por la cantidad vendida.
3. CUANDO el Vendedor registra una Venta, EL SISTEMA DEBERÁ registrar la fecha y hora de la Venta junto con Producto, cantidad, precio unitario y total.
4. CUANDO el Vendedor registra una Venta, EL SISTEMA DEBERÁ descontar la cantidad vendida del stock actual del Producto.
5. CUANDO el Vendedor registra una Venta, EL SISTEMA DEBERÁ validar que la cantidad vendida sea un valor numérico entero mayor que cero. *(RN1)*
6. SI el Vendedor intenta registrar una Venta cuya cantidad vendida supera el stock actual del Producto, ENTONCES EL SISTEMA DEBERÁ rechazar la operación e informar al Vendedor. *(RN2)*
7. SI el Vendedor intenta registrar una Venta de un Producto no registrado en el catálogo, ENTONCES EL SISTEMA DEBERÁ rechazar la operación e informar al Vendedor.

### Requirement 4: RF4 — Listar las ventas del día y calcular el total vendido

**User Story:** Como Vendedor, quiero consultar las Ventas del día y el total vendido, para conocer el resultado de las ventas en tiempo real sin sumar manualmente.

#### Acceptance Criteria

1. CUANDO el Vendedor consulta las Ventas del día, EL SISTEMA DEBERÁ listar las Ventas registradas en el día actual mostrando fecha y hora, Producto, cantidad, precio unitario y total.
2. CUANDO el Vendedor consulta las Ventas del día, EL SISTEMA DEBERÁ calcular y mostrar el total vendido como la suma de los totales de todas las Ventas del día actual.

### Requirement 5: RNF1 — Persistencia de datos

**User Story:** Como Administrador, quiero que los datos de Productos y Ventas se almacenen de forma persistente, para que no se pierdan ante un cierre o reinicio del Sistema.

#### Acceptance Criteria

1. CUANDO se completa el registro de un Producto o una Venta, EL SISTEMA DEBERÁ almacenar los datos de forma persistente en la Base de datos MySQL/MariaDB.
2. SI el Sistema se cierra normalmente o el servidor se reinicia, ENTONCES EL SISTEMA DEBERÁ conservar todos los Productos y Ventas registrados previamente en la Base de datos.

### Requirement 6: RNF2 — Integridad de datos

**User Story:** Como Administrador, quiero que el Sistema garantice la integridad de los datos, para confiar en que el catálogo y las Ventas sean consistentes y válidos.

#### Acceptance Criteria

1. CUANDO el Sistema registra un Producto, EL SISTEMA DEBERÁ garantizar la unicidad del Código, de modo que no existan dos Productos con el mismo Código en el catálogo. *(RN4)*
2. CUANDO el Sistema registra una Venta, EL SISTEMA DEBERÁ verificar la integridad referencial asociando la Venta a un Producto existente en el catálogo.
3. CUANDO el Sistema registra un Producto, EL SISTEMA DEBERÁ validar que el stock actual y el stock mínimo sean valores numéricos enteros no negativos.
4. CUANDO el Sistema registra una Venta, EL SISTEMA DEBERÁ validar que la cantidad vendida sea un valor numérico entero mayor que cero. *(RN1)*
5. SI se ingresan valores inválidos (códigos vacíos, precios negativos, cantidades no numéricas), ENTONCES EL SISTEMA DEBERÁ rechazar la operación antes de persistir los datos.

### Requirement 7: RNF3 — Atomicidad de la venta y descuento de stock

**User Story:** Como Administrador, quiero que el registro de una Venta y el descuento del stock sean atómicos, para que nunca quede un estado intermedio inconsistente.

#### Acceptance Criteria

1. CUANDO el Sistema registra una Venta, EL SISTEMA DEBERÁ ejecutar el registro de la Venta y el descuento del stock del Producto como una transacción atómica, de modo que ambas operaciones se completen exitosamente o ninguna se aplique. *(RN5)*
2. SI el descuento de stock falla durante el registro de una Venta, ENTONCES EL SISTEMA DEBERÁ revertir la transacción de modo que la Venta no quede registrada. *(RN5)*
3. SI el registro de la Venta falla, ENTONCES EL SISTEMA DEBERÁ revertir la transacción de modo que el stock no sea descontado. *(RN5)*

### Requirement 8: RNF4 — Seguridad de la aplicación

**User Story:** Como Administrador, quiero que el Sistema proteja los datos contra vulnerabilidades comunes de aplicaciones web, para mantener la integridad y confidencialidad de la información del minimarket.

#### Acceptance Criteria

1. CUANDO el Sistema ejecuta consultas sobre la Base de datos, EL SISTEMA DEBERÁ usar sentencias preparadas PDO con consultas parametrizadas para prevenir la inyección SQL.
2. CUANDO el Sistema procesa el envío de formularios que modifican datos (registro de Productos y registro de Ventas), EL SISTEMA DEBERÁ validar un token anti-CSRF para prevenir la falsificación de petición entre sitios.
3. CUANDO el Sistema muestra en la interfaz datos ingresados por el usuario (nombres, categorías y códigos de Productos), EL SISTEMA DEBERÁ escapar los caracteres especiales HTML para prevenir ataques de cross-site scripting (XSS).

## Supuestos

- **SUPUESTO 1:** El minimarket cuenta con al menos un Administrador y uno o más Vendedores que comparten el acceso al Sistema sin autenticación.
- **SUPUESTO 2:** "Día" se define como el día calendario natural (00:00 a 23:59), no como un turno de trabajo.
- **SUPUESTO 3:** La persistencia se implementa sobre el motor de Base de datos de XAMPP (MySQL/MariaDB).
- **SUPUESTO 4:** El volumen máximo esperado es de aproximadamente 500 Productos y 200 Ventas diarias.
- **SUPUESTO 5:** El Sistema es de uso local; no se requiere alta disponibilidad ni acceso remoto.

## Trazabilidad de reglas de negocio

Notación: `RFx.n` = Requerimiento x, criterio de aceptación n.

| Regla | Descripción | Criterios que la cubren |
|-------|-------------|--------------------------|
| RN1 | Cantidad vendida mayor que cero | RF3.5, RNF2.4 |
| RN2 | Prohibido vender más unidades que el stock | RF3.6 |
| RN3 | Precio de venta no negativo | RF1.3 |
| RN4 | Código de producto único | RF1.2, RNF2.1 |
| RN5 | Venta y descuento de stock atómicos | RNF3.1, RNF3.2, RNF3.3 |
