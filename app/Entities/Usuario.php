<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

use App\Libraries\Token;

class Usuario extends Entity
{
    protected $dates   = [
        'criado_em',
        'atualizado_em',
        'deletado_em'
    ];

    public function exibeSituacao()
    {

        if ($this->deletado_em != null) {
            //Usuario excluido

            $icone = '<span class="text-white">Excluído</span>&nbsp;<i class="fa fa-undo"></i>&nbsp;Desfazer';

            $situacao = anchor("usuarios/desfazerexclusao/$this->id", $icone, ['class' => 'btn btn-outline-succes btn-sm']);

            return $situacao;
        }

        //<i class="fa fa-unlock text-success"></i>&nbsp;Ativo' : '<i class="fa fa-lock text-warning"></i>&nbsp;Inativo')
        if ($this->ativo == true) {

            return '<i class="fa fa-unlock text-success"></i>&nbsp;Ativo';
        }

        if ($this->ativo == false) {

            return '<i class="fa fa-lock text-warning"></i>&nbsp;Inativo';
        }
    }

    /**
     * Metodo que verifica se a senha é valida
     *
     * @param string $password
     * @return boolean
     */
    public function verificaPassword(string $password): bool
    {
        return password_verify($password, $this->password_hash);
    }

    /**
     * Metodo q valida se o usuario logado possui a permissao para visualizar / acessar determinada rota
     *
     * @param string $permissao
     * @return boolean
     */
    public function temPermissaoPara(string $permissao): bool
    {

        // Se o user for admin, 
        if ($this->is_admin == true) {

            return true;
        }

        if (empty($this->permissoes)) {

            return false;
        }

        if (in_array($permissao, $this->permissoes) == false) {

            return false;
        }

        return true;
    }

    /**
     * Metodo q inicia a recuperação de senha
     *
     * @return void
     */
    public function iniciaPasswordReset(): void
    {
        $token = new Token();

        $this->reset_token = $token->getValue();

        $this->reset_hash = $token->getHash();

        $this->reset_expira_em = date('Y-m-d H:i:s', time() + 7200);
    }

    /**
     * Metodo que finaliza o processo de redefinição de senha
     *
     * @return void
     */
    public function finalizaPasswordReset(): void
    {

        $this->reset_hash = null;
        $this->reset_expira_em = null;
    }
}
