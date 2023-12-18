<?php

namespace Api\Lot;

use App\Entity\User;
use App\Repository\LotRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class UpdateTest extends WebTestCase
{
    public function testUpdateSuccessful(User $user): void
    {
        self::ensureKernelShutdown();
        $client = self::createClient();
        $container = self::getContainer();

        $lotRepository = $container->get(LotRepository::class);
        $lot = $lotRepository->getFirst();
        $lotId = $lot->getId();

        $client->loginUser($user);

        $testFilesDir = $container->getParameter('test_files_dir');
        $filePath = $testFilesDir.DIRECTORY_SEPARATOR.'test-update.png';
        copy($testFilesDir.DIRECTORY_SEPARATOR.'test-image.png', $filePath);

        self::assertFileExists($filePath);

        $requestBody = [
            'title' => 'New Changed Title',
            'cost' => 10000,
            'count' => 1,
        ];

        $client->request('PATCH', '/api/lot/'.$lotId, $requestBody, files: [
            new UploadedFile(
                $filePath,
                'test-update.png'
            ),
        ]);

        self::assertResponseIsSuccessful();

        $updatedLot = $lotRepository->find($lotId);
        self::assertEquals($requestBody['title'], $updatedLot->getTitle());
        self::assertEquals($requestBody['cost'], $updatedLot->getCost());
        self::assertEquals($requestBody['count'], $updatedLot->getCount());
        self::assertNotEquals($updatedLot->getImage(), $lot->getImage());

        $uploadDir = $container->getParameter('upload_directory');
        self::assertFileExists($uploadDir.DIRECTORY_SEPARATOR.$updatedLot->getImage());
    }
}
