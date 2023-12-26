<?php

namespace App\Message;

class NewLotEmailMessage
{
    public function __construct(
        private readonly ?int $lotId,
        private readonly ?string $lotTitle,
        private readonly ?int $lotCount,
        private readonly ?int $lotCost,
        private readonly ?string $lotImageUrl,
        private readonly ?string $userEmail
    ) {
    }

    public function getLotId(): ?int
    {
        return $this->lotId;
    }

    public function getLotTitle(): ?string
    {
        return $this->lotTitle;
    }

    public function getLotCount(): ?int
    {
        return $this->lotCount;
    }

    public function getLotCost(): ?int
    {
        return $this->lotCost;
    }

    public function getLotImageUrl(): ?string
    {
        return $this->lotImageUrl;
    }

    public function getUserEmail(): ?string
    {
        return $this->userEmail;
    }
}
