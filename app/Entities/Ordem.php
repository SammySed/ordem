<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class Ordem extends Entity
{

    protected $dates   = [
        'criado_em',
        'atualizado_em',
        'deletado_em'
    ];


    public function exibeSituacao()
    {

        if ($this->deletado_em != null) {

            //ordem excluido

            if (url_is('relatorios*')) {

                return '<span class="text-white">Excluído</span>&nbsp;<i class="fa fa-undo"></i>&nbsp;Desfazer';
            }

            $icone = '<span class="text-white">Excluído</span>';

            $situacao = anchor("ordens/desfazerexclusao/$this->codigo", $icone, ['class' => 'btn btn-outline-succes btn-sm']);

            return $situacao;
        } else {

            if ($this->situacao === 'aberta') {

                return '<span class="text-warning"><i class="fa fa-unlock" aria-hidden="true"></i></i>&nbsp;' . ucfirst($this->situacao);
            }

            if ($this->situacao === 'aberta') {

                return '<span class="text-white"><i class="fa fa-lock" aria-hidden="true"></i></i>&nbsp;' . ucfirst($this->situacao);
            }

            if ($this->situacao === 'aguardando') {

                return '<span class="text-warning"><i class="fa fa-clock-o" aria-hidden="true"></i></i>&nbsp;' . ucfirst($this->situacao);
            }

            if ($this->situacao === 'nao_pago') {

                return '<span class="text-warning"><i class="fa fa-clock-o" aria-hidden="true"></i></i>&nbsp; Não pago';
            }

            if ($this->situacao === 'cancelada') {

                return '<span class="text-white"><i class="fa fa-ban" aria-hidden="true"></i></i>&nbsp;' . ucfirst($this->situacao);
            }
        }
    }
}
