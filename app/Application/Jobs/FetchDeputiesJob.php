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
            $d->nome            = $i['nome'] ?? '';
            $d->siglaPartido    = $i['siglaPartido'];
            $d->siglaUf         = $i['siglaUf'];
            $d->urlFoto         = $i['urlFoto'] ?? null;
            $d->email           = $i['email'] ?? null;
            $d->uri             = $i['uri'] ?? null;
            $d->uriPartido      = $i['uriPartido'] ?? null;
            $d->idLegislatura   = $i['idLegislatura'] ?? null;

            $repo->save($d);
        }
    }
}
