<?php

namespace App\Exceptions;

use RuntimeException;

/**
 * Regla de una labor de campo que no se cumple (zona de otro lote, jornal en un evento cancelado...).
 * El mensaje está pensado para mostrárselo tal cual al supervisor.
 */
class EventoException extends RuntimeException
{
}
