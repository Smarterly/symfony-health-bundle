<?php
declare(strict_types=1);

namespace App\Kernel\Exception;

use RuntimeException;

final class EnvironmentVariableNotSet extends RuntimeException
{
    /**
     * @param string $varName
     * @return static
     */
    public static function fromVarName(string $varName): self
    {
        return new self(sprintf('The environment variable "%s" was not set', $varName));
    }
}
