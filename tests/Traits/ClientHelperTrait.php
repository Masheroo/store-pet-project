<?php

declare(strict_types=1);

namespace App\Tests\Traits;

use App\DataFixtures\UserFixture;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\DependencyInjection\ContainerInterface;

trait ClientHelperTrait
{
    public function getJsonDecodedResponse(KernelBrowser $client): array
    {
        $content = $client->getResponse()->getContent();

        return json_decode($content, true);
    }

    public function getLoginClient(KernelBrowser $client, ContainerInterface $container): KernelBrowser
    {
        /** @var UserRepository $userRepository */
        $userRepository = $container->get(UserRepository::class);
        $user = $userRepository->findOneBy(['email' => UserFixture::USER_EMAIL]);
        assert($user != null);

        return $client->loginUser($user);
    }
}
