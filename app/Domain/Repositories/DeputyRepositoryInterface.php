<?php

namespace App\Domain\Repositories;

use App\Domain\Entities\Deputy;
use Illuminate\Support\Collection;

interface DeputyRepositoryInterface
{
    public function save(Deputy $deputy): void;

    public function findById(int $id): ?Deputy;

    public function getAll(): Collection;

    public function getActive(): Collection;

    public function getWithExpenses(): \Illuminate\Support\Collection;

    public function findByParty(string $partyAbbr): Collection;

    public function findByState(string $stateAbbr): Collection;

    public function sumDeputy(): int;

    public function sumStateAbbr(): int;

    public function countDeputiesByParty();
}
