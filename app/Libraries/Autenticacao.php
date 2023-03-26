<?php

namespace App\libraries;

class Autenticacao
{
    private $usuario;
    private $usuarioModel;
    private $grupoUsuarioModel;


    public function __construct()
    {
        $this->usuarioModel = new \App\Models\UsuarioModel();
        $this->grupoUsuarioModel = new \App\Models\GrupoUsuarioModel();
    }

    /**
     * Método que realiza o login na aplicação
     *
     * @param string $email
     * @param string $password
     * @return boolean
     */
    public function login(string $email, string $password): bool
    {

        //buscar o user
        $usuario = $this->usuarioModel->buscaUsuarioPorEmail($email);

        if ($usuario === null) {


            return false;
        }

        //verificado a senha é valida
        if ($usuario->verificaPassword($password) == false) {


            return false;
        }

        //verificado se o user pode logar na apk
        if ($usuario->ativo == false) {


            return false;
        }

        //Logar user
        $this->logaUsuario($usuario);

        //O user pode logar 
        return true;
    }

    /**
     * Método de logout
     *
     * @return void
     */
    public function logout(): void
    {
        session()->destroy();
    }

    public function pegaUsuarioLogado()
    {

        if ($this->usuario === null) {

            $this->usuario = $this->pegaUsuarioDaSessao();
        }

        return $this->usuario;
    }

    /**
     * Metodo que ferifica se o usuario ta logado
     *
     * @return boolean
     */
    public function estaLogado(): bool
    {
        return $this->pegaUsuarioLogado() !== null;
    }

    /**
     * Método que insere na sessão o id do user
     *
     * @param object $usuario
     * @return void
     */
    private function logaUsuario(object $usuario): void
    {

        //recuperado a instância da sessão
        $session = session();

        //Antes de inserir o id do user na sessão...gerar um novo id da sessão
        //$session->regenerate();

        $_SESSION['__ci_last_regenerate'] = time(); // UTILIZAR essa instrução que o efeito é o mesmo e funciona perfeitamente.

        //Setado na sessão o Id do user
        $session->set('usuario_id', $usuario->id);
    }

    /**
     * Metodo que recupera da sessão e valida o usuario logado
     *
     * @return null|object
     */
    private function pegaUsuarioDaSessao()
    {
        if (session()->has('usuario_id') == false) {

            return null;
        }

        //busca o user na db
        $usuario = $this->usuarioModel->find(session()->get('usuario_id'));

        //validado se o user existe ou se tem permi na apk
        if ($usuario == null || $usuario->ativo == false) {

            return null;
        }

        //Definido as permins do user logado
        $usuario = $this->definePermissoesDoUsuarioLogado($usuario);

        //retornado o object $user
        return $usuario;
    }

    /**
     * Método que verifica se o user logado (session()->get('usuario_id')) está associado ao gp de admin
     *
     * @return boolean
     */
    private function isAdmin(): bool
    {
        // Definir o id do grupo admin.
        // não esquer que o ID jamais poderá ser alterado.. defender(ta defendido) no controller
        $grupoAdmin = 1;

        // Verificar se o user logado está no grupo admin
        $administrador = $this->grupoUsuarioModel->usuarioEstaNoGrupo($grupoAdmin, session()->get('usuario_id'));

        //verificar se foi escontrado o registro
        if ($administrador == null) {

            return false;
        }

        //retornar true..ou seja o usuario faz parte do gp admin
        return true;
    }

    /**
     * Método que verifica se o user logado (session()->get('usuario_id')) está associado ao gp de clientes
     *
     * @return boolean
     */
    private function isCliente(): bool
    {
        // Definir o id do grupo clienmte.
        // não esquer que o ID jamais poderá ser alterado.. defender(ta defendido) no controller
        $grupoCliente = 2;

        // Verificar se o user logado está no grupo admin
        $cliente = $this->grupoUsuarioModel->usuarioEstaNoGrupo($grupoCliente, session()->get('usuario_id'));

        //verificar se foi escontrado o registro
        if ($cliente == null) {

            return false;
        }

        //retornar true..ou seja o usuario faz parte do gp admin
        return true;
    }

    /**
     * Metodo q define as permis q o user logado tem
     * usado exclusiv no metodo pegaUsuarioDaSessao()
     *
     * @param object $usuario
     * @return object
     */
    private function definePermissoesDoUsuarioLogado(object $usuario): object
    {

        // definido se o user logado é admin..esse atributo será colocado no metodo temPermissaoPara() na Entity user
        $usuario->is_admin = $this->isAdmin();

        // Se for admin...não é cliente
        if ($usuario->is_admin == true) {

            $usuario->is_cliente = false;
        } else {
            //Nesse point...verficar se o user logado é cliente..visto q não é admin
            $usuario->is_cliente = $this->isCliente();
        }

        if ($usuario->is_admin == false && $usuario->is_cliente == false) {

            $usuario->permissoes = $this->recuperaPermissoesDoUsuarioLogado();
        }

        return $usuario;
    }

    /**
     * Metodo que retorna as permissões do user logado
     *
     * @return array
     */
    private function recuperaPermissoesDoUsuarioLogado(): array
    {

        $permissoesDoUsuario = $this->usuarioModel->recuperaPermissoesDoUsuarioLogado(session()->get('usuario_id'));

        return array_column($permissoesDoUsuario, 'permissao');
    }
}
