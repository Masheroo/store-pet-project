<?php

namespace App\DataFixtures;

use App\Entity\ExternalApiToken;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ExternalApiTokenFixture extends Fixture
{
    public const TOKEN = 'pu5V/eU6QWRhyKQez/FBy-wkqdNNxKH2GC26ttEQk906Z8UxTfqqt?yjN9tETRxvXQwkBD1lN!eW2Xu!SFG-!AUym6cVy5M8e0O3im7mY?Cd!1mF2eYGJ=GanQ4UjY!6uv7M=O8q4-SYq-bONahY39Mur?heX9uOoqGCi3bpQ9c4/duZHse!d2RmjcTA7a2!C/0?hSzbTA=u?G2pBw=!lXWwwcYT=Pd/ywTWtRPZRt5y1tzKiY?SEHaNjvYTlK?m';
    public const TOKEN_NAME = 'testExternalToken';
    public function load(ObjectManager $manager): void
    {
        $externalApiToken = new ExternalApiToken(self::TOKEN, self::TOKEN_NAME);
        $manager->persist($externalApiToken);
        $manager->flush();
    }
}
