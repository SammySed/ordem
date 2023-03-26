<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Entities\Grupo;

class Grupos extends BaseController
{

    private $grupoModel;
    private $grupoPermissaoModel;
    private $permissaoModel;

    public function __construct()
    {
        $this->grupoModel = new \App\Models\GrupoModel();
        $this->grupoPermissaoModel = new \App\Models\GrupoPermissaoModel();
        $this->permissaoModel = new \App\Models\PermissaoModel();
    }


    public function index()
    {

        $data = [
            'titulo' => 'Listando os grupos de acesso ao sistema',
        ];

        return view('Grupos/index', $data);
    }

    public function recuperaGrupos()
    {

        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }

        $atributos = [
            'id',
            'nome',
            'descricao',
            'exibir',
            'deletado_em'
        ];

        $grupos = $this->grupoModel->select($atributos)
            ->withDeleted(true)
            ->orderBy('id', 'DESC')
            ->findAll();


        // Receberá o array de objetos de Grupos
        $data = [];

        foreach ($grupos as $grupo) {

            $data[] = [
                'nome' => anchor("grupos/exibir/$grupo->id", esc($grupo->nome), 'title="Exibir grupo  ' . esc($grupo->nome) . ' "'),
                'descricao' => esc($grupo->descricao),
                'exibir' => $grupo->exibeSituacao(),
            ];
        }

        $retorno = [
            'data' => $data,
        ];

        return $this->response->setJSON($retorno);
    }

    public function exibir(int $id = null)
    {

        $grupo = $this->buscaGrupoOu404($id);



        $data = [
            'titulo' => "Detalhando o grupo de acesso " . esc($grupo->nome),
            'grupo' => $grupo,
        ];

        return view('Grupos/exibir', $data);
    }

    public function criar()
    {

        $grupo = new Grupo;



        $data = [
            'titulo' => "Criando novo grupo de acesso ",
            'grupo' => $grupo,
        ];

        return view('Grupos/criar', $data);
    }

    public function cadastrar()
    {

        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }


        // Envio o hash do token do form
        $retorno['token'] = csrf_hash();

        // Recupero o post da requisição
        $post = $this->request->getPost();

        // criar novo objeto da entidade grupo
        $grupo = new Grupo($post);


        if ($this->grupoModel->save($grupo)) {

            $btnCriar = anchor("grupos/criar", 'Cadastrar novo grupo de acesso', ['class' => 'btn btn-danger mt-2']);

            session()->setFlashdata('sucesso', "Dados salvos com sucesso!<br> $btnCriar");

            // Retornar o ultimo ID inserido na table grupo..grupo recem criado
            $retorno['id'] = $this->grupoModel->getInsertID();

            return $this->response->setJSON($retorno);
        }

        // Retornamos os erros de validação
        $retorno['erro'] = 'Por favor verifique os erros e tente novamente';
        $retorno['erros_model'] = $this->grupoModel->errors();

        // Retorno para o ajax request
        return $this->response->setJSON($retorno);
    }

    public function editar(int $id = null)
    {

        $grupo = $this->buscaGrupoOu404($id);

        if ($grupo->id < 3) {
            return redirect()->back()->with('atencao', 'O grupo <b>' . esc($grupo->nome) . '</b> não pode ser editado ou excluído, conforme detalhado na exibição do mesmo.');
        }

        $data = [
            'titulo' => "Editando o grupo de acesso " . esc($grupo->nome),
            'grupo' => $grupo,
        ];

        return view('Grupos/editar', $data);
    }

    public function atualizar()
    {

        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }


        // Envio o hash do token do form
        $retorno['token'] = csrf_hash();

        // Recupero o post da requisição
        $post = $this->request->getPost();


        //Validar existência do User
        $grupo = $this->buscaGrupoOu404($post['id']);


        //Grupo admin e cliente não pode ser editado
        if ($grupo->id < 3) {

            $retorno['erro'] = 'Por favor verifique os erros e tente novamente';
            $retorno['erros_model'] = ['grupo'  => 'O grupo <b class="text-white">' . esc($grupo->nome) . '</b> não pode ser editado ou excluído, conforme detalhado na exibição do mesmo.'];
            return $this->response->setJSON($retorno);
        }


        // Preencher os atributos do grupo com os val do POST
        $grupo->fill($post);

        if ($grupo->hasChanged() == false) {
            $retorno['info'] = 'Não há dados para serem atualizados';
            return $this->response->setJSON($retorno);
        }

        if ($this->grupoModel->protect(false)->save($grupo)) {

            session()->setFlashdata('sucesso', 'Dados salvos com sucesso!');

            return $this->response->setJSON($retorno);
        }

        // Retornamos os erros de validação
        $retorno['erro'] = 'Por favor verifique os erros e tente novamente';
        $retorno['erros_model'] = $this->grupoModel->errors();

        // Retorno para o ajax request
        return $this->response->setJSON($retorno);
    }

    public function excluir(int $id = null)
    {

        $grupo = $this->buscaGrupoOu404($id);

        if ($grupo->id < 3) {
            return redirect()->back()->with('atencao', 'O grupo <b>' . esc($grupo->nome) . '</b> não pode ser editado ou excluído, conforme detalhado na exibição do mesmo.');
        }

        if ($grupo->deletado_em != null) {
            return redirect()->back()->with('info', "Esse grupo já encontra-se excluído!");
        }

        if ($this->request->getMethod() === 'post') {

            // exclui o grupo
            $this->grupoModel->delete($grupo->id);


            return redirect()->to(site_url("grupos"))->with('sucesso', 'Grupo ' . esc($grupo->nome) . ' excluido com sucesso!');
        }

        $data = [
            'titulo' => "Excluindo o grupo de acesso " . esc($grupo->nome),
            'grupo' => $grupo,
        ];

        return view('Grupos/excluir', $data);
    }

    public function desfazerExclusao(int $id = null)
    {

        $grupo = $this->buscaGrupoOu404($id);

        if ($grupo->deletado_em == null) {
            return redirect()->back()->with('info', "Apenas grupos excluídos podem ser recuperados!");
        }

        $grupo->deletado_em = null;
        $this->grupoModel->protect(false)->save($grupo);


        return redirect()->back()->with('Sucesso', 'Grupo ' . esc($grupo->nome) . ' excluido com sucesso!');
    }

    public function permissoes(int $id = null)
    {

        $grupo = $this->buscaGrupoOu404($id);

        //Grupo Admin
        if ($grupo->id == 1) {
            return redirect()->back()->with('info', 'Não é necessário atribuir ou remover permissões de acesso para o grupo <b>' . esc($grupo->nome) . '</b>, pois esse grupo é Administrador.');
        }

        // Grupo de Clientes
        if ($grupo->id == 2) {
            return redirect()->back()->with('info', 'Não é necessário atribuir ou remover permissões de acesso para o grupo de Clientes.');
        }

        //garantir recuperacao de permi quando não for admin ou cliente
        if ($grupo->id > 2) {
            $grupo->permissoes = $this->grupoPermissaoModel->recuperaPermissoesDoGrupo($grupo->id, 5);
            $grupo->pager = $this->grupoPermissaoModel->pager;
        }


        $data = [
            'titulo' => "Gerenciando as permissões do grupo de acesso " . esc($grupo->nome),
            'grupo' => $grupo,
        ];

        if (!empty($grupo->permissoes)) {

            $permissoesExistentes = array_column($grupo->permissoes, 'permissao_id');

            $data['permissoesDisponiveis'] = $this->permissaoModel->whereNotIn('id', $permissoesExistentes)->findAll();
        } else {

            //Se cair no else..grupo não possui permissão
            $data['permissoesDisponiveis'] = $this->permissaoModel->findAll();
        }

        return view('Grupos/permissoes', $data);
    }

    public function salvarPermissoes()
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }


        // Envio o hash do token do form
        $retorno['token'] = csrf_hash();

        // Recupero o post da requisição
        $post = $this->request->getPost();


        //Validar existência do User
        $grupo = $this->buscaGrupoOu404($post['id']);

        if (empty($post['permissao_id'])) {
            // Retornamos os erros de validação
            $retorno['erro'] = 'Por favor verifique os erros e tente novamente';
            $retorno['erros_model'] = ['permissao_id' => 'Escolha uma ou mais permissões para salvar'];

            // Retorno para o ajax request
            return $this->response->setJSON($retorno);
        }

        // Receberá as permis do POST
        $permissaoPush = [];

        foreach ($post['permissao_id'] as $permissao) {
            array_push($permissaoPush, [
                'grupo_id' => $grupo->id,
                'permissao_id' => $permissao
            ]);
        }

        $this->grupoPermissaoModel->insertBatch(($permissaoPush));

        session()->setFlashdata('sucesso', 'Dados salvos com sucesso!');

        return $this->response->setJSON($retorno);
    }

    public function removePermissao(int $principal_id = null)
    {

        if ($this->request->getMethod() === 'post') {

            // exclui a permissão (principal_id)
            $this->grupoPermissaoModel->delete($principal_id);


            return redirect()->back()->with('sucesso', 'Permissão removida com sucesso!');
        }

        //não é POST
        return redirect()->back();
    }




    /**
     * Método que recupera o grupo
     *
     * @param integer $id
     * @return Exception|object
     */
    private function buscaGrupoOu404(int $id = null)
    {
        if (!$id || !$grupo = $this->grupoModel->withDeleted(true)->find($id)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Não encontramos o grupo de acesso $id");
        }
        return $grupo;
    }
}
