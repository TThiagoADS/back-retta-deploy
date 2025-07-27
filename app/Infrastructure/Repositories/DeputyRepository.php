<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Entities\Deputy;
use App\Domain\Entities\Expense as ExpenseEntity;
use App\Domain\Repositories\DeputyRepositoryInterface;
use App\Models\Deputy as DeputyModel;
use App\Models\Expense as ExpenseModel;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class DeputyRepository implements DeputyRepositoryInterface
{
    public function findById(int $id): ?Deputy
    {
        $model = DeputyModel::find($id);

        if (!$model) {
            return null;
        }

        return $this->modelToEntity($model);
    }

    public function getAll(): Collection
    {
        return DeputyModel::all()
            ->map(fn(DeputyModel $model) => $this->modelToEntity($model));
    }

    public function getActive(): Collection
    {
        return DeputyModel::whereNotNull('name')
            ->whereNotNull('party_abbr')
            ->whereNotNull('state_abbr')
            ->get()
            ->map(fn(DeputyModel $model) => $this->modelToEntity($model));
    }

    public function findByParty(string $partyAbbr): Collection
    {
        return DeputyModel::where('party_abbr', $partyAbbr)
            ->get()
            ->map(fn(DeputyModel $model) => $this->modelToEntity($model));
    }

    public function findByState(string $stateAbbr): Collection
    {
        return DeputyModel::where('state_abbr', $stateAbbr)
            ->get()
            ->map(fn(DeputyModel $model) => $this->modelToEntity($model));
    }

public function getWithExpenses(): Collection
{
    $deputies = DeputyModel::with('expenses')->get()->map(function (DeputyModel $deputy) {
        $totalExpenses = $deputy->expenses->sum('net_value');
        return [
            'id' => $deputy->id,
            'name' => $deputy->name,
            'state_abbr' => $deputy->state_abbr,
            'photo_url' => $deputy->photo_url,
            'party_abbr' => $deputy->party_abbr,
            'total_expenses' => $totalExpenses,
        ];
    });

    return $deputies->sortByDesc('total_expenses')->values()->map(function($item) {
        $item['total_expenses'] = number_format($item['total_expenses'], 2, ',', '.');
        return $item;
    });
}




    private function modelToExpenseEntity(ExpenseModel $model): ExpenseEntity
    {
        $expense = new ExpenseEntity();
        $expense->id = $model->id;
        $expense->expense_type = $model->expense_type;
        $expense->net_value = $model->net_value;
        return $expense;
    }


    public function sumDeputy(): int
    {
        return DeputyModel::count();
    }

    public function sumStateAbbr(): int
    {
        return DeputyModel::whereNotNull('state_abbr')->distinct('state_abbr')->count('state_abbr');
    }


public function save(Deputy $deputy): void
{
    $model = isset($deputy->id)
        ? DeputyModel::find($deputy->id) ?? new DeputyModel()
        : new DeputyModel();

    if (isset($deputy->id)) {
        $model->id = $deputy->id;  // <-- Setando o id manualmente no model
    }

    $model->name = $deputy->name ?? null;
    $model->party_abbr = $deputy->party_abbr ?? null;
    $model->state_abbr = $deputy->state_abbr ?? null;
    $model->uri = $deputy->uri ?? null;
    $model->party_uri = $deputy->party_uri ?? null;
    $model->legislature_id = $deputy->legislature_id ?? null;
    $model->photo_url = $deputy->photo_url ?? null;
    $model->email = $deputy->email ?? null;

    $model->save();

    if (!isset($deputy->id)) {
        $deputy->id = $model->id;
    }
}


   public function countDeputiesByParty()
    {
        $result = DeputyModel::select('party_abbr', DB::raw('count(*) as total'))
                            ->groupBy('party_abbr')
                            ->get();

        return response()->json($result);
    }

    private function modelToEntity(DeputyModel $model): Deputy
{
    $deputy = new Deputy();

    $deputy->id = $model->id;
    $deputy->uri = $model->uri ?? '';
    $deputy->name = $model->name ?? '';
    $deputy->party_abbr = $model->party_abbr ?? '';
    $deputy->party_uri = $model->party_uri ?? '';
    $deputy->state_abbr = $model->state_abbr ?? '';
    $deputy->legislature_id = $model->legislature_id ?? '';
    $deputy->photo_url = $model->photo_url ?? '';
    $deputy->email = $model->email ?? '';

    return $deputy;
}
}
