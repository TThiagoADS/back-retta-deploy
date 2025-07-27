<?php

namespace App\Application\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Http;
use App\Domain\Entities\Expense;
use App\Domain\Repositories\ExpenseRepositoryInterface;

class FetchExpensesJob implements ShouldQueue
{
    use Queueable;

    private int $deputyId;

    public function __construct(int $deputyId)
    {
        $this->deputyId = $deputyId;
    }

    public function handle(ExpenseRepositoryInterface $repo): void
    {
        $url   = "https://dadosabertos.camara.leg.br/api/v2/deputados/{$this->deputyId}/despesas";
        $items = Http::get($url)->json('dados', []);

        foreach ($items as $i) {
            $e = new Expense();
            $e->deputy_id            = $this->deputyId;
            $e->year                 = $i['ano'];
            $e->month                = $i['mes'];
            $e->expense_type         = $i['tipoDespesa'];
            $e->document_code        = $i['codDocumento'];
            $e->document_type        = $i['tipoDocumento'];
            $e->document_type_code   = $i['codTipoDocumento'];
            $e->document_date        = substr($i['dataDocumento'], 0, 10);
            $e->document_number      = $i['numDocumento'];
            $e->gross_value          = $i['valorDocumento'];
            $e->document_url         = $i['urlDocumento'];
            $e->supplier_name        = $i['nomeFornecedor'];
            $e->supplier_cnpj_cpf    = $i['cnpjCpfFornecedor'];
            $e->net_value            = $i['valorLiquido'];
            $e->glosa_value          = $i['valorGlosa'];
            $e->reimbursement_number = $i['numRessarcimento'] ?: null;
            $e->batch_code           = $i['codLote'];
            $e->installment          = $i['parcela'];
            $repo->save($e);
        }
    }
}
