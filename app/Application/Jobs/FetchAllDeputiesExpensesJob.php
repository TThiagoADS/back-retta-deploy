<?php

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

    public $timeout = 3600;
    public $tries = 3;
    public $backoff = 60;

    public function handle(DeputyRepositoryInterface $deputyRepo): void
    {
        try {
            $deputies = $deputyRepo->getAll();

            if ($deputies === null) {
                Log::error('Repository retornou NULL');
                return;
            }

            if ($deputies->isEmpty()) {
                Log::warning('Repository retornou coleção vazia');
                return;
            }

            $processed = 0;
            $errors = 0;

            foreach ($deputies as $deputy) {
                try {

                    if (!class_exists('App\Application\Jobs\FetchDeputyExpensesJob')) {
                        Log::error('Classe FetchDeputyExpensesJob não encontrada');
                        $errors++;
                        continue;
                    }

                    FetchDeputyExpensesJob::dispatch($deputy->id);
                    $processed++;

                    usleep(100000);

                } catch (\Exception $e) {
                    $errors++;
                }
            }

        } catch (\Exception $e) {
            Log::error("Erro geral no job: " . $e->getMessage());
            Log::error("Stack trace: " . $e->getTraceAsString());
            throw $e;
        }
    }
}
