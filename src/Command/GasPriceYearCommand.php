<?php

namespace App\Command;

use App\Services\GasPriceYearService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

#[AsCommand(
    name: 'app:gas-price-year',
    description: 'Creating year GasPrices.',
)]
class GasPriceYearCommand extends Command
{
    public function __construct(
        private GasPriceYearService $gasPriceYearService,
    ) {
        parent::__construct(self::getDefaultName());
    }

    protected function configure(): void
    {
        $this->setDescription(self::getDescription());
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $helper = $this->getHelper('question');
        $questionYear = new Question('Which year to insert ? ', '2007');

        $year = $helper->ask($input, $output, $questionYear);

        $this->gasPriceYearService->update($year);

        return Command::SUCCESS;
    }
}