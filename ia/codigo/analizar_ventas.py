
# BODEGA IQ - Análisis inicial de ventas
# Datos ficticios para comprobar los cálculos

ventas_diarias = [5, 4, 6, 5, 9, 12, 10]

stock_actual = 20

# Calcular el promedio de unidades vendidas
promedio_ventas = sum(ventas_diarias) / len(ventas_diarias)

# Estimar cuántos días durará el inventario
dias_restantes = stock_actual / promedio_ventas

print("=== BODEGA IQ ===")
print("Producto: Arroz")
print(f"Promedio diario: {promedio_ventas:.2f} unidades")
print(f"Stock disponible: {stock_actual} unidades")
print(f"Duración estimada: {dias_restantes:.1f} días")

if dias_restantes <= 3:
    print("ALERTA: Se recomienda reponer el producto.")
else:
    print("Inventario suficiente por el momento.")
