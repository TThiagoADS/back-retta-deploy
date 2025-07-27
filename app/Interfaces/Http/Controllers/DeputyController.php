<?php

namespace App\Interfaces\Http\Controllers;

use App\Interfaces\Http\Controllers\Controller;
use App\Domain\Entities\Deputy;
use App\Domain\Repositories\DeputyRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class DeputyController extends Controller
{
    private DeputyRepositoryInterface $repository;

    public function __construct(DeputyRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'party_abbr' => ['string', 'max:10'],
            'state_abbr' => ['string', 'size:2'],
            'active'     => ['boolean'],
        ]);

        if ($request->boolean('active')) {
            $deputies = $this->repository->getActive();
        } elseif ($request->filled('party_abbr')) {
            $deputies = $this->repository->findByParty($request->party_abbr);
        } elseif ($request->filled('state_abbr')) {
            $deputies = $this->repository->findByState($request->state_abbr);
        } else {
            $deputies = $this->repository->getAll();
        }

        return response()->json($deputies);
    }

    public function show(int $id): JsonResponse
    {
        $deputy = $this->repository->findById($id);

        if (! $deputy) {
            return response()->json(['message' => 'Deputy not found'], 404);
        }

        return response()->json($deputy);
    }

    public function sumNetValueTotal(): JsonResponse
    {
        $total = $this->repository->sumNetValueTotal();

        return response()->json([$total]);
    }

    public function sumStateAbbr(): JsonResponse
    {
        $total = $this->repository->sumStateAbbr();

        return response()->json($total);
    }

    public function getWithExpenses(): JsonResponse
    {
        $deputies = $this->repository->getWithExpenses();

        return response()->json($deputies);
    }

    public function sumDeputy(): JsonResponse
    {
        $total = $this->repository->sumDeputy();

        return response()->json([$total]);
    }

    public function countDeputiesByParty(): JsonResponse
    {
        $result = $this->repository->countDeputiesByParty();

        $arrayResult = is_object($result) && method_exists($result, 'toArray') ? $result->toArray() : $result;

        return response()->json($arrayResult);
    }
}
