<?php

namespace App\Models;

use CodeIgniter\Model;

class ItemModel extends Model
{
    protected $table            = 'itens';
    protected $returnType       = 'App\Entities\Item';
    protected $useSoftDeletes   = true;
    protected $allowedFields    = [
        'codigo_interno',
        'nome',
        'marca',
        'modelo',
        'preco_custo',
        'preco_venda',
        'estoque',
        'controla_estoque',
        'tipo',
        'ativo',
        'descricao',
    ];

    // Dates    
    protected $useTimestamps = true;
    protected $createdField  = 'criado_em';
    protected $updatedField  = 'atualizado_em';
    protected $deletedField  = 'deletado_em';

    // Validation
    protected $validationRules = [
        'nome'        => 'required|max_length[120]|is_unique[itens.nome,id,{id}]', // Não pode ter espaços
        'preco_venda'     => 'required',
        'descricao' => 'required',
    ];
    protected $validationMessages = [];

    // Callbacks    
    protected $beforeInsert   = ['removeVirgulaValores'];
    protected $beforeUpdate   = ['removeVirgulaValores'];

    protected function removeVirgulaValores(array $data)
    {

        if (isset($data['data']['preco_custo'])) {

            $data['data']['preco_custo'] = str_replace(",", "", $data['data']['preco_custo']);
        }

        if (isset($data['data']['preco_venda'])) {

            $data['data']['preco_venda'] = str_replace(",", "", $data['data']['preco_venda']);
        }

        return $data;
    }

    /**
     * Metodo que gera o codigo interno do item na hr de cadastrar
     *
     * @return string
     */
    public function geraCodigoInternoItem(): string
    {

        do {

            $codigoInterno = random_string('numeric', 15);

            $this->where('codigo_interno', $codigoInterno);
        } while ($this->countAllResults() > 1);

        return $codigoInterno;
    }
}
