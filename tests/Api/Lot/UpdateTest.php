<?php

namespace Api\Lot;

use App\DataFixtures\UserFixture;
use App\Entity\User;
use App\Repository\LotRepository;
use App\Repository\UserRepository;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class UpdateTest extends WebTestCase
{
    /**
     * @covers \App\Controller\LotController::update
     */
    #[DataProvider('provideUsers')]
    public function testUpdateSuccessful(User $user): void
    {
        self::ensureKernelShutdown();
        $client = self::createClient();
        $container = self::getContainer();

        $lotRepository = $container->get(LotRepository::class);
        $lot = $lotRepository->getFirst();
        $lotId = $lot->getId();
        $lotImage = $lot->getImage();

        $client->loginUser($user);
        $client->setServerParameter('Content-Type', 'application/x-www-form-urlencoded');

        $testFilesDir = $container->getParameter('test_files_dir');
        $filePath = $testFilesDir.DIRECTORY_SEPARATOR.'test-update.png';
        copy($testFilesDir.DIRECTORY_SEPARATOR.'test-image.png', $filePath);

        self::assertFileExists($filePath);

        $requestBody = [
            'title' => 'New Changed Title',
            'cost' => 10000,
            'count' => 1,
        ];

        $client->request(
            'POST',
            '/api/lot/'.$lotId,
            parameters: $requestBody,
            files: [
                'image' => new UploadedFile(
                    $filePath,
                    'test-update.png',
                    mimeType: 'image/png',
                    test: true
                ),
            ],
        );

        self::assertResponseIsSuccessful();

        $updatedLot = $lotRepository->find($lotId);
        self::assertEquals($requestBody['title'], $updatedLot->getTitle());
        self::assertEquals($requestBody['cost'], $updatedLot->getCost());
        self::assertEquals($requestBody['count'], $updatedLot->getCount());
        self::assertNotEquals($updatedLot->getImage(), $$lotImage);

        $uploadDir = $container->getParameter('upload_directory');
        self::assertFileExists($uploadDir.DIRECTORY_SEPARATOR.$updatedLot->getImage());
    }

    public static function provideUsers(): array
    {
        $container = self::getContainer();
        $userRepository = $container->get(UserRepository::class);

        return [
            [
                $userRepository->findOneBy(['email' => UserFixture::ADMIN_EMAIL]),
            ],
            [
                $userRepository->findOneBy(['email' => UserFixture::MANAGER_EMAIL]),
            ],
        ];
    }
}
