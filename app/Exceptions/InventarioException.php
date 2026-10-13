<?php

namespace App\Exceptions;

use RuntimeException;

/**
 * Regla de inventario que no se cumple (stock insuficiente, lote vencido, unidades incompatibles...).
 * El mensaje está pensado para mostrárselo tal cual al almacenista o al supervisor.
 */
class InventarioException extends RuntimeException
{
}
