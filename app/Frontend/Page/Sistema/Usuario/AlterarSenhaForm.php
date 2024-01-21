<?php

#region Imports

use KaiokenFramework\Components\Base\JScript;
use KaiokenFramework\Components\Container\VBox;
use KaiokenFramework\Components\Dialog\Message;
use KaiokenFramework\Components\Form\Form;
use KaiokenFramework\Components\Form\Password;
use KaiokenFramework\Components\Form\Text;
use KaiokenFramework\Components\Wrapper\KaiokenFormWrapper;

use KaiokenFramework\Database\Transaction;

use KaiokenFramework\Page\Action;
use KaiokenFramework\Page\Page;
use KaiokenFramework\Router\Router;
use KaiokenFramework\Session\Session;
#endregion

class AlterarSenhaForm extends Page
{
    #region Propriedades da Classe
    private $form;
    private $activeRecord;
    private $idInput;
    #endregion

    #region Construtor
    /**
     * Construtor da página
    */
    public function __construct()
    {
        parent::__construct();
        $this->criarPagina();
    }
    #endregion

    #region Metodos

    #region criarPagina
    private function criarPagina()
    {
        $this->activeRecord = 'Usuario';

        #region Formulario
        $this->form = new KaiokenFormWrapper(new Form('form_alterar_senha'));
        $this->form->setTitle('Alterar Senha');

        $txtNovaSenha = new Password('NovaSenha');
        $txtNovaSenha->id = "txtNovaSenha";
        $txtNovaSenha->maxlength = "200";

        $txtConfirmarSenha  = new Password('ConfirmarSenha');
        $txtConfirmarSenha->id = "txtConfirmarSenha";
        $txtNovaSenha->maxlength = "200";
        

        $this->form->addField('Nova Senha', $txtNovaSenha, '100%');
        $this->form->addField('Confirmar Senha', $txtConfirmarSenha, '100%');
        $this->form->addAction('Salvar', new Action(array($this, 'onSave')));

        $box = new VBox;
        $box->style = 'display:block';
        $box->add($this->form);

        parent::add($box);        
        #endregion
    }
    #endregion

    #region onSave
    public function onSave()
    {
        try
        {
            $this->validarCampos();
            $this->salvar();
            
            new Message('success', 'Dados armazenados com sucesso');
            
            $link = 'index.php?class=UsuarioList';
            JScript::redirect($link ,1000);
        }
        catch(Exception $ex)
        {
            Transaction::rollback();
            new Message('error', $ex->getMessage(), 5000, IdFoco: "#$this->idInput");
        }
    }
    #endregion

    #region salvar
    private function salvar()
    {
        Transaction::open();

        $user = new Usuario(); 
        $txtNovaSenha = $_POST['NovaSenha'];

        $IdUsuarioLogado = Session::getValue("IdUsuarioLogado");
        $usuarioLogado = Usuario::find($IdUsuarioLogado);
        
        $user = Session::getValue('KAIOKEN');
        $user->DataAlteracao =  date('Y-m-d H:i');
        $user->UsuarioAlteracao = $usuarioLogado->Usuario;
        $user->Senha = password_hash($txtNovaSenha, PASSWORD_ARGON2ID);
        
        $user->save();
        
        Transaction::close();
        Session::setValue('KAIOKEN', NULL);
    }
    #endregion

    #region onEdit

    /**
    * Carrega registro para edição
    */
    public function onEdit($param)
    {
        try
        {
            if (isset($param["IdUsuario"]))
            {
                $IdUsuario = $param["IdUsuario"];

                Transaction::open(); 

                $usuario = Usuario::find($IdUsuario);
                Session::setValue('KAIOKEN', clone $usuario);
                Session::getValue('KAIOKEN')->IdUsuario = $usuario->IdUsuario;           

                Transaction::close(); 
            }
        }
        catch (Exception $e)
        {
            Transaction::rollback(); 
            new Message('error', $e->getMessage());
        }
    }
    #endregion

    #region validarCampos
    private function validarCampos()
    { 
        $txtNovaSenha = $_POST['NovaSenha'];
        $txtConfirmarSenha = $_POST['ConfirmarSenha'];

        #region NovaSenha

        if(empty($txtNovaSenha))
        {
            $this->idInput = $this->getIdInput("NovaSenha");
            throw new Exception("Preencha o campo Nova Senha!");
        }

        if(strlen($txtNovaSenha) > 200)
        {
            $this->idInput = $this->getIdInput("NovaSenha");
            throw new Exception("Nova Senha deve conter menos de 200 caracteres");
        }

        if(!empty($txtNovaSenha)) 
        {
            Usuario::checkPassword($txtNovaSenha);
        }
        #endregion

        #region Confirmar Senha

        if(empty($txtConfirmarSenha)) 
        {
            $this->idInput = "txtConfirmarSenha";
            throw new Exception("Preencha o campo Confirmar Senha!");        
        }          

        if($txtNovaSenha != $txtConfirmarSenha)
        {
            $this->idInput = "txtConfirmarSenha";
            throw new Exception("Senhas Divergentes!");
        }
        #endregion        
    }
    #endregion

    #region getIdInput
    private function getIdInput($indice) {

        $field = $this->form->getFields()[$indice];
        return $field->getProperty("id");
    }
    #endregion

    #region Redirect
    function Redirect()
    {
        $link = 'index.php?class=UsuarioList';
        JScript::redirect($link ,1000);
    }
    #endregion

    #endregion
}