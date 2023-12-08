<?php

declare(strict_types=1);

namespace App\Tests\Traits;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;

trait ClientHelperTrait
{
    public function getJsonDecodedResponse(KernelBrowser $client): array
    {
        $content = $client->getResponse()->getContent();

        return json_decode($content, true);
    }
}
