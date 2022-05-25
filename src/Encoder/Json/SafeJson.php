<?php

declare(strict_types=1);

namespace Cushon\HealthBundle\Encoder\Json;

use Ergebnis\Json\Printer\PrinterInterface;

use function Safe\json_encode;

class SafeJson implements Encoder
{
    public const DEFAULT_INDENT = 2;

    private PrinterInterface $printer;

    private string $indent;

    /**
     * @param PrinterInterface $printer
     * @param int $indent
     */
    public function __construct(PrinterInterface $printer, int $indent = self::DEFAULT_INDENT)
    {
        $this->printer = $printer;
        $this->indent = str_repeat(' ', $indent);
    }

    /**
     * @inheritDoc
     */
    public function encode(mixed $data): string
    {
        return $this->printer->print(
            json_encode($data),
            $this->indent
        );
    }
}
