<?php

namespace App\Services\Courier;

use RuntimeException;

/**
 * Raised when the courier module is misconfigured (missing live credentials,
 * unsupported provider, etc.). Never contains secret values.
 */
class CourierConfigurationException extends RuntimeException
{
}
