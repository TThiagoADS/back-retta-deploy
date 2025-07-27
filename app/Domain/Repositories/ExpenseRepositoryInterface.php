<?php

namespace App\Domain\Repositories;

use App\Domain\Entities\Expense;

interface ExpenseRepositoryInterface
{
    public function save(Expense $expense): void;

    public function findById(int $id): ?Expense;

    public function findByUniqueFields(
        int $deputyId,
        int $documentCode,
        ?int $reimbursementNumber = null
    ): ?Expense;

    public function findByDeputyId(int $deputyId): \Illuminate\Support\Collection;

    public function findByPeriod(int $year, int $month): \Illuminate\Support\Collection;

    public function deleteOlderThan(\DateTime $date): int;

    public function sumNetValueTotal(): float;

    public function sumExpenseType(): \Illuminate\Support\Collection;

}
