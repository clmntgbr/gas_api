<?php

namespace App\Command;

use App\Services\GasStationClosedService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:gas-station-closed',
    description: 'Check if a gas station is closed or not.',
)]
class GasStationClosedCommand extends Command
{
    public function __construct(
        private GasStationClosedService $gasStationClosedService,
    ) {
        parent::__construct(self::getDefaultName());
    }

    protected function configure(): void
    {
        $this->setDescription(self::getDescription());
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->gasStationClosedService->update();

        return Command::SUCCESS;
    }
}
