-- ============================================================
--  Esquema de base de datos — Sistema de Ventas e Inventario
--  Minimarket "La Esquina"
--  Motor: MySQL / MariaDB (InnoDB) sobre XAMPP
--  Codificación: utf8mb4
--  Archivo: database/schema.sql
-- ============================================================

-- --------------------------------------------------------
-- Base de datos
-- --------------------------------------------------------
CREATE DATABASE IF NOT EXISTS bodega_ventas
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_general_ci;

USE bodega_ventas;

-- --------------------------------------------------------
--  Tabla: producto  (catálogo de productos — RF1, RF2)
-- --------------------------------------------------------
--  Se elimina en orden de dependencia para permitir
--  reimportar el script sin errores.
DROP TABLE IF EXISTS venta;
DROP TABLE IF EXISTS producto;

CREATE TABLE producto (
    id            INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    codigo        VARCHAR(64)   NOT NULL,               -- RN4: identificador único
    nombre        VARCHAR(150)  NOT NULL,
    categoria     VARCHAR(80)   DEFAULT NULL,           -- puede quedar vacío
    precio_venta  DECIMAL(10,2) NOT NULL DEFAULT 0.00,   -- RN3: no negativo
    stock_actual  INT UNSIGNED  NOT NULL DEFAULT 0,     -- RNF2.3: no negativo
    stock_minimo  INT UNSIGNED  NOT NULL DEFAULT 0,     -- RNF2.3: no negativo
    PRIMARY KEY (id),
    UNIQUE KEY uk_producto_codigo (codigo),
    CONSTRAINT chk_precio_venta CHECK (precio_venta >= 0),
    CONSTRAINT chk_stock_actual CHECK (stock_actual  >= 0),
    CONSTRAINT chk_stock_minimo CHECK (stock_minimo  >= 0)
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
--  Tabla: venta  (registro de ventas — RF3, RF4)
-- --------------------------------------------------------
CREATE TABLE venta (
    id              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    producto_id     INT UNSIGNED   NOT NULL,            -- RNF2.2: FK a producto
    cantidad        INT UNSIGNED   NOT NULL,            -- RN1: entero > 0
    precio_unitario DECIMAL(10,2)  NOT NULL,            -- precio vigente al vender
    total           DECIMAL(10,2)  NOT NULL,            -- precio_unitario * cantidad
    fecha_hora      DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_venta_producto (producto_id),
    KEY idx_venta_fecha    (fecha_hora),
    CONSTRAINT fk_venta_producto
        FOREIGN KEY (producto_id) REFERENCES producto(id),
    CONSTRAINT chk_cantidad CHECK (cantidad > 0)
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_general_ci;
