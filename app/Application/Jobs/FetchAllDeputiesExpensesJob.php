<?php

// Arquivo: app/Application/Jobs/FetchAllDeputiesExpensesJob.php
namespace App\Application\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Domain\Repositories\DeputyRepositoryInterface;
use Illuminate\Support\Facades\Log;

class FetchAllDeputiesExpensesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 3600; // 1 hora de timeout
    public $tries = 3;
    public $backoff = 60; // 1 minuto entre tentativas

    // SEM CONSTRUTOR - este job não recebe parâmetros

    public function handle(DeputyRepositoryInterface $deputyRepo): void
    {
        Log::info('Iniciando busca de despesas de todos os deputados');

        // Busca todos os deputados do banco
        $deputies = $deputyRepo->getAll();

        Log::info("Encontrados {$deputies->count()} deputados para processar");

        $processed = 0;
        $errors = 0;

        foreach ($deputies as $deputy) {
            try {
                // Despacha um job individual para cada deputado
                FetchDeputyExpensesJob::dispatch($deputy->id);
                $processed++;

                Log::info("Job despachado para deputado {$deputy->name} (ID: {$deputy->id})");

                // Pequena pausa para não sobrecarregar a API
                usleep(100000); // 0.1 segundo

            } catch (\Exception $e) {
                $errors++;
                Log::error("Erro ao processar deputado {$deputy->name} (ID: {$deputy->id}): " . $e->getMessage());
            }
        }

        Log::info("Processamento concluído. Jobs despachados: {$processed}, Erros: {$errors}");
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('Job FetchAllDeputiesExpensesJob falhou: ' . $exception->getMessage());
    }
}
