<?php

#region Imports

use KaiokenFramework\Components\Dialog\Message;
use KaiokenFramework\Database\Transaction;
use KaiokenFramework\Email\EmailRecuperarSenha;
use KaiokenFramework\Page\Page;
use KaiokenFramework\Traits\DeleteTrait;
use KaiokenFramework\Util\Util;
#endregion

class RecuperarSenhaForm extends Page
{
    #region Objetos
    private $html;
    private $usuario;
    #endregion

    #region Construtor
    public function __construct()
    {
        parent::__construct();
        // $this->criarPagina();
    }
    #endregion
    
    #region Traits
    use DeleteTrait;
    #endregion

    #region Metodos

    #region criarPagina
    public function criarPagina()
    {
        #region Html
        $template = __DIR__ . DIRECTORY_SEPARATOR . "RecuperarSenha.html";
        $this->html = file_get_contents($template);

        // parent::add($this->html);

        echo $this->html;
        #endregion
    }
    #endregion

    #region onRecuperarSenha
    public function onRecuperarSenha($param)
    {
        try 
        {
            Transaction::open();

            $email = $_POST['Email'];

            $this->validarEmail($email);
            $this->enviarEmail();

            Transaction::close();
        }
        catch(Exception $ex)
        {
            Transaction::rollback();
            new Message("error", $ex->getMessage(), 5000);
        }
    }
    #endregion

    private function enviarEmail()
    {
        $novaSenha = base64_encode(rand());
        $hash = password_hash($novaSenha, PASSWORD_ARGON2ID);

        $msg = "NOVA SENHA:  $novaSenha";
        $mail = new EmailRecuperarSenha($this->usuario, $msg);

        if($mail->enviar())
            new Message("success", "Nova senha enviada com sucesso!");                
    }

    #region validarEmail

    private function validarEmail($email)
    {
        if(!Util::validaEmail($email))
            throw new Exception("Email Inválido!");

        $usuario = Usuario::findByEmail($email);

        if($usuario instanceof Usuario)
            $this->usuario = $usuario;
        else
            throw new Exception("Email Inválido!");
    }
    #endregion

    #endregion
}