<?php

namespace App\Service\Resolver;

use Symfony\Component\HttpFoundation\Request;

class FilterResolver
{
    /**
     * @return array<array-key,non-empty-list<int>>
     */
    public function resolve(Request $request): array
    {
        $filters = [];

        if ($requestData = $request->query->all('filter')) {

            foreach ($requestData as $key => $data) {
                if (is_array($data)) {
                    foreach ($data as $filterId) {
                        if (ctype_digit($filterId)) {
                            $filters[$key][] = (int) $filterId;
                        }
                    }
                }
            }
        }
        return $filters;
    }
}
