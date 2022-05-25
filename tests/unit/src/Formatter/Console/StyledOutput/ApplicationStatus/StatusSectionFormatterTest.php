<?php

declare(strict_types=1);

namespace Tests\Unit\Formatter\Console\StyledOutput\ApplicationStatus;

use Cushon\HealthBundle\Formatter\Console\StyledOutput\ApplicationStatus\StatusSection;
use Cushon\HealthBundle\Message\Result\HealthCheck;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Tester\TesterTrait;

final class StatusSectionFormatterTest extends TestCase
{
    use ProphecyTrait;
    use TesterTrait;

    protected function setUp(): void
    {
        $this->input = new ArrayInput([]);

        $this->initOutput([
            'capture_stderr_separately' => true,
        ]);
    }

    public function testItAddsTheApplicationStatusSection(): void
    {
        $healthCheck = $this->prophesize(HealthCheck::class);
        $healthCheck->isHealthy()->willReturn(true);

        $symfonyStyler = new SymfonyStyle($this->getInput(), $this->getOutput());

        $healthyMsg = 'yep it is %s';
        $statusSectionFormatter = new StatusSection($healthyMsg);
        $statusSectionFormatter->format(
            $healthCheck->reveal(),
            $symfonyStyler
        );

        $display = $this->getDisplay();

        $this->assertStringContainsString('Application Health', $display);

        $this->assertStringContainsString(
            sprintf($healthyMsg, StatusSection::STATUS_HEALTHY),
            $display
        );
    }
}
