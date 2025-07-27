<?php

namespace App\Domain\Entities;

class Expense
{
    public ?int $id = null;
    public ?int $deputyId = null;
    public ?int $ano = null;
    public ?int $mes = null;
    public ?string $tipoDespesa = null;
    public ?int $codDocumento = null;
    public ?string $tipoDocumento = null;
    public ?int $codTipoDocumento = null;
    public ?\DateTime $dataDocumento = null;
    public ?int $numDocumento = null;
    public ?float $valorDocumento = null;
    public ?string $urlDocumento = null;
    public ?string $nomeFornecedor = null;
    public ?string $cnpjCpfFornecedor = null;
    public ?float $valorLiquido = null;
    public ?float $valorGlosa = null;
    public ?int $numRessarcimento = null;
    public ?int $codLote = null;
    public ?int $parcela = null;
}
