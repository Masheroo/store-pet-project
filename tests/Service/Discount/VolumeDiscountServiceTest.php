<?php

namespace App\Tests\Service\Discount;

use App\DataFixtures\DiscountFixtures;
use App\DataFixtures\UserFixture;
use App\Repository\OrderRepository;
use App\Service\Discount\VolumeDiscountService;
use App\Tests\Traits\UserGetterTrait;
use App\Type\DiscountType;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

use function PHPUnit\Framework\assertEquals;

class VolumeDiscountServiceTest extends KernelTestCase
{
    use UserGetterTrait;

    /**
     * @throws NonUniqueResultException
     */
    public function testComputeDiscountSuccessful(): void
    {
        $container = self::getContainer();
        /** @var VolumeDiscountService $service */
        $service = $container->get(VolumeDiscountService::class);

        $user = $this->getUserByEmail(UserFixture::USER_EMAIL, $container);

        /** @var OrderRepository $orderRepository */
        $orderRepository = $container->get(OrderRepository::class);
        $order = $orderRepository->findOneBy(['user' => $user]);

        $discount = $service->computeDiscount($order);

        assertEquals(1, count($discount));
        assertEquals(DiscountFixtures::VOLUME_DISCOUNT_VALUE * $order->getFullPrice(), $discount[0]->discount);
        assertEquals(DiscountType::Percent, $discount[0]->type);
        assertEquals($service::DISCOUNT_NAME, $discount[0]->discountName);
    }

    protected function setUp(): void
    {
        self::bootKernel();
    }
}
