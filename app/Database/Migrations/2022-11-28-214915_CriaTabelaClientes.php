<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CriaTabelaClientes extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 5,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'usuario_id' => [
                'type'           => 'INT',
                'constraint'     => 5,
                'unsigned'       => true,
                'null'           => true, // será populado depois que oo user for criado para o cliente
            ],
            'nome' => [
                'type'       => 'VARCHAR',
                'constraint' => '128',
            ],
            'cpf' => [
                'type'       => 'VARCHAR',
                'constraint' => '240',
                'unique'     => true,
                'null'       => true,
            ],
            'cnpj' => [
                'type'       => 'VARCHAR',
                'constraint' => '240',
                'unique'     => true,
                'null'       => true,
            ],
            'telefone' => [
                'type'       => 'VARCHAR',
                'constraint' => '240',
                'null'       => true,
            ],
            'email' => [
                'type'       => 'VARCHAR',
                'constraint' => '128',
                'unique'     => true,
            ],
            'cep' => [
                'type'       => 'VARCHAR',
                'constraint' => '20',
            ],
            'endereco' => [
                'type'       => 'VARCHAR',
                'constraint' => '128',
            ],
            'numero' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'null'       =>  true,
            ],
            'bairro' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
            ],
            'cidade' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
            ],
            'estado' => [
                'type'       => 'VARCHAR',
                'constraint' => '5',
            ],
            'criado_em' => [
                'type'       => 'DATETIME',
                'null' => true,
                'default' => null,
            ],
            'atualizado_em' => [
                'type'       => 'DATETIME',
                'null' => true,
                'default' => null,
            ],
            'deletado_em' => [
                'type'       => 'DATETIME',
                'null' => true,
                'default' => null,
            ],
        ]);

        $this->forge->addKey('id', true);

        $this->forge->addForeignKey('usuario_id', 'usuarios', 'id');


        $this->forge->createTable('clientes');
    }

    public function down()
    {
        $this->forge->dropTable('clientes');
    }
}
