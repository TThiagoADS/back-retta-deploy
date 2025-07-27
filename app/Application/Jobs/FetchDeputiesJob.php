<?php

namespace App\Application\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Domain\Repositories\DeputyRepositoryInterface;
use App\Domain\Entities\Deputy;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FetchDeputiesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(DeputyRepositoryInterface $repo): void
    {
        try {
            $response = Http::get('https://dadosabertos.camara.leg.br/api/v2/deputados');
            $items = $response->json('dados', []);

            foreach ($items as $i) {
                // Log para debug
                Log::info("Processando deputado", ['id' => $i['id'], 'nome' => $i['nome'] ?? 'sem nome']);

                // Verificar se o nome existe
                if (empty($i['nome'])) {
                    Log::warning("Deputado sem nome encontrado", ['data' => $i]);
                    continue; // Pular este deputado
                }

                $d = new Deputy();
                $d->id = $i['id'];

                // Corrigir o mapeamento - usar as propriedades corretas
                $d->name = $i['nome']; // Era 'nome', deve ser 'name'
                $d->party_abbr = $i['siglaPartido']; // Era 'siglaPartido'
                $d->state_abbr = $i['siglaUf']; // Era 'siglaUf'
                $d->photo_url = $i['urlFoto'] ?? null; // Era 'urlFoto'
                $d->email = $i['email'] ?? null;
                $d->uri = $i['uri'] ?? null;
                $d->party_uri = $i['uriPartido'] ?? null; // Era 'uriPartido'
                $d->legislature_id = $i['idLegislatura'] ?? null; // Era 'idLegislatura'

                $repo->save($d);
            }
        } catch (\Exception $e) {
            Log::error("Erro no FetchDeputiesJob: " . $e->getMessage());
            throw $e;
        }
    }
}
