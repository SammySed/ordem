<?php

namespace App\Models;

use CodeIgniter\Model;

class ClienteModel extends Model
{
    protected $table            = 'clientes';
    protected $returnType       = 'App\Entities\Cliente';
    protected $useSoftDeletes   = true;
    protected $allowedFields    = [
        'usuario_id',
        'nome',
        'cpf',
        'cnpj',
        'telefone',
        'email',
        'cep',
        'endereco',
        'numero',
        'bairro',
        'cidade',
        'estado',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $createdField  = 'criado_em';
    protected $updatedField  = 'atualizado_em';
    protected $deletedField  = 'deletado_em';

    // Validation
    protected $validationRules = [
        'nome'         => 'required|min_length[3]|max_length[125]',
        'email'        => 'required|valid_email|max_length[230]|is_unique[clientes.email,id,{id}]', // Não pode ter espaços
        'email'        => 'is_unique[usuarios.email,id,{id}]', // tbm validar se o email informado não existe na tabela de user
        'telefone'     => 'required|max_length[16]',
        'cpf'          => 'exact_length[14]|validaCPF|is_unique[clientes.cpf,id,{id}]',
        'cnpj'         => 'exact_length[18]|validaCNPJ|is_unique[clientes.cnpj,id,{id}]',
        'cep'          => 'required|exact_length[9]',

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
    ];
}
