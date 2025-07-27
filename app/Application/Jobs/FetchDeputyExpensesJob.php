<?php

namespace App\Application\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Domain\Repositories\ExpenseRepositoryInterface;
use App\Domain\Entities\Expense;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FetchDeputyExpensesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 300;
    public $tries = 3;
    public $backoff = 30;

    public function __construct(
        private int $deputyId
    ) {
        if (empty($this->deputyId) || $this->deputyId <= 0) {
            throw new \InvalidArgumentException("Deputy ID deve ser um número positivo válido");
        }
    }

    public function handle(ExpenseRepositoryInterface $repo): void
    {
        try {

            Log::info("Buscando despesas para deputado ID: {$this->deputyId}");

            $url = "https://dadosabertos.camara.leg.br/api/v2/deputados/{$this->deputyId}/despesas";

            $response = Http::timeout(30)
                          ->retry(3, 1000)
                          ->get($url);

            if (!$response->successful()) {
                throw new \Exception("Erro na API: " . $response->status());
            }

            $items = $response->json('dados', []);

            if (empty($items)) {
                Log::info("Nenhuma despesa encontrada para deputado ID: {$this->deputyId}");
                return;
            }

            $processed = 0;
            $updated = 0;
            $created = 0;

            foreach ($items as $i) {
                try {
                    $this->createNewExpense($i, $repo);
                    $created++;
                    $processed++;
                } catch (\Exception $e) {
                    Log::error("Erro ao processar despesa individual do deputado {$this->deputyId}: " . $e->getMessage(), [
                        'expense_data' => $i
                    ]);
                    continue;
                }
            }

        } catch (\Exception $e) {
            Log::error("Erro ao processar despesas do deputado {$this->deputyId}: " . $e->getMessage());
            throw $e;
        }
    }

    private function createNewExpense(array $data, ExpenseRepositoryInterface $repo): void
    {

        $expense = new Expense();
        $this->mapExpenseData($expense, $data);

        Log::debug("Salvando despesa para deputado {$this->deputyId}", [
            'deputy_id' => $expense->deputy_id,
            'document_code' => $expense->document_code ?? 'N/A'
        ]);

        $repo->save($expense);
    }

    private function mapExpenseData(Expense $expense, array $data): void
    {

        $expense->deputy_id = $this->deputyId;

        Log::debug("Mapeando dados para deputado {$this->deputyId}", [
            'deputy_id_set' => $expense->deputy_id,
            'original_deputy_id' => $this->deputyId
        ]);

        $expense->year                 = $data['ano'] ?? null;
        $expense->month                = $data['mes'] ?? null;
        $expense->expense_type         = $data['tipoDespesa'] ?? null;
        $expense->document_code        = $data['codDocumento'] ?? null;
        $expense->document_type        = $data['tipoDocumento'] ?? null;
        $expense->document_type_code   = $data['codTipoDocumento'] ?? null;
        $expense->document_date        = isset($data['dataDocumento']) ? substr($data['dataDocumento'], 0, 10) : null;
        $expense->document_number      = $data['numDocumento'] ?? null;
        $expense->gross_value          = $data['valorDocumento'] ?? 0;
        $expense->document_url         = $data['urlDocumento'] ?? null;
        $expense->supplier_name        = $data['nomeFornecedor'] ?? null;
        $expense->supplier_cnpj_cpf    = $data['cnpjCpfFornecedor'] ?? null;
        $expense->net_value            = $data['valorLiquido'] ?? 0;
        $expense->glosa_value          = $data['valorGlosa'] ?? 0;
        $expense->reimbursement_number = $data['numRessarcimento'] ?: null;
        $expense->batch_code           = $data['codLote'] ?? null;
        $expense->installment          = $data['parcela'] ?? null;

        if (empty($expense->deputy_id)) {
            throw new \Exception("Deputy ID foi perdido durante o mapeamento");
        }
    }
}
