<?php

namespace App\Interfaces\Http\Controllers;

use App\Domain\Entities\Expense;
use App\Domain\Repositories\ExpenseRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;

class ExpenseController extends Controller
{
    private ExpenseRepositoryInterface $repository;

    public function __construct(ExpenseRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'deputy_id' => ['integer', 'exists:deputies,id'],
            'year'      => ['integer', 'min:1900', 'max:' . date('Y')],
            'month'     => ['integer', 'between:1,12'],
        ]);

        if ($request->filled('deputy_id')) {
            $expenses = $this->repository->findByDeputyId((int) $request->deputy_id);
        } elseif ($request->filled('year') && $request->filled('month')) {
            $expenses = $this->repository->findByPeriod((int) $request->year, (int) $request->month);
        } else {

            $expenses = $this->repository->findAll();
        }

        return response()->json($expenses);
    }

    public function show(int $id): JsonResponse
    {
        $expense = $this->repository->findById($id);

        if (!$expense) {
            return response()->json(['message' => 'Expense not found'], 404);
        }

        return response()->json($expense);
    }

    public function destroy(int $id): JsonResponse
    {
        $deleted = $this->repository->deleteById($id);

        if (!$deleted) {
            return response()->json(['message' => 'Expense not found or could not be deleted'], 404);
        }

        return response()->json(null, 204);
    }

    public function deleteOlderThan(Request $request): JsonResponse
    {
        $request->validate([
            'date' => ['required', 'date'],
        ]);

        $date = new \DateTime($request->date);
        $count = $this->repository->deleteOlderThan($date);

        return response()->json([ 'deleted' => $count ]);
    }

    public function sumNetValueTotal(): JsonResponse
    {
        $total = $this->repository->sumNetValueTotal();

        $format = number_format($total, 2, ',', '.');

        return response()->json($format);
    }

    public function sumExpenseType(): JsonResponse
    {
        $result = $this->repository->sumExpenseType();

        return response()->json($result);
    }
}
