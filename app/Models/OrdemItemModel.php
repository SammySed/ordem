<?php

namespace App\Models;

use CodeIgniter\Model;

class OrdemItemModel extends Model
{

    protected $table            = 'ordens_itens';
    protected $returnType       = 'object';
    protected $allowedFields    = [
        'ordem_id',
        'item_id',
        'item_quantidade',
    ];

    /**
     * Metodo responsavel por recuper os itens da ordem de serv
     *
     * @param integer $ordem_id
     * @return array|null
     */
    public function recuperaItensDaOrdem(int $ordem_id)
    {

        $atributos = [
            'itens.id',
            'itens.nome',
            'itens.preco_venda',
            'itens.tipo',
            'ordens_itens.id AS id_principal',
            'ordens_itens.item_quantidade',
        ];

        return $this->select($atributos)
            ->join('itens', 'itens.id = ordens_itens.item_id')
            ->where('ordens_itens.ordem_id', $ordem_id)
            ->groupBy('itens.nome')
            ->orderBy('itens.tipo', 'ASC')
            ->findAll();
    }
}
