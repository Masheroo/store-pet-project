<?php

namespace App\Tests\Service\Discount;

use App\DataFixtures\DiscountFixtures;
use App\DataFixtures\UserFixture;
use App\Repository\OrderRepository;
use App\Service\Discount\PersonalUserDiscountService;
use App\Service\Discount\VolumeDiscountService;
use App\Tests\Traits\UserGetterTrait;
use App\Type\DiscountType;
use PHPUnit\Framework\TestCase;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

use function PHPUnit\Framework\assertEquals;

class PersonalUserDiscountServiceTest extends KernelTestCase
{
    use UserGetterTrait;
    public function testComputeDiscountSuccessful(): void
    {
        $container = self::getContainer();
        /** @var PersonalUserDiscountService $service */
        $service = $container->get(PersonalUserDiscountService::class);

        $user = $this->getUserByEmail(UserFixture::USER_EMAIL, $container);

        /** @var OrderRepository $orderRepository */
        $orderRepository = $container->get(OrderRepository::class);
        $order = $orderRepository->findOneBy(['user' => $user]);

        $discounts = $service->computeDiscount($order);

        $totalDiscount = 0;

        foreach ($discounts as $discount){
            $totalDiscount += $discount->discount;
        }

        self::assertTrue($discounts > 1);
        assertEquals(DiscountFixtures::PERCENT_USER_DISCOUNT_VALUE * $order->getFullPrice() + DiscountFixtures::ABSOLUTE_USER_DISCOUNT_VALUE, $totalDiscount);
        assertEquals($service::DISCOUNT_NAME, $discounts[0]->discountName);
    }

    protected function setUp(): void
    {
        self::bootKernel();
    }
}
