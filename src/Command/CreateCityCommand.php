<?php

namespace App\Command;

use App\Repository\CityRepository;
use App\Service\City\CityService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:create:city',
    description: 'Creates City',
)]
class CreateCityCommand extends Command
{
    public function __construct(
        private readonly CityService $cityService,
        private readonly CityRepository $repository
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $countOfCreatedCity = 0;
        while (true) {

            $cityName = $io->ask('Enter city name (press enter if you want exit)');

            if (!$cityName) {
                break;
            }

            $this->cityService->create($cityName);
            ++$countOfCreatedCity;
        }

        $this->repository->flush();

        $io->success('Count of created cities: '.$countOfCreatedCity);

        return Command::SUCCESS;
    }
}
