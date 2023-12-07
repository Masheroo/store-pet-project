<?php

declare(strict_types=1);

namespace App\Tests\Traits;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;

trait ClientConfiguratorTrait
{
    public function configureJsonClient(KernelBrowser $client): void
    {
        $client->setServerParameter('CONTENT_TYPE', 'application/json');
        $client->setServerParameter('HTTP_ACCEPT', 'application/json');
    }

}
