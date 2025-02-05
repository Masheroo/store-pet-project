<?php

namespace App\Tests\Command;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

class CreateAdminCommandTest extends KernelTestCase
{
    public const COMMAND = 'app:create:admin';

    /**
     * @throws \Exception
     */
    public function testCreateAdminSuccessful(): void
    {
        self::ensureKernelShutdown();
        $kernel = self::bootKernel();
        $application = new Application($kernel);

        $command = $application->find(self::COMMAND);
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'email' => 'example.admin@mail.ru',
            'password' => 'admin123',
        ]);

        $commandTester->assertCommandIsSuccessful();

        /** @var ManagerRegistry $managerRegistry */
        $managerRegistry = $kernel->getContainer()->get('doctrine');
        $em = $managerRegistry->getManager();
        $entity = $em
            ->getRepository(User::class)
            ->findOneBy(['email' => 'example.admin@mail.ru']);

        self::assertNotEmpty($entity);
    }

    public function testCreateAdminThrowValidationException(): void
    {
        self::ensureKernelShutdown();
        $kernel = self::bootKernel();
        $application = new Application($kernel);

        $command = $application->find(self::COMMAND);
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'email' => '123',
            'password' => '123',
        ]);

        self::assertEquals(Command::FAILURE, $commandTester->getStatusCode() );

        /** @var UserRepository $managerRegistry */
        $userRepository = self::getContainer()->get(UserRepository::class);

        self::assertNull($userRepository->findOneBy(['email' => '123']));
    }
}
