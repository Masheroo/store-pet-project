<?php

namespace App\Tests\Api\Lot;

use App\DataFixtures\UserFixture;
use App\Repository\LotRepository;
use App\Repository\OrderRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BuyLotTest extends WebTestCase
{
    /** @covers \App\Controller\LotController::buyLot */
    public function testBuyLotSuccessful(): void
    {
        $client = self::createClient();
        $container = self::getContainer();

        /** @var UserRepository $userRepository */
        $userRepository = $container->get(UserRepository::class);
        $user = $userRepository->findOneBy(['email' => UserFixture::USER_EMAIL]);
        $userBalanceBeforeBuy = $user->getBalance();

        $client->loginUser($user);

        /** @var LotRepository $lotRepository */
        $lotRepository = $container->get(LotRepository::class);
        $lot = $lotRepository->findAll()[1];

        $client->jsonRequest('POST', '/api/lot/buy/'.$lot->getId(), [
            'quantity' => 1
        ]);
        self::assertResponseIsSuccessful();

        /** @var OrderRepository $orderRepository */
        $orderRepository = $container->get(OrderRepository::class);
        $order = $orderRepository->findOneBy(['user' => $user->getId()]);

        self::assertNotNull($order);
        self::assertTrue($user->getBalance() < $userBalanceBeforeBuy);
        self::assertTrue($user->getBalance() + $order->getFullPrice() - $order->getDiscount() == $userBalanceBeforeBuy);
    }
}
