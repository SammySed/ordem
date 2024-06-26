<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class Cliente extends Entity
{
    protected $dates   = [
        'criado_em',
        'atualizado_em',
        'deletado_em'
    ];




    public function exibeSituacao()
    {

        if ($this->deletado_em != null) {

            //cliente excluido

            $icone = '<span class="text-white">Excluído</span>&nbsp;<i class="fa fa-undo"></i>&nbsp;Desfazer';

            $situacao = anchor("clientes/desfazerexclusao/$this->id", $icone, ['class' => 'btn btn-outline-succes btn-sm']);

            return $situacao;
        }

        $situacao = '<span class="text-white"><i class="fa fa-thumbs-up" aria-hidden="true"></i></i>&nbsp;Disponível';

        return $situacao;
    }
}
