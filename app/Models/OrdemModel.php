<?php

namespace App\Models;

use CodeIgniter\Model;

class OrdemModel extends Model
{
    protected $table            = 'ordens';
    protected $returnType       = 'App\Entities\Ordem';
    protected $useSoftDeletes   = true;
    protected $allowedFields    = [
        'cliente_id',
        'codigo',
        'forma_pagamento',
        'situacao',
        'itens',
        'valor_servicos',
        'valor_desconto',
        'valor_ordem',
        'observacoes',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $createdField  = 'criado_em';
    protected $updatedField  = 'atualizado_em';
    protected $deletedField  = 'deletado_em';

    // Validation
    protected $validationRules = [
        'cliente_id'        => 'required',
        'codigo'            => 'required',
    ];

    protected $validationMessages = [];

    public function recuperaOrdens()
    {

        $atributos = [
            'ordens.codigo',
            'ordens.criado_em',
            'ordens.situacao',
            'clientes.nome',

        ];

        return $this->select($atributos)
            ->join('clientes', 'clientes.id = ordens.cliente_id')
            ->orderBy('ordens.situacao', 'ASC')
            ->withDeleted(true)
            ->findAll();
    }

    /**
     * Metodo responsavel, recuperar ordem
     *
     * @param string|null $codigo
     * @return object|PageNotFoundException
     */
    public function buscaOrdemOu404(string $codigo = null)
    {

        if ($codigo === null) {

            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Não encontramos o ordem $codigo");
        }

        $atributos = [
            'ordens.*',
            'u_aber.id AS usuario_abertura_id',
            'u_aber.nome AS usuario_abertura',

            'u_ence.id AS usuario_encerramento_id',
            'u_ence.nome AS usuario_encerramento',

            'clientes.usuario_id AS cliente_usuario_id',
            'clientes.nome',
            'clientes.telefone',
            'clientes.email',

        ];

        $ordem = $this->select($atributos)
            ->join('ordens_responsaveis', 'ordens_responsaveis.ordem_id = ordens.id')
            ->join('clientes', 'clientes.id = ordens.cliente_id')
            ->join('usuarios AS u_cliente', 'u_cliente.id = clientes.usuario_id')
            ->join('usuarios AS u_aber', 'u_aber.id = ordens_responsaveis.usuario_abertura_id')
            ->join('usuarios AS u_ence', 'u_ence.id = ordens_responsaveis.usuario_encerramento_id', 'LEFT')
            ->where('ordens.codigo', $codigo)
            ->withDeleted(true)
            ->first();

        if ($ordem === null) {

            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Não encontramos o ordem $codigo");
        }

        return $ordem;
    }

    /**
     * Metodo que gera o codigo interno da ordem de serv na hr de cadastrar
     *
     * @return string
     */
    public function geraCodigoOrdem(): string
    {

        do {

            $codigo = strtoupper(random_string('alnum', 20));

            $this->select('codigo')->where('codigo', $codigo);
        } while ($this->countAllResults() > 1);

        return $codigo;
    }
}
