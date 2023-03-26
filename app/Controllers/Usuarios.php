<?php

namespace App\Controllers;

use App\Controllers\BaseController;

use App\Entities\Usuario;
use App\Models\GrupoUsuarioModel;

class Usuarios extends BaseController
{
    private $usuarioModel;
    private $grupoUsuarioModel;
    private $grupoModel;

    public function __construct()
    {
        $this->usuarioModel = new \App\Models\UsuarioModel();
        $this->grupoUsuarioModel = new \App\Models\GrupoUsuarioModel();
        $this->grupoModel = new \App\Models\GrupoModel();
    }

    public function index()
    {

        // dd(usuario_logado());

        $data = [
            'titulo' => 'Listando os usuário do sistema',
        ];

        return view('Usuarios/index', $data);
    }

    public function recuperaUsuarios()
    {

        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }

        $atributos = [
            'id',
            'nome',
            'email',
            'ativo',
            'imagem',
            'deletado_em'
        ];

        $usuarios = $this->usuarioModel->select($atributos)
            ->asArray()
            ->withDeleted(true)
            ->orderBy('id', 'DESC')
            ->findAll();


        $gruposUsuarios = $this->grupoUsuarioModel->recuperaGrupos();

        foreach ($usuarios as $key => $usuario) {

            foreach ($gruposUsuarios as $grupo) {

                if ($usuario['id'] === $grupo['usuario_id'])

                    $usuarios[$key]['grupos'][] = $grupo['nome'];
            }
        }



        // Receberá o array de objetos de usuários
        $data = [];

        foreach ($usuarios as $usuario) {

            //Definido o caminho da img do user
            if ($usuario['imagem'] != null) {
                //Tem Imagem

                $imagem = [
                    'src' => site_url("usuarios/imagem/" . $usuario['imagem']),
                    'class' => 'rounded-circle img-fluid',
                    'alt' => esc($usuario['nome']),
                    'width' => '50',

                ];
            } else {
                //Não tem imagem
                $imagem = [
                    'src' => site_url("recursos/img/usuario_sem_imagem.png"),
                    'class' => 'rounded-circle img-fluid',
                    'alt' => "Usuário sem imagem",
                    'width' => '50',

                ];
            }


            if (isset($usuario['grupos']) === false) {

                $usuario['grupos'] = ['<span class="text-warning">Sem grupos de acesso</span>'];
            }

            $usuario = new Usuario($usuario);

            $data[] = [
                'imagem' => $usuario->imagem = img($imagem),
                'nome' => anchor("usuarios/exibir/" . $usuario->id, esc($usuario->nome), 'title="Exibir usuário  ' . esc($usuario->nome) . ' "'),
                'email' => esc($usuario->email),
                'grupos' => ($usuario->grupos),
                'ativo' => $usuario->exibeSituacao(),

            ];
        }

        $retorno = [
            'data' => $data,
        ];

        return $this->response->setJSON($retorno);
    }

    public function criar(int $id = null)
    {

        $usuario = new Usuario();

        $data = [
            'titulo' => "Criando novo usuário",
            'usuario' => $usuario,
        ];

        return view('Usuarios/criar', $data);
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

        // criar novo objeto da entidade usuario
        $usuario = new Usuario($post);


        if ($this->usuarioModel->protect(false)->save($usuario)) {

            $btnCriar = anchor("usuarios/criar", 'Cadastrar novo usuário', ['class' => 'btn btn-danger mt-2']);

            session()->setFlashdata('sucesso', "Dados salvos com sucesso!<br> $btnCriar");

            // Retornar o ultimo ID inserido na table ususario..usuario recem criado
            $retorno['id'] = $this->usuarioModel->getInsertID();

            return $this->response->setJSON($retorno);
        }

        // Retornamos os erros de validação
        $retorno['erro'] = 'Por favor verifique os erros e tente novamente';
        $retorno['erros_model'] = $this->usuarioModel->errors();

        // Retorno para o ajax request
        return $this->response->setJSON($retorno);
    }

    public function exibir(int $id = null)
    {

        $usuario = $this->buscaUsuarioOu404($id);

        $data = [
            'titulo' => "Detalhando o usuário " . esc($usuario->nome),
            'usuario' => $usuario,
        ];

        return view('Usuarios/exibir', $data);
    }

    public function editar(int $id = null)
    {

        $usuario = $this->buscaUsuarioOu404($id);



        $data = [
            'titulo' => "Editando o usuário " . esc($usuario->nome),
            'usuario' => $usuario,
        ];

        return view('Usuarios/editar', $data);
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
        $usuario = $this->buscaUsuarioOu404($post['id']);

        // Se não foi informado a senha, remover do $post
        //fazer dessa forma, haspassword hash não vira vazia

        if (empty($post['password'])) {

            unset($post['password']);
            unset($post['password_confirmation']);
        }

        // Preenchemos os atributos do usuário com os val do POST
        $usuario->fill($post);

        if ($usuario->hasChanged() === false) {
            $retorno['info'] = 'Não há dados para serem atualizados';
            return $this->response->setJSON($retorno);
        }

        if ($this->usuarioModel->protect(false)->save($usuario)) {

            session()->setFlashdata('sucesso', 'Dados salvos com sucesso!');

            return $this->response->setJSON($retorno);
        }

        // Retornamos os erros de validação
        $retorno['erro'] = 'Por favor verifique os erros e tente novamente';
        $retorno['erros_model'] = $this->usuarioModel->errors();

        // Retorno para o ajax request
        return $this->response->setJSON($retorno);
    }

    public function editarImagem(int $id = null)
    {

        $usuario = $this->buscaUsuarioOu404($id);



        $data = [
            'titulo' => "Alterando a imagem do usuário " . esc($usuario->nome),
            'usuario' => $usuario,
        ];

        return view('Usuarios/editar_imagem', $data);
    }

    public function upload()
    {

        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }


        // Envio o hash do token do form
        $retorno['token'] = csrf_hash();

        $validacao = service('validation');

        $regras = [
            'imagem' => 'uploaded[imagem]|max_size[imagem,1024]|ext_in[imagem,png,jpg,gif,jpeg,webp]',
        ];

        $mensagens = [   // Errors
            'imagem' => [
                'required' => 'Por favor escolha uma imagem',
                'max_size' => 'Por favor escolha uma imagem no máximo 1024',
                'ext_in' => 'Por favor escolha uma imagem png, jpg, gif, jpeg ou webp',
            ],
        ];

        $validacao->setRules($regras, $mensagens);

        if ($validacao->withRequest($this->request)->run() == false) {

            $retorno['erro'] = 'Por favor verifique os erros e tente novamente';
            $retorno['erros_model'] = $validacao->getErrors();

            // Retorno para o ajax request
            return $this->response->setJSON($retorno);
        }

        // Recupero o post da requisição
        $post = $this->request->getPost();

        //Validar existência do User
        $usuario = $this->buscaUsuarioOu404($post['id']);

        // Recuperamos a img que vem no post
        $imagem = $this->request->getFile('imagem');

        list($largura, $altura) = getimagesize($imagem->getPathName());

        if ($largura < "300" || $altura < "300") {

            $retorno['erro'] = 'Por favor verifique os erros e tente novamente';
            $retorno['erros_model'] = ['dimensao' => 'A imagem não pode ser menor do que 300 X 300 pixels'];

            // Retorno para o ajax request
            return $this->response->setJSON($retorno);
        }

        $caminhoImagem = $imagem->store('usuarios');

        // C:\xampp\htdocs\ordem\writable\uploads/usuarios/1667003368_15829ed2dabd02b56c00.jpg
        $caminhoImagem = WRITEPATH . "uploads/$caminhoImagem";

        // Manitupar a img que está salva no diretorio

        // Redimensionando img marca d'agua
        $this->manipulaImagem($caminhoImagem, $usuario->id);

        //Atualizar a Tabela Usuario

        //Recupara old img
        $imagemAntiga = $usuario->imagem;


        $usuario->imagem = $imagem->getName();


        $this->usuarioModel->save($usuario);

        if ($imagemAntiga != null) {
            $this->removeImagemDoFileSystem($imagemAntiga);
        }

        session()->setFlashdata('sucesso', 'Imagem atualizada com sucesso!');

        // Retorno para o ajax request
        return $this->response->setJSON($retorno);
    }

    public function imagem(string $imagem = null)
    {
        if ($imagem != null) {

            $this->exibeArquivo('usuarios', $imagem);
        }
    }

    public function excluir(int $id = null)
    {

        $usuario = $this->buscaUsuarioOu404($id);

        if ($usuario->deletado_em != null) {
            return redirect()->back()->with('info', "Esse usuário já encontra-se excluído!");
        }

        if ($this->request->getMethod() === 'post') {

            $this->usuarioModel->delete($usuario->id);

            if ($usuario->imagem != null) {
                $this->removeImagemDoFileSystem($usuario->imagem);
            }

            $usuario->imagem = null;
            $usuario->ativo = false;

            $this->usuarioModel->protect(false)->save($usuario);

            return redirect()->to(site_url("usuarios"))->with('sucesso', "Usuário $usuario->nome excluido com sucesso!");
        }

        $data = [
            'titulo' => "Excluindo o usuário " . esc($usuario->nome),
            'usuario' => $usuario,
        ];

        return view('Usuarios/excluir', $data);
    }

    public function desfazerExclusao(int $id = null)
    {

        $usuario = $this->buscaUsuarioOu404($id);

        if ($usuario->deletado_em == null) {
            return redirect()->back()->with('info', "Apenas usuários excluídos podem ser recuperados!");
        }

        $usuario->deletado_em = null;
        $this->usuarioModel->protect(false)->save($usuario);


        return redirect()->back()->with('Sucesso', "Usuário $usuario->nome recuperado com sucesso!");
    }

    public function grupos(int $id = null)
    {

        $usuario = $this->buscaUsuarioOu404($id);


        $usuario->grupos = $this->grupoUsuarioModel->recuperaGruposDoUsuario($usuario->id, 5);

        $usuario->pager = $this->grupoUsuarioModel->pager;

        $data = [
            'titulo' => "Gerenciando os grupos de acesso do usuário " . esc($usuario->nome),
            'usuario' => $usuario,
        ];

        // Quando o user for cliente..retornar para a view de exibição do user informando q o user é um cliente e não será possivel add a outros grupos ou remover de grupo existente
        $grupoCliente = 2;
        if (in_array($grupoCliente, array_column($usuario->grupos, 'grupo_id'))) {

            return redirect()->to(site_url("usuarios/exibir/$usuario->id"))->with('info', "Esse usuário é um Cliente, portanto, não é necessário atribuí-lo ou removê-lo de outros grupos de acesso");
        }

        $grupoAdmin = 1;
        if (in_array($grupoAdmin, array_column($usuario->grupos, 'grupo_id'))) {

            $usuario->full_control = true; // Esta no gp de admin..retornar view
            return view('Usuarios/grupos', $data);
        }

        $usuario->full_control = false; //não esta no gp admin...

        if (!empty($usuario->grupos)) {

            // Recupera os grupos que o usuario ainda não faz parte

            $gruposExistentes = array_column($usuario->grupos, 'grupo_id');

            $data['gruposDisponiveis'] = $this->grupoModel
                ->where('id !=', 2) // não recuparar o grup de clientes
                ->whereNotIn('id', $gruposExistentes)
                ->findAll();
        } else {

            // Recupera todos os grupos com exceção do grupo ID 2 que é o cliente

            $data['gruposDisponiveis'] = $this->grupoModel
                ->where('id !=', 2) // não recuparar o grup de clientes                
                ->findAll();
        }

        return view('Usuarios/grupos', $data);
    }

    public function salvarGrupos()
    {

        // Envio o hash do token do form
        $retorno['token'] = csrf_hash();

        // Recupero o post da requisição
        $post = $this->request->getPost();


        //Validar existência do User
        $usuario = $this->buscaUsuarioOu404($post['id']);

        if (empty($post['grupo_id'])) {

            // Retornamos os erros de validação
            $retorno['erro'] = 'Por favor verifique os erros e tente novamente';
            $retorno['erros_model'] = ['grupo_id' => 'Escolha um ou mais grupos para salvar'];

            // Retorno para o ajax request
            return $this->response->setJSON($retorno);
        }


        if (in_array(2, $post['grupo_id'])) {

            // Retornamos os erros de validação
            $retorno['erro'] = 'Por favor verifique os erros e tente novamente';
            $retorno['erros_model'] = ['grupo_id' => 'O grupo de clientes não pode ser atribuído de forma manual'];

            // Retorno para o ajax request
            return $this->response->setJSON($retorno);
        }

        //verificar se o POST esta vindo o gp admin (ID)
        if (in_array(1, $post['grupo_id'])) {

            $grupoAdmin = [
                'grupo_id' => 1,
                'usuario_id' => $usuario->id
            ];

            //associar o user em questão apenas ao gp admin
            $this->grupoUsuarioModel->insert($grupoAdmin);

            //remove todos os grupos q estão associados ao user em questão
            $this->grupoUsuarioModel->where('grupo_id !=', 1)
                ->where('usuario_id', $usuario->id)
                ->delete();


            session()->setFlashdata('sucesso', 'Dados salvos com sucesso!');
            session()->setFlashdata('info', 'O Grupo Administrador foi informado, portanto, não há necessidade de informar outros grupos, pois apenas o Administrator será associado ao usuário!');
            return $this->response->setJSON($retorno);
        }


        // Receberá as permis do POST
        $grupoPush = [];

        foreach ($post['grupo_id'] as $grupo) {
            array_push($grupoPush, [
                'grupo_id' => $grupo,
                'usuario_id' => $usuario->id
            ]);
        }


        $this->grupoUsuarioModel->insertBatch(($grupoPush));

        session()->setFlashdata('sucesso', 'Dados salvos com sucesso!');

        return $this->response->setJSON($retorno);
    }

    public function removeGrupo(int $principal_id = null)
    {

        if ($this->request->getMethod() === 'post') {

            $grupoUsuario = $this->buscaGrupoUsuarioOu404($principal_id);

            if ($grupoUsuario->grupo_id == 2) {
                return redirect()->to(site_url("usuarios/exibir/$grupoUsuario->usuario_id"))->with("info", "Não é permitida a exclusão do usuário do grupo de Clientes");
            }

            $this->grupoUsuarioModel->delete($principal_id);
            return redirect()->back()->with("sucesso", "Usuário removido do grupo de acesso com sucesso!");
        }

        //Não é post
        return redirect()->back();
    }

    public function editarSenha()
    {

        // Sem ACL aq
        $data = [
            'titulo' => 'Edite a sua senha de acesso',
        ];

        return view('usuarios/editar_senha', $data);
    }

    public function atualizarSenha()
    {

        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }


        // Envio o hash do token do form
        $retorno['token'] = csrf_hash();

        $current_password = $this->request->getPost('current_password');

        // Recuperado o user logado
        $usuario = usuario_logado();

        if ($usuario->verificaPassword($current_password) === false) {

            $retorno['erro'] = 'Por favor verifique os erros abaixo e tente novamente';
            $retorno['erros_model'] = ['current_password' => 'Senha atual inválida'];

            return $this->response->setJSON($retorno);
        }

        $usuario->fill($this->request->getPost());


        if ($usuario->hasChanged() === false) {

            $retorno['info'] = 'Não há dados para atualizar';

            return $this->response->setJSON($retorno);
        }

        if ($this->usuarioModel->save($usuario)) {

            $retorno['sucesso'] = 'Senha atualizada com sucesso';

            return $this->response->setJSON($retorno);
        }

        // Retornamos os erros de validação
        $retorno['erro'] = 'Por favor verifique os erros e tente novamente';
        $retorno['erros_model'] = $this->usuarioModel->errors();

        // Retorno para o ajax request
        return $this->response->setJSON($retorno);
    }

    /**
     * Método que recupera o usuário
     *
     * @param integer $id
     * @return Exception|object
     */
    private function buscaUsuarioOu404(int $id = null)
    {
        if (!$id || !$usuario = $this->usuarioModel->withDeleted(true)->find($id)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Não encontramos o usuário $id");
        }
        return $usuario;
    }

    private function buscaGrupoUsuarioOu404(int $principal_id = null)
    {
        if (!$principal_id || !$grupoUsuario = $this->grupoUsuarioModel->find($principal_id)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Não encontramos o registro de associação ao grupo de acesso $principal_id");
        }
        return $grupoUsuario;
    }

    private function manipulaImagem(string $caminhoImagem, int $usuario_id)
    {

        // Redimensionando img
        service('image')
            ->withFile($caminhoImagem)
            ->fit(300, 300, 'center')
            ->save($caminhoImagem);

        $anoAtual = date('Y');

        // Adding a Text Watermark (marca d'agua)
        \Config\Services::image('imagick')
            ->withFile($caminhoImagem)
            ->text("Etarc $anoAtual - User-ID $usuario_id", [
                'color'      => '#fff',
                'opacity'    => 0.5,
                'withShadow' => false,
                'hAlign'     => 'center',
                'vAlign'     => 'bottom',
                'fontSize'   => 10,
            ])
            ->save($caminhoImagem);
    }

    /**
     * Metodo q recupera o registro do gp associado ao usuer
     *
     * @param integer $grupoUsuario
     * @return Exception|object
     */
    private function removeImagemDoFileSystem(string $imagem)
    {

        $caminhoImagem = WRITEPATH . "uploads/usuarios/$imagem";

        if (is_file($caminhoImagem)) {
            unlink($caminhoImagem);
        }
    }
}
