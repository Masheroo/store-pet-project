<?php

namespace App\Security\TokenExtractor;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Http\AccessToken\AccessTokenExtractorInterface;

class AccessTokenExtractor implements AccessTokenExtractorInterface
{
    public function extractAccessToken(Request $request): ?string
    {
        return $request->headers->get('External-Authentication') ?? throw new AccessDeniedException();
    }
}