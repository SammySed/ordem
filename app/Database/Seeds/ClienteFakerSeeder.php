<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class ClienteFakerSeeder extends Seeder
{
    public function run()
    {

        $clienteModel = new \App\Models\ClienteModel();

        $usuarioModel = new \App\Models\UsuarioModel();

        $grupoUsuarioModel = new \App\Models\GrupoUsuarioModel();


        // ---
        $faker = \Faker\Factory::create('pt-BR');

        $faker->addProvider(new \Faker\Provider\pt_BR\Person($faker));
        $faker->addProvider(new \Faker\Provider\pt_BR\Company($faker));
        $faker->addProvider(new \Faker\Provider\pt_BR\PhoneNumber($faker));


        $criarQuantosClientes = 10;

        for ($i = 0; $i < $criarQuantosClientes; $i++) {

            $nomeGerado  = $faker->unique()->name;
            $emailGerado = $faker->unique()->email;


            $cliente = [
                'nome' => $nomeGerado,
                'cpf' => $faker->unique()->cpf,
                'cnpj' => $faker->unique()->cnpj,
                'telefone' => $faker->cellphoneNumber,
                'email' => $emailGerado,
                'cep'   => $faker->postcode,
                'endereco' => $faker->streetName,
                'numero'   => $faker->buildingNumber,
                'bairro'   => $faker->city,
                'cidade'   => $faker->city,
                'estado'   => $faker->stateAbbr,
            ];



            //criado o cliente
            $clienteModel->skipValidation(true)->insert($cliente);

            // motando o dados do user do cliente
            $usuario = [
                'nome' => $nomeGerado,
                'email' => $emailGerado,
                'password' => '123456',
                'ativo'    => true,
            ];

            $usuarioModel->skipValidation(true)->protect(false)->insert($usuario);


            //criando o grupo q o user fará parte
            $grupoUsuario = [
                'grupo_id' => 2, //grupo de clientes
                'usuario_id' => $usuarioModel->getInsertID(),
            ];

            $grupoUsuarioModel->protect(false)->insert($grupoUsuario);

            // atualizar a table de clientes com o id do user criado
            $clienteModel
                ->protect(false)
                ->where('id', $clienteModel->getInsertID())
                ->set('usuario_id', $usuarioModel->getInsertID())
                ->update();
        }

        echo "$criarQuantosClientes clientes semeados com sucesso!";
    }
}
