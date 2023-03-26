<?php

namespace App\Models;

use CodeIgniter\Model;

use App\Libraries\Token;

class UsuarioModel extends Model
{

    protected $table            = 'usuarios';
    protected $returnType       = 'App\Entities\Usuario';
    protected $useSoftDeletes   = true;
    protected $allowedFields    = [
        'nome',
        'email',
        'password',
        'reset_hash',
        'reset_expira_em',
        'imagem',
        // não colocar o campo ativo...Pois existe a manipulação de formulário     
    ];

    // Dates
    protected $useTimestamps = true;
    protected $createdField  = 'criado_em';
    protected $updatedField  = 'atualizado_em';
    protected $deletedField  = 'deletado_em';

    // Validation
    protected $validationRules = [
        'nome'         => 'required|min_length[3]|max_length[125]',
        'email'        => 'required|valid_email|max_length[230]|is_unique[usuarios.email,id,{id}]', // Não pode ter espaços
        'password'     => 'required|min_length[6]',
        'password_confirmation' => 'required_with[password]|matches[password]',
    ];
    protected $validationMessages = [
        'nome' => [
            'required' => 'O campo Nome é obrigatório.',
            'min_length' => 'O campo Nome precisa ter pelo menos 3 caractéres.',
            'max_length' => 'O campo Nome não pode ser maior que 125 caractéres.',
        ],
        'email' => [
            'required' => 'O campo E-mail é obrigatório.',
            'max_length' => 'O campo E-mail não pode ser maior que 230 caractéres.',
            'is_unique' => 'Esse E-mail já está sendo utilizado. Por favor informe outro E-mail',
        ],
        'password_confirmation' => [
            'required_with' => 'Por favor confirme a sua Senha.',
            'matches' => 'As Senhas precisam ser idênticas.',
        ],
    ];

    // Callbacks    
    protected $beforeInsert   = ['hashPassword'];
    protected $beforeUpdate   = ['hashPassword'];

    protected function hashPassword(array $data)
    {

        if (isset($data['data']['password'])) {

            $data['data']['password_hash'] = password_hash($data['data']['password'], PASSWORD_DEFAULT);

            // Remover dos dados a serem salvos
            unset($data['data']['password']);
            unset($data['data']['password_confirmation']);
        }

        return $data;
    }

    /**
     * Metodo que recupera o usuario para logar na aplicação
     *
     * @param string $email
     * @return null|object
     */
    public function buscaUsuarioPorEmail(string $email)
    {
        return $this->where('email', $email)->where('deletado_em', null)->first();
    }

    /**
     * Método que recupera as permissões do user logado
     *
     * @param integer $usuario_id
     * @return null|array
     */
    public function recuperaPermissoesDoUsuarioLogado(int $usuario_id)
    {
        $atributos = [
            // 'usuarios.id',
            // 'usuarios.nome as usuario',
            // 'grupos_usuarios.*',
            'permissoes.nome AS permissao'
        ];


        return $this->select($atributos)
            ->asArray() //recuperado no form array
            ->join('grupos_usuarios', 'grupos_usuarios.usuario_id = usuarios.id')
            ->join('grupos_permissoes', 'grupos_permissoes.grupo_id = grupos_usuarios.grupo_id')
            ->join('permissoes', 'permissoes.id = grupos_permissoes.permissao_id')
            ->where('usuarios.id', $usuario_id)
            ->groupBy('permissoes.nome')
            ->findAll();
    }

    /**
     * Método que recupera o usuário de acordo com o hash do token
     *
     * @param string $token
     * @return null|object
     */
    public function buscaUsuarioParaRedefinirSenha(string $token)
    {
        //Instancia o object da classe, passando como paramentro no contrutor o $token
        $token = new Token($token);

        //recupera o hash do token
        $tokenHash = $token->getHash();

        // Consultando na base o user de acordo com o hash
        $usuario = $this->where('reset_hash', $tokenHash)
            ->where('deletado_em', null)
            ->first();

        //validado se o user foi encontrado
        if ($usuario === null) {

            return null;
        }

        //validado se o token não expirou
        if ($usuario->reset_expira_em < date('Y-m-d H:i:s')) {

            return null;
        }

        //retorna user
        return $usuario;
    }

    /**
     * Método q att o email do user conforme email do cliente
     *
     * @param integer $usuario_id
     * @param string $email
     * @return void
     */
    public function atualizaEmailDoCliente(int $usuario_id, string $email)
    {

        return $this->protect(false)
            ->where('id', $usuario_id)
            ->set('email', $email)
            ->update();
    }
}
