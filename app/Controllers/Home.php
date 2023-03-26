<?php

namespace App\Controllers;

use App\Traits\ValidacoesTrait;

class Home extends BaseController
{
    use ValidacoesTrait;

    public function index()
    {
        // d(usuario_logado());

        $data = [
            'titulo' => 'Home'
        ];

        return view('Home/index', $data);
    }

    public function login()
    {

        $autenticacao = service('autenticacao');

        $autenticacao->login('walldestr@gmail.com', '123456');

        $usuario = $autenticacao->pegaUsuarioLogado();

        // dd($usuario);

        // dd($autenticacao->isCliente());

        // $autenticacao->logout();
        // return redirect()->to(site_url('/'));

        // dd($autenticacao->estaLogado());
    }

    public function email()
    {
        $email = service('email');

        $email->setFrom('no-reply@ordem.com', 'Etarc Contabilidade');
        $email->setTo('walldestroyer9643@gmail.com');


        $email->setSubject('Recuperação de senha');
        $email->setMessage('Iniciando a recuperação de senha.');

        if ($email->send()) {
            echo 'email enviado';
        } else {
            $email->printDebugger();
        }
    }

    public function cep()
    {

        $cep = "14150-000";

        return $this->response->setJSON($this->consultaViaCep($cep));
    }

    public function barcode()
    {
        // $generator = new \Picqer\Barcode\BarcodeGeneratorSVG();
        // echo $generator->getBarcode('081231723897', $generator::TYPE_CODE_128);
    }

    public function checkemail()
    {

        // $email = 'walldestroyer9643@g.com';

        // return $this->response->setJSON($this->consultaEmail($email));
    }
}
