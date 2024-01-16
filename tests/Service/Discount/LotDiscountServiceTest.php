<?php

namespace App\Tests\Service\Discount;

use App\DataFixtures\DiscountFixtures;
use App\DataFixtures\UserFixture;
use App\Entity\Order;
use App\Repository\LotDiscountRepository;
use App\Repository\OrderRepository;
use App\Service\Discount\LotDiscountService;
use App\Tests\Traits\UserGetterTrait;
use App\Type\DiscountType;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

use function PHPUnit\Framework\assertEquals;

class LotDiscountServiceTest extends KernelTestCase
{
    use UserGetterTrait;

    public function testComputeDiscount(): void
    {
        self::bootKernel();
        $container = self::getContainer();
        /** @var LotDiscountService $service */
        $service = $container->get(LotDiscountService::class);

        $user = $this->getUserByEmail(UserFixture::USER_EMAIL, $container);

        /** @var LotDiscountRepository $lotDiscountRepository */
        $lotDiscountRepository = $container->get(LotDiscountRepository::class);
        $lotDiscount = $lotDiscountRepository->findAll()[0];
        $lot = $lotDiscount->getLot();

        $order = new Order($user, $lot, DiscountFixtures::LOT_DISCOUNT_COUNT_OF_PURCHASES);

        $discount = $service->computeDiscount($order);

        assertEquals(1, count($discount));
        assertEquals(DiscountFixtures::LOT_DISCOUNT_DISCOUNT_VALUE * $order->getFullPrice(), $discount[0]->discount);
        assertEquals(DiscountType::Percent, $discount[0]->type);
        assertEquals($service::DISCOUNT_NAME, $discount[0]->discountName);
    }
}
