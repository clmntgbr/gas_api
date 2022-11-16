<?php

namespace App\Command;

use App\Services\GasPriceWeekService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:gas-price-week',
    description: 'Updating last month GasPrices by week.',
)]
class GasPriceWeekCommand extends Command
{
    public function __construct(
        private GasPriceWeekService $gasPriceWeekService,
    )
    {
        parent::__construct(self::getDefaultName());
    }

    protected function configure(): void
    {
        $this->setDescription(self::getDescription());
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->gasPriceWeekService->update();

        return Command::SUCCESS;
    }
}
