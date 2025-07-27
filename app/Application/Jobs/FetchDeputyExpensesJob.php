<?php

// Arquivo: app/Application/Jobs/FetchDeputyExpensesJob.php
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

    public $timeout = 300; // 5 minutos
    public $tries = 3;
    public $backoff = 30;

    public function __construct(
        private int $deputyId
    ) {}

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
                // Se você não tem o método findByUniqueFields ainda,
                // pode comentar essa parte e usar só a criação por enquanto

                // Cria nova despesa (versão simples)
                $this->createNewExpense($i, $repo);
                $created++;
                $processed++;
            }

            Log::info("Deputado {$this->deputyId}: {$processed} despesas processadas, {$created} criadas, {$updated} atualizadas");

        } catch (\Exception $e) {
            Log::error("Erro ao processar despesas do deputado {$this->deputyId}: " . $e->getMessage());
            throw $e;
        }
    }

    private function createNewExpense(array $data, ExpenseRepositoryInterface $repo): void
    {
        $expense = new Expense();
        $this->mapExpenseData($expense, $data);
        $repo->save($expense);
    }

    private function mapExpenseData(Expense $expense, array $data): void
    {
        $expense->deputy_id            = $this->deputyId;
        $expense->year                 = $data['ano'];
        $expense->month                = $data['mes'];
        $expense->expense_type         = $data['tipoDespesa'];
        $expense->document_code        = $data['codDocumento'];
        $expense->document_type        = $data['tipoDocumento'];
        $expense->document_type_code   = $data['codTipoDocumento'];
        $expense->document_date        = substr($data['dataDocumento'], 0, 10);
        $expense->document_number      = $data['numDocumento'];
        $expense->gross_value          = $data['valorDocumento'];
        $expense->document_url         = $data['urlDocumento'];
        $expense->supplier_name        = $data['nomeFornecedor'];
        $expense->supplier_cnpj_cpf    = $data['cnpjCpfFornecedor'];
        $expense->net_value            = $data['valorLiquido'];
        $expense->glosa_value          = $data['valorGlosa'];
        $expense->reimbursement_number = $data['numRessarcimento'] ?: null;
        $expense->batch_code           = $data['codLote'];
        $expense->installment          = $data['parcela'];
    }

    public function failed(\Throwable $exception): void
    {
        Log::error("Job FetchDeputyExpensesJob falhou para deputado {$this->deputyId}: " . $exception->getMessage());
    }
}
