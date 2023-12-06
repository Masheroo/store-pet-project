<?php

namespace App\Tests\Command;

use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class CreateAdminCommandTest extends KernelTestCase
{
    public const COMMAND = 'app:create:admin';

    /**
     * @throws Exception
     */
    public function testCreateAdminSuccessful(): void
    {
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
}
