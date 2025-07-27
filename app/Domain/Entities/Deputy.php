<?php

namespace App\Domain\Entities;

class Deputy
{
    public ?int $id = null;
    public ?string $uri = null;
    public ?string $name = null;
    public ?string $party_abbr = null;
    public ?string $party_uri = null;
    public ?string $state_abbr = null;
    public ?int $legislature_id = null;
    public ?string $photo_url = null;
    public ?string $email = null;
}
