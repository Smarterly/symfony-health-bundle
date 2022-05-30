<?php

declare(strict_types=1);

namespace Cushon\HealthBundle\Encoder\Json;

/**
 * @author Barney Hanlon <barney.hanlon@cushon.co.uk>
 */
interface Encoder
{
    /**
     * @param mixed $data
     * @return string
     */
    public function encode(mixed $data): string;
}
