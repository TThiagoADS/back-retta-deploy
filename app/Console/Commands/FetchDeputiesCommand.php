<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Application\Jobs\FetchDeputiesJob;

class FetchDeputiesCommand extends Command
{
    protected $signature = 'deputies:fetch-list';

    protected $description = 'Atualiza a lista de deputados';

    public function handle()
    {
        $this->info('Atualizando lista de deputados...');

        FetchDeputiesJob::dispatch();

        $this->info('Job despachado com sucesso!');
    }
}
