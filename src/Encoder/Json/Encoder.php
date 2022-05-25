<?php

declare(strict_types=1);

namespace Cushon\HealthBundle\Encoder\Json;

interface Encoder
{
    /**
     * @param mixed $data
     * @return string
     */
    public function encode(mixed $data): string;
}
