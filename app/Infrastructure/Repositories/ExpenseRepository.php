<?php
namespace App\Infrastructure\Repositories;

use App\Domain\Entities\Expense as ExpenseEntity;
use App\Domain\Repositories\ExpenseRepositoryInterface;
use App\Models\Expense as ExpenseModel;
use Illuminate\Support\Collection;

class ExpenseRepository implements ExpenseRepositoryInterface
{
    public function save(ExpenseEntity $expense): void
    {

        $model = ExpenseModel::firstOrNew([
            'deputy_id'     => $expense->deputy_id,
            'document_code' => $expense->document_code,
        ]);

        $model->deputy_id            = $expense->deputy_id;
        $model->year                 = $expense->year;
        $model->month                = $expense->month;
        $model->expense_type         = $expense->expense_type;
        $model->document_code        = $expense->document_code;
        $model->document_type        = $expense->document_type;
        $model->document_type_code   = $expense->document_type_code;
        $model->document_date        = $expense->document_date;
        $model->document_number      = $expense->document_number;
        $model->gross_value          = $expense->gross_value;
        $model->document_url         = $expense->document_url;
        $model->supplier_name        = $expense->supplier_name;
        $model->supplier_cnpj_cpf    = $expense->supplier_cnpj_cpf;
        $model->net_value            = $expense->net_value;
        $model->glosa_value          = $expense->glosa_value;
        $model->reimbursement_number = $expense->reimbursement_number;
        $model->batch_code           = $expense->batch_code;
        $model->installment          = $expense->installment;

        $model->save();

        if (empty($expense->id)) {
            $expense->id = $model->id;
        }
    }

    public function findById(int $id): ?ExpenseEntity
    {
        $model = ExpenseModel::find($id);
        if (!$model) {
            return null;
        }
        return $this->modelToEntity($model);
    }

    public function findByUniqueFields(
        int $deputyId,
        int $documentCode,
        ?int $reimbursementNumber = null
    ): ?ExpenseEntity
    {
        $query = ExpenseModel::where('deputy_id', $deputyId)
            ->where('document_code', $documentCode);

        if ($reimbursementNumber !== null) {
            $query->where('reimbursement_number', $reimbursementNumber);
        }

        $model = $query->first();
        if (!$model) {
            return null;
        }
        return $this->modelToEntity($model);
    }

    public function findByDeputyId(int $deputyId): Collection
    {
        $models = ExpenseModel::where('deputy_id', $deputyId)->get();
        return $models->map(fn($model) => $this->modelToEntity($model));
    }

    public function findByPeriod(int $year, int $month): Collection
    {
        $models = ExpenseModel::where('year', $year)
            ->where('month', $month)
            ->get();

        return $models->map(fn($model) => $this->modelToEntity($model));
    }

    public function deleteOlderThan(\DateTime $date): int
    {
        return ExpenseModel::where('created_at', '<', $date)->delete();
    }

    public function sumNetValueTotal(): float
    {
        return ExpenseModel::sum('net_value');
    }

    public function sumExpenseType(): Collection
    {
        return ExpenseModel::select('expense_type', \DB::raw('SUM(net_value) as total'))
            ->groupBy('expense_type')
            ->orderBy('total', 'desc')
            ->get()
            ->map(function ($item) {
                return [
                    'expense_type' => $item->expense_type,
                    'total'        => number_format($item->total, 2, ',', '.'),
                ];
            });
    }

    private function modelToEntity(ExpenseModel $model): ExpenseEntity
    {
        $expense = new ExpenseEntity();
        $expense->id                   = $model->id;
        $expense->deputy_id            = $model->deputy_id;
        $expense->year                 = $model->year;
        $expense->month                = $model->month;
        $expense->expense_type         = $model->expense_type;
        $expense->document_code        = $model->document_code;
        $expense->document_type        = $model->document_type;
        $expense->document_type_code   = $model->document_type_code;
        $expense->document_date        = $model->document_date;
        $expense->document_number      = $model->document_number;
        $expense->gross_value          = $model->gross_value;
        $expense->document_url         = $model->document_url;
        $expense->supplier_name        = $model->supplier_name;
        $expense->supplier_cnpj_cpf    = $model->supplier_cnpj_cpf;
        $expense->net_value            = $model->net_value;
        $expense->glosa_value          = $model->glosa_value;
        $expense->reimbursement_number = $model->reimbursement_number;
        $expense->batch_code           = $model->batch_code;
        $expense->installment          = $model->installment;

        return $expense;
    }
}
