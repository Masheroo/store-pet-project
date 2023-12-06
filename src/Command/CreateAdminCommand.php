<?php

namespace App\Command;

use App\Repository\UserRepository;
use App\Service\UserService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\Exception\ValidationFailedException;

#[AsCommand(
    name: 'app:create:admin',
    description: 'Creates a new Admin',
)]
class CreateAdminCommand extends Command
{
    public function __construct(
        private readonly UserService $userService,
        private readonly UserRepository $userRepository
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('email', InputArgument::REQUIRED, 'Enter an admin email')
            ->addArgument('password', InputArgument::REQUIRED, 'Enter an admin password');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $email = (string) $input->getArgument('email');
        $password = (string) $input->getArgument('password');

        $io->writeln('Creating Admin...');

        try {
            $admin = $this->userService->createAdmin($email, $password);
            $this->userRepository->persistAndFlush($admin);
        } catch (ValidationFailedException $exception) {
            $violations = $exception->getViolations();

            /** @var ConstraintViolation $violation */
            foreach ($violations as $violation) {
                $io->error([
                    $violation->getPropertyPath(),
                    $violation->getMessage(),
                ]);
            }

            return Command::FAILURE;
        }

        $io->success('Admin has been created!');

        return Command::SUCCESS;
    }
}
