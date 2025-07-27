<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Application\Jobs\FetchAllDeputiesExpensesJob;

class FetchExpensesCommand extends Command
{
    protected $signature = 'deputies:fetch-expenses
                           {--deputy-id= : ID específico do deputado}
                           {--force : Força a execução mesmo se já rodou hoje}';

    protected $description = 'Busca despesas dos deputados';

    public function handle()
    {
        if ($this->option('deputy-id')) {
            $deputyId = (int) $this->option('deputy-id');
            $this->info("Buscando despesas para deputado ID: {$deputyId}");

            \App\Application\Jobs\FetchAllDeputiesExpensesJob::dispatch($deputyId);

            $this->info('Job despachado com sucesso!');
        } else {
            $this->info('Iniciando busca de despesas para todos os deputados...');

            FetchAllDeputiesExpensesJob::dispatch();

            $this->info('Job principal despachado! Verifique os logs para acompanhar o progresso.');
        }
    }
}
