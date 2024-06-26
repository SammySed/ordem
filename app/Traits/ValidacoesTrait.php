<?php

namespace App\Traits;

trait ValidacoesTrait
{
    public function consultaViaCep(string $cep): array
    {
        $cep = str_replace('-', '', $cep);

        $url = "https://viacep.com.br/ws/{$cep}/json/";

        // abrir a conection

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        //execuntando colsulta

        $resposta = curl_exec($ch);

        // capturando erro
        $erro = curl_error($ch);

        $retorno = [];

        if ($erro) {

            $retorno['erro'] = $erro;

            return $retorno;
        }


        $consulta = json_decode($resposta);

        if (isset($consulta->erro) && !isset($consulta->cep)) {

            session()->set('blockCep', true); //usar na controller

            $retorno['erro'] = '<span class="text-danger">Informe um CEP válido</span>';

            return $retorno;
        }

        session()->set('blockCep', false); //usar na controller

        $retorno['endereco'] = esc($consulta->logradouro);
        $retorno['bairro'] = esc($consulta->bairro);
        $retorno['cidade'] = esc($consulta->localidade);
        $retorno['estado'] = esc($consulta->uf);

        return $retorno;
    }

    public function checkEmail(string $email, bool $bypass = false): array
    {

        $retorno = [];

        if ($bypass === true) {

            return $retorno;
        }


        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://mailcheck.p.rapidapi.com/?domain={$email}",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "x-rapidapi-host: mailcheck.p.rapidapi.com",
                "x-rapidapi-key: " . getenv('CHAVE_CHECK_MAIL_ORG_API')
            ),
        ));

        $resposta = curl_exec($curl);
        $erro = curl_error($curl);

        curl_close($curl);

        if ($erro) {

            $retorno['erro'] = "cURL Error #:" . $erro;

            return $retorno;
        }

        $consulta = json_decode($resposta);

        // Debug
        // return $consulta;

        session()->set('blockEmail', esc($consulta->block)); //usar na controller

        if ($consulta->block) {

            $retorno['erro'] = '<span class="text-danger">O domínio ' . $consulta->domain . ' não é válido</span>';
            return $retorno;
        }

        return $retorno;
    }
}
