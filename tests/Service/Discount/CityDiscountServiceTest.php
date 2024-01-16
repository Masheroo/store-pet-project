<?php

namespace App\Tests\Service\Discount;

use App\DataFixtures\DiscountFixtures;
use App\DataFixtures\UserFixture;
use App\Repository\OrderRepository;
use App\Service\Discount\CityDiscountService;
use App\Service\Discount\VolumeDiscountService;
use App\Tests\Traits\UserGetterTrait;
use App\Type\DiscountType;
use PHPUnit\Framework\TestCase;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertTrue;

class CityDiscountServiceTest extends KernelTestCase
{
    use UserGetterTrait;
    public function testComputeDiscountSuccessful(): void
    {
        $container = self::getContainer();
        /** @var CityDiscountService $service */
        $service = $container->get(CityDiscountService::class);

        $user = $this->getUserByEmail(UserFixture::USER_EMAIL, $container);

        /** @var OrderRepository $orderRepository */
        $orderRepository = $container->get(OrderRepository::class);
        $order = $orderRepository->findOneBy(['user' => $user]);

        $discounts = $service->computeDiscount($order);

        $totalDiscount = 0;

        foreach ($discounts as $discount){
            $totalDiscount += $discount->discount;
        }

        assertEquals(DiscountFixtures::CITY_DISCOUNT_VALUE * $order->getFullPrice(), $totalDiscount);
        assertEquals(DiscountType::Percent, $discounts[0]->type);
        assertEquals($service::DISCOUNT_NAME, $discounts[0]->discountName);
    }

    protected function setUp(): void
    {
        self::bootKernel();
    }
}
