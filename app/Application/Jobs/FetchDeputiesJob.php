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

class FetchDeputiesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(DeputyRepositoryInterface $repo): void
    {
        $response = Http::get('https://dadosabertos.camara.leg.br/api/v2/deputados');
        $items    = $response->json('dados', []);

        foreach ($items as $i) {
            $d = new Deputy();
            $d->id              = $i['id'];
            $d->uri             = $i['uri'];
            $d->name            = $i['nome'];
            $d->party_abbr      = $i['siglaPartido'];
            $d->party_uri       = $i['uriPartido'];
            $d->state_abbr      = $i['siglaUf'];
            $d->legislature_id  = $i['idLegislatura'];
            $d->photo_url       = $i['urlFoto'] ?? null;
            $d->email           = $i['email'] ?? null;

            $repo->save($d);
        }
    }
}
