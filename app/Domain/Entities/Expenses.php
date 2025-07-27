<?php

namespace App\Domain\Entities;

class Expenses
{
    public int $ano;
    public int $mes;
    public string $tipoDespesa;
    public int $codDocumento;
    public string $tipoDocumento;
    public int $codTipoDocumento;
    public \DateTime $dataDocumento;
    public int $numDocumento;
    public float $valorDocumento;
    public string $urlDocumento;
    public string $nomeFornecedor;
    public string $cnpjCpfFornecedor;
    public float $valorLiquido;
    public float $valorGlosa;
    public int $numRecebimento;
    public int $codLote;
    public int $parcela;

}
