<?php

namespace App\Tests\Service\Lot;

use App\DataFixtures\UserFixture;
use App\Entity\User;
use App\Repository\CategoryRepository;
use App\Repository\LotRepository;
use App\Repository\UserRepository;
use App\Request\CreateLotRequest;
use App\Service\Lot\LotService;
use League\Flysystem\FilesystemException;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

use function PHPUnit\Framework\assertEquals;

class LotServiceTest extends KernelTestCase
{
    /**
     * @throws FilesystemException
     */
    public function testCreateLotFromCreateLotRequest(): void
    {
        self::bootKernel();
        $container = self::getContainer();

        /** @var CategoryRepository $categoryRepository */
        $categoryRepository = $container->get(CategoryRepository::class);
        $category = $categoryRepository->findAll()[0];

        $createLotRequest = new CreateLotRequest();
        $createLotRequest->title = 'TestTitle';
        $createLotRequest->count = 1;
        $createLotRequest->cost = 10.1;
        $createLotRequest->category = $category->getId();

        /** @var string $testFilesDirectory */
        $testFilesDirectory = $container->getParameter('test_files_dir');
        $file = new \SplFileInfo($testFilesDirectory.DIRECTORY_SEPARATOR.'test.png');
        $createLotRequest->image = new UploadedFile($file->getRealPath(), $file->getFilename());

        /** @var UserRepository $userRepository */
        $userRepository = $container->get(UserRepository::class);
        /** @var User $user */
        $user = $userRepository->findOneBy(['email' => UserFixture::MANAGER_EMAIL]);
        /** @var LotService $service */
        $service = $container->get(LotService::class);
        $savedLot = $service->createLotFromRequest($createLotRequest, $user);

        /** @var LotRepository $lotRepository */
        $lotRepository = $container->get(LotRepository::class);
        $lot = $lotRepository->find($savedLot->getId());

        self::assertNotNull($lot);
        assertEquals($savedLot, $lot);

        /** @var string $uploadDirectory */
        $uploadDirectory = $container->getParameter('upload_directory');
        self::assertFileExists($uploadDirectory.DIRECTORY_SEPARATOR.$lot->getImage());
    }
}
