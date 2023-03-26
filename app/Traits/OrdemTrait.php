<?php

namespace App\Traits;

trait OrdemTrait
{
    /**
     * Metodo q prepara a exibição dos possiveis itens
     *
     * @param object $ordem
     * @return object
     */
    public function preparaItensDaOrdem(object $ordem): object
    {

        $ordemItemModel = new \App\Models\OrdemItemModel();

        if ($ordem->situacao === 'aberta') {

            $ordemItens = $ordemItemModel->recuperaItensDaOrdem($ordem->id);

            $ordem->itens = (!empty($ordemItens) ? $ordemItens : null);

            return $ordem;
        }

        //ordem não esta mais aberta
        if ($ordem->itens !== null) {

            $ordem->itens = unserialize($ordem->itens);
        }

        return $ordem;
    }
}
