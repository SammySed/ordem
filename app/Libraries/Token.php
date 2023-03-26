<?php

namespace App\libraries;

class Token{

    private $token;

    /**
     * Metodo construtor da classe.
     * $token = new token($token); ja possui token...precisa do hash do token
     * $token = new token(); Gerar token parar recuperar senha
     *
     * @param string $token
     */
    public function __construct(string $token = null)
    {
        
        if($token === null){

            $this->token = bin2hex(random_bytes(16));
        }else{

            $this->token = $token;
        }

    }

    /**
     * Metodo q retorna o valor do $token
     *
     * @return string
     */
    public function getValue(): string
    {
        return $this->token;
    }

    public function getHash(): string
    {
        return hash_hmac("sha256", $this->token, getenv('CHAVE_RECUPERACAO_SENHA'));
    }

}