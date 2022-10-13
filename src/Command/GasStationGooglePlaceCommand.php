<?php

namespace App\Command;

use App\Services\GasStationGooglePlaceService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:gas-station-google-place',
    description: 'Find google place for a gas station.',
)]
class GasStationGooglePlaceCommand extends Command
{
    public function __construct(
        private GasStationGooglePlaceService $gasStationGooglePlaceService,
    ) {
        parent::__construct(self::getDefaultName());
    }

    protected function configure(): void
    {
        $this->setDescription(self::getDescription());
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->gasStationGooglePlaceService->update();

        return Command::SUCCESS;
    }
}
