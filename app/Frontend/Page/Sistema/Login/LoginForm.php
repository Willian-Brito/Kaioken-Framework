<?php

#region Imports

use KaiokenFramework\Components\Base\JScript;
use KaiokenFramework\Page\Page;

use KaiokenFramework\Components\Dialog\Message;
use KaiokenFramework\Components\Dialog\Question;
use KaiokenFramework\Database\Criteria;
use KaiokenFramework\Database\Repository;
use KaiokenFramework\Database\Transaction;
use KaiokenFramework\Page\Action;
use KaiokenFramework\Session\Session;
use KaiokenFramework\Traits\DeleteTrait;
#endregion

class LoginForm extends Page
{

    #region Construtor
    public function __construct()
    {
        parent::__construct();
    }
    #endregion
    
    #region Traits
    use DeleteTrait;
    #endregion

    #region Metodos

    #region onLogin

    public function onLogin($param)
    {
        try
        {
            #region Credenciais

            $user =  htmlspecialchars($_POST['Usuario'], double_encode:false);
            $password = htmlspecialchars($_POST['Senha'], double_encode:false);
            #endregion

            #region Validação

            $this->validarCampos($user, $password);
            $this->vincularSessaoAoLogin();
            $this->verificarSeUltrapassouNumeroTentativas();
            #endregion

            #region Autenticação
            
            $this->autenticar($user, $password);
            $this->contarFalhaDeLogin();

            throw new Exception("Usuário ou Senha inválido");
            #endregion                    
        }
        catch(Exception $ex)
        {
            Transaction::rollback();
            new Message('error', $ex->getMessage(), 5000);
        }
    }
    #endregion

    #region validarCampos
    public function validarCampos($user, $password)
    {
        if(empty($user))
            throw new Exception("Preencha o campo usuário");

        if(empty($password))
            throw new Exception("Preencha o campo senha");

        if(!Usuario::estaAtivo($user))
            throw new Exception("Usuário ou Senha inválido");
    }
    #endregion

    #region onLogout
    
    public function onLogout($param)
    {
        // $actionYes = new Action(array($this, 'logout'));
        // $actionNo = new Action(array($this, 'fecharMsg'));
        
        // new Question('Deseja realmente sair do sistema?', $actionYes, $actionNo);  
        
        $this->logout();
    }
    #endregion

    #region logout
    public function logout()
    {
        Session::setValue('formulariosComPermissao', NULL);
        Session::setValue('IdUsuarioLogado', NULL);
        Session::setValue('UsuarioLogado', NULL);
        Session::setValue('logado', FALSE);

        Session::regenerate();
        JScript::redirect('index.php');
    }
    #endregion

    #region Autenticação

    #region vincularSessaoAoLogin

    private function vincularSessaoAoLogin()
    {
        //Se não tem Sessão Vincula a Login
        if (!Session::getValue("Login"))
        {
            Session::setValue("Login", new Login());
        }
    }
    #endregion

    #region contarFalhaDeLogin

    private function contarFalhaDeLogin()
    {
        Session::getValue("Login")->QuantidadeTentativa++;

        if (Session::getValue("Login")->QuantidadeTentativa >= 10)  
        {
            $now = new DateTime();
            Session::getValue("Login")->DataProximaTentativa = $now->modify('+5 minutes');
        }
    }
    #endregion

    #region verificarSeUltrapassouNumeroTentativas

    private function verificarSeUltrapassouNumeroTentativas()
    {
        $now = new DateTime();
        $tempoDeEsperaNaoTerminou = Session::getValue("Login")->DataProximaTentativa > $now;

        if ($tempoDeEsperaNaoTerminou)
        {
            Session::getValue("Login")->QuantidadeTentativa = 0;

            throw new Exception("Usuário ultrapassou o numero de tentativas, tente daqui 5 minutos!");
            return;
        }
    }
    #endregion

    #region autenticar
    private function autenticar($user, $password) 
    {
        Transaction::open();

        $usuario = Usuario::findByLogin($user);

        if($usuario instanceof Usuario)
        {
            if($usuario->validatePassword($password))
            {     
                #region Reseta Tentativas de Login

                Session::getValue("Login")->QuantidadeTentativa = 0;
                Session::getValue("Login")->DataProximaTentativa = new DateTime();
                #endregion

                #region Usuario Logado
                Session::regenerate();
                Session::setValue('IdUsuarioLogado', $usuario->IdUsuario);
                Session::setValue('UsuarioLogado', $usuario->Usuario);
                Session::setValue('logado', TRUE);
                #endregion

                JScript::redirect('index.php');
            }  

            $this->contarFalhaDeLogin();
            throw new Exception("Usuário ou Senha inválido");
        }

        Transaction::close();
    }
    #endregion

    #endregion

    #endregion
}
