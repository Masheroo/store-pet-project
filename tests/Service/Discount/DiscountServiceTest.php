<?php

namespace App\Tests\Service\Discount;

use App\DataFixtures\DiscountFixtures;
use App\DataFixtures\UserFixture;
use App\Repository\CityDiscountRepository;
use App\Repository\CityRepository;
use App\Repository\LotDiscountRepository;
use App\Repository\LotRepository;
use App\Repository\OrderRepository;
use App\Repository\UserDiscountRepository;
use App\Repository\VolumeDiscountRepository;
use App\Service\Discount\DiscountService;
use App\Tests\Traits\UserGetterTrait;
use App\Type\DiscountType;
use Doctrine\ORM\NonUniqueResultException;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertNotNull;

class DiscountServiceTest extends KernelTestCase
{
    use UserGetterTrait;

    private ContainerInterface $container;
    private DiscountService $service;

    #[DataProvider('provideDiscountTypeAndValue')]
    public function testCreateUserDiscount(DiscountType $discountType, float $discountValue): void
    {
        $user = $this->getUserByEmail(UserFixture::USER_EMAIL, $this->container);
        $this->service->createUserDiscount($user, $discountValue, $discountType);

        /** @var UserDiscountRepository $userDiscountRepository */
        $userDiscountRepository = $this->container->get(UserDiscountRepository::class);
        $savedUserDiscount = $userDiscountRepository->findOneBy(['user' => $user->getId(), 'type' => $discountType]);

        self::assertNotNull($savedUserDiscount);
        self::assertEquals($discountValue, $savedUserDiscount->getDiscount());
        self::assertEquals($discountType, $savedUserDiscount->getType());
    }

    public function testCreateVolumeDiscount(): void
    {
        $this->service->createVolumeDiscount(100000, 0.01);

        /** @var VolumeDiscountRepository $volumeDiscountRepository */
        $volumeDiscountRepository = $this->container->get(VolumeDiscountRepository::class);
        $savedVolumeDiscount = $volumeDiscountRepository->findOneBy(['amount' => 100000, 'discount' => 0.01]);

        assertNotNull($savedVolumeDiscount);
    }

    public function testCreateCityDiscount(): void
    {
        /** @var CityRepository $cityRepository */
        $cityRepository = $this->container->get(CityRepository::class);
        $city = $cityRepository->findAll()[0] ?? throw new \DomainException('No one city found');

        $this->service->createCityDiscount($city, .1);

        /** @var CityDiscountRepository $cityDiscountRepository */
        $cityDiscountRepository = $this->container->get(CityDiscountRepository::class);
        $cityDiscount = $cityDiscountRepository->findOneBy(['city' => $city]);

        assertNotNull($cityDiscount);
        assertEquals(.1, $cityDiscount->getDiscount());
    }

    /**
     * @throws NonUniqueResultException
     */
    public function testCreateLotDiscount(): void
    {
        /** @var LotRepository $lotRepository */
        $lotRepository = $this->container->get(LotRepository::class);
        $lot = $lotRepository->getFirst();

        $this->service->createLotDiscount($lot, .1, 1);

        /** @var LotDiscountRepository $lotDiscountRepository */
        $lotDiscountRepository = $this->container->get(LotDiscountRepository::class);
        $lotDiscount = $lotDiscountRepository->findOneBy(['lot' => $lot]);

        assertNotNull($lotDiscount);
        assertEquals(.1, $lotDiscount->getDiscount());
        assertEquals(1, $lotDiscount->getCountOfPurchases());
    }

    public function testComputeAllDiscountsForOrder(): void
    {
        $user = $this->getUserByEmail(UserFixture::USER_EMAIL, $this->container);

        /** @var OrderRepository $orderRepository */
        $orderRepository = $this->container->get(OrderRepository::class);
        $order = $orderRepository->findOneBy(['user' => $user]);

        $discounts = $this->service->computeAllDiscountsForOrder($order);
        $totalDiscountValue = 0;

        foreach ($discounts as $discount) {
            $totalDiscountValue += $discount->discount;
        }

        $expectedDiscount =
            DiscountFixtures::VOLUME_DISCOUNT_VALUE * $order->getFullPrice() +
            DiscountFixtures::PERCENT_USER_DISCOUNT_VALUE * $order->getFullPrice() +
            DiscountFixtures::ABSOLUTE_USER_DISCOUNT_VALUE +
            DiscountFixtures::CITY_DISCOUNT_VALUE * $order->getFullPrice();

        assertEquals($expectedDiscount, $totalDiscountValue);
    }

    public static function provideDiscountTypeAndValue(): iterable
    {
        return [
            [
                DiscountType::Percent,
                .1,
            ],
            [
                DiscountType::Absolute,
                1000,
            ],
        ];
    }

    protected function setUp(): void
    {
        self::bootKernel();
        $this->container = self::getContainer();
        /** @var DiscountService $service */
        $service = $this->container->get(DiscountService::class);
        $this->service = $service;
    }
}
