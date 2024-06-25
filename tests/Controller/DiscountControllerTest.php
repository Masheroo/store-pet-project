<?php
namespace App\Tests\Controller;

use App\Controller\DiscountController;
use App\DataFixtures\UserFixture;
use App\Repository\CityRepository;
use App\Repository\LotRepository;
use App\Tests\Traits\ClientHelperTrait;
use App\Tests\Traits\UserGetterTrait;
use App\Type\DiscountType;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

use function PHPUnit\Framework\assertArrayHasKey;
use function PHPUnit\Framework\assertEquals;

class DiscountControllerTest extends WebTestCase
{
    use ClientHelperTrait;
    use UserGetterTrait;

    /** @covers \App\Controller\DiscountController::createVolumeDiscount */
    public function testCreateVolumeDiscount(): void
    {
        $client = self::createClient();
        $container = self::getContainer();
        $client = $this->getLoginByAdminClient($client, $container);

        $client->request('post', 'api/discount/volume', [
            'amount' => 1001,
            'discount' => .01
        ]);

        self::assertResponseIsSuccessful();

        $response = $this->getJsonDecodedResponse($client);

        self::assertArrayHasKey('amount', $response);
        self::assertArrayHasKey('discount', $response);
        self::assertArrayHasKey('id', $response);
        self::assertEquals(1001, $response['amount']);
        self::assertEquals(.01, $response['discount']);
    }

    /** @covers \App\Controller\DiscountController::createCityDiscount */
    public function testCreateCityDiscount(): void
    {
        $client = self::createClient();
        $container = self::getContainer();
        $client = $this->getLoginByAdminClient($client, $container);

        /** @var CityRepository $cityRepository */
        $cityRepository = $container->get(CityRepository::class);
        $city = $cityRepository->findAll()[0];
        assert($city != null);

        $client->request('post', 'api/discount/city/'.$city->getId(),[
            'discount' => .1
        ]);

        $response = $this->getJsonDecodedResponse($client);

        self::assertArrayHasKey('id', $response);
        self::assertArrayHasKey('discount', $response);
        self::assertArrayHasKey('city', $response);
        self::assertEquals(.1, $response['discount']);
        self::assertEquals($city->getId(), $response['city']['id']);
    }

    /** @covers \App\Controller\DiscountController::createLotDiscount */
    public function testCreateLotDiscount(): void
    {
        $client = self::createClient();
        $container = self::getContainer();
        $client = $this->getLoginByAdminClient($client, $container);

        /** @var LotRepository $lotRepository */
        $lotRepository = $container->get(LotRepository::class);
        $lot = $lotRepository->findAll()[0];

        $client->request('post', 'api/discount/lot/'.$lot->getId(), [
            'countOfPurchases' => 1,
            'discount' => .1
        ]);

        self::assertResponseIsSuccessful();

        $response = $this->getJsonDecodedResponse($client);

        self::assertArrayHasKey('id', $response);
        self::assertArrayHasKey('discount', $response);
        self::assertArrayHasKey('countOfPurchases', $response);
        self::assertArrayHasKey('lot', $response);
        self::assertEquals(1, $response['countOfPurchases']);
        self::assertEquals(.1, $response['discount']);
        self::assertEquals($lot->getId(), $response['lot']['id']);
    }

    /** @covers \App\Controller\DiscountController::createUserDiscount */
    #[DataProvider('provideUserDiscountTypeAndDiscountValues')]
    public function testCreateUserDiscount(float $discount, int $type): void
    {
        $client = self::createClient();
        $container = self::getContainer();
        $client = $this->getLoginByAdminClient($client, $container);

        $user = $this->getUserByEmail(UserFixture::USER_EMAIL, $container);

        $client->request('post', 'api/discount/user/'.$user->getId(), [
            'discount' => $discount,
            'type' => $type
        ]);

        self::assertResponseIsSuccessful();

        $response = $this->getJsonDecodedResponse($client);

        assertArrayHasKey('id', $response);
        assertArrayHasKey('discount', $response);
        assertArrayHasKey('type', $response);
        assertArrayHasKey('user', $response);
        assertEquals($discount, $response['discount']);
        assertEquals($type, $response['type']);
        assertEquals($type, $response['type']);
        assertEquals($user->getId(), $response['user']['id']);
    }

    public static function provideUserDiscountTypeAndDiscountValues(): iterable
    {
        return [
            [
                'discount' => .1,
                'type' => DiscountType::Percent->value
            ],
            [
                'discount' => 1000,
                'type' => DiscountType::Absolute->value
            ]
        ];
    }
}