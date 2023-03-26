<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class ItemFakerSeeder extends Seeder
{
    public function run()
    {
        $itemModel = new \App\Models\ItemModel();

        // ---
        $faker = \Faker\Factory::create('pt-BR');

        $faker->addProvider(new \Faker\Provider\pt_BR\Person($faker));

        // chama metdo gera codigo interno do intem
        helper('text');

        $itensPush = [];

        $criarQuantosItens = 20;

        for ($i = 0; $i < $criarQuantosItens; $i++) {

            $tipo = $faker->randomElement($array = array('produto', 'serviço'));

            $controlaEstoque = $faker->numberBetween(0, 1); // true or false

            array_push($itensPush, [
                'codigo_interno' => $itemModel->geraCodigoInternoItem(),
                'nome' => $faker->unique()->words(3, true),
                'marca' => ($tipo === 'produto' ? $faker->word : null),  // singular
                'modelo' => ($tipo === 'produto' ? $faker->unique()->words(2, true) : null),
                'preco_custo' => $faker->randomFloat(2, 10, 100), //aqui max 100 para ficar menor q preço de venda
                'preco_venda' => $faker->randomFloat(2, 100, 1000), //aqui 100, 100 para ficar maior q preço de custo
                'estoque' => ($tipo === 'produto' ? $faker->randomDigitNot(0) : null),
                'controla_estoque' => ($tipo === 'produto' ? $controlaEstoque : null),
                'tipo' => $tipo,
                'ativo' => $controlaEstoque,
                'descricao' => $faker->text(300),
                'criado_em' => $faker->dateTimeBetween('-2 month', '-1 days')->format('Y-m-d H:i:s'),
                'atualizado_em' => $faker->dateTimeBetween('-2 month', '-1 days')->format('Y-m-d H:i:s'),

            ]);
        }



        $itemModel->skipValidation(true)->insertBatch($itensPush);

        echo "$criarQuantosItens semeados com sucesso!";
    }
}
