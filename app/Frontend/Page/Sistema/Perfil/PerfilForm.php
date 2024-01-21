<?php

#region Imports

use KaiokenFramework\Components\Container\VBox;
use KaiokenFramework\Components\Datagrid\Datagrid;
use KaiokenFramework\Components\Datagrid\DatagridColumn;
use KaiokenFramework\Components\Dialog\Message;
use KaiokenFramework\Components\Form\CheckButton;
use KaiokenFramework\Components\Form\Form;
use KaiokenFramework\Components\Form\Hidden;
use KaiokenFramework\Components\Form\Text;
use KaiokenFramework\Components\Wrapper\KaiokenDatagridWrapper;
use KaiokenFramework\Components\Wrapper\KaiokenFormWrapper;

use KaiokenFramework\Database\Criteria;
use KaiokenFramework\Database\Repository;
use KaiokenFramework\Database\Transaction;

use KaiokenFramework\Page\Action;
use KaiokenFramework\Page\Page;
use KaiokenFramework\Security\Token;
use KaiokenFramework\Session\Session;

use KaiokenFramework\Traits\EditTrait;
use KaiokenFramework\Traits\ReloadTrait;
use KaiokenFramework\Traits\SaveTrait;
use KaiokenFramework\Util\Util;

#endregion

/*
* Tela de Perfil dos Usuários
*/
class PerfilForm extends Page
{
    #region Objetos

    private $form;
    private $datagrid;
    private $loaded;
    private $activeRecord;
    private $filters;
    private $idInput;
    private $checkBox = [];
    #endregion

    #region Traits

    use ReloadTrait;
    use EditTrait;
    #endregion

    #region Construtor

    /**
     * Construtor da página
    */
    public function __construct()
    {
        parent::__construct();

        $this->createPage();
    }
    #endregion

    #region Metodos

    #region Principais

    #region createPage
    private function createPage()
    {
        // Define o Active Record
        $this->activeRecord = 'Formulario';

        #region Criar Formulario
        $this->form = new KaiokenFormWrapper(new Form('form_perfil'));
        $this->form->setTitle('Cadastro de Perfil');
        $this->form->setUseJS(true);
        
        $codigo    = new Text('IdPerfil');
        $codigo->id = "txtCodigo";
        $codigo->maxlength = "10";
        
        $descricao = new Text('Descricao');
        $descricao->id = "txtDescricao";
        $descricao->maxlength = "100";

        $this->form->addField('Codigo', $codigo, '15%');
        $this->form->addField('Descrição', $descricao, '90%');
        
        $this->form->addAction('Salvar', new Action(array($this, 'onSave')));
        $this->form->addAction('Cancelar', new Action(array($this, 'onEdit')));

        $nextCode = $this->newCode();
        $codigo->setEditable(FALSE, $nextCode);
        #endregion

        #region Criar DataGrid

        // instancia a Datagrid
        $this->datagrid = new KaiokenDatagridWrapper(new Datagrid);

        // instancia as colunas da Datagrid
        $formulario = new DatagridColumn('nome_formulario',     'Formulário',   'left', '600px');
        $menu  = new DatagridColumn('caminho_formulario',   'Menu', 'left', '600px');
        
        // adiciona as colunas à Datagrid
        $this->datagrid->addColumn($formulario);
        $this->datagrid->addColumn($menu);

        $this->datagrid->addCheckBox('IdFormulario'); 

        // monta a página através de uma tabela
        $box = new VBox;
        $box->style = 'display:block';
        $box->add($this->form);
        $box->add($this->datagrid);

        $script = '<script type="text/javascript" src="app/Frontend/Page/Sistema/Perfil/Perfil.js"></script>';
        $box->add($script);
        #endregion

        parent::add($box);
    }
    #endregion

    #region show

    /**
     * exibe a página
     */
    public function show()
    {
        // se a listagem ainda não foi carregada
        if (!$this->loaded)
        {
            $this->onReload();
        }
        parent::show();
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
            $this->Redirect(); 
        }
        catch(Exception $ex)
        {
            Transaction::rollback();
            ob_end_clean();

            new Message('error', $ex->getMessage(), 5000, IdFoco: "#$this->idInput");
            $this->form->loadData();
            // Token::generateTokenCsrf();
            $this->setToken();
            exit();
        }
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

            if (isset($param["key"]))
            {     
                $IdPerfil = $param["key"];

                Transaction::open(); 

                $perfil = Perfil::find($IdPerfil);
                Session::setValue('KAIOKEN', clone $perfil);
                Session::getValue('KAIOKEN')->IdPerfil = $perfil->IdPerfil;

                if ($perfil) 
                    $this->form->setData($perfil);                

                $this->ativarCheckBox($IdPerfil);

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

    #endregion

    #region Auxiliares

    #region salvar
    private function salvar()
    {
        Transaction::open();

        #region Perfil
        $this->activeRecord = 'Perfil';
        $IdUsuarioLogado = Session::getValue("IdUsuarioLogado");
        $usuario = Usuario::find($IdUsuarioLogado);

        $dados = $this->form->getData();

        $dados->UsuarioAlteracao = $usuario->Usuario;
        $dados->DataAlteracao =  date('Y-m-d H:i');

        $perfil = new Perfil;
        $perfil->fromArray( (array) $dados);
        $perfil->save();
        #endregion

        #region PerfilFormulario
        $perfil->deletarTodosFormularios();
        $formularios = $_POST["Formularios"];

        for($i = 0; $i < count($formularios); $i++) 
        {
            $IdFormulario  = $formularios[$i];
            $perfil->addFormulario( new Formulario($IdFormulario) );
        }
        #endregion
        
        Transaction::close();
    }
    #endregion

    #region validarCampos
    private function validarCampos()
    {
        $campos = $this->form->getData();

        foreach ($campos as $indice => $value) 
        {     
            #region Codigo
            if($indice == "IdPerfil")
            {
                if(empty($value))
                {
                    $this->idInput = $this->getIdInput($indice);
                    throw new Exception("Preencha o campo $indice!");
                }

                if(!Util::ehStatusNovo() && (int)$value != Perfil::getIdPerfilSuporte())
                {
                    if(Session::getValue("KAIOKEN")->IdPerfil == Perfil::getIdPerfilSuporte())
                    {
                        $this->idInput = $this->getIdInput($indice);
                        throw new Exception("Não é possível alterar o código do perfil 'suporte'");
                    }
                }
            }
            #endregion

            #region Descricao
            if($indice == 'Descricao')
            {
                if(empty($value))
                {
                    $this->idInput = $this->getIdInput($indice);
                    throw new Exception("Preencha o campo $indice!");
                }

                if(strlen($value) > 100)
                {
                    $this->idInput = $this->getIdInput($indice);
                    throw new Exception("Descrição deve conter menos de 100 caracteres");
                }

                if(!Util::ehStatusNovo() && strtolower($value) != "suporte")
                {
                    if(Session::getValue("KAIOKEN")->IdPerfil == Perfil::getIdPerfilSuporte())
                    {
                        $this->idInput = $this->getIdInput($indice);
                        throw new Exception("Não é possível alterar a descrição do perfil 'suporte'");
                    }
                }

                if(Util::ehStatusNovo() && Perfil::TemPerfil($value))
                {
                    $this->idInput = $this->getIdInput($indice);
                    $this->fecharModalAlert();
                    throw new Exception("Perfil já Cadastrado");
                }
                else 
                {
                    $DescricaoDoPerfilEditado = $this->getDescricaoDoPerfilEditado();

                    if(Perfil::TemPerfil($value) && $value != $DescricaoDoPerfilEditado)
                    {
                        $this->idInput = $this->getIdInput($indice);
                        $this->fecharModalAlert();
                        $this->setToken();

                        throw new Exception("Perfil já Cadastrado");
                    }
                }
            }
            #endregion
        }

        $formularios = $_POST["Formularios"];

        if($formularios == null)
            throw new Exception("Selecione ao menos um Formulário!"); 
    }
    #endregion

    #region fecharModalAlert
    private function fecharModalAlert()
    {
        echo "setTimeout(function() { CloseAlert(); $('.close-alert').trigger('click'); }, 500); </script> ";
    }
    #endregion

    #region getDescricaoDoPerfilEditado
    private function getDescricaoDoPerfilEditado()
    {
        $Descricao = isset(Session::getValue('KAIOKEN')->Descricao) ? Session::getValue('KAIOKEN')->Descricao : "";
        return $Descricao;
    }
    #endregion

    #region getIdInput
    function getIdInput($indice) {

        $field = $this->form->getFields()[$indice];
        return $field->getProperty("id");
    }
    #endregion

    #region ativarCheckBox
    private function ativarCheckBox($IdPerfil)
    {
        $criteria = new Criteria;
        $repository = new Repository('PerfilFormulario');

        $criteria->add('IdPerfil', '=', $IdPerfil);
        $formularios = $repository->load($criteria);

        foreach($formularios as $formulario)
        {
            echo "<script> setTimeout(function(){ ativarCheckBox('$formulario->IdFormulario') }, 100); </script> ";
        }
    }
    #endregion

    #region Redirect
    function Redirect()
    {
        echo "<script> setTimeout(function(){ window.location = 'index.php?class=PerfilForm' }, 1000); </script>";
    }
    #endregion

    #region newCode
    private function newCode()
    {
        Transaction::open();
        $perfil = new Perfil();
        $IdPerfil = $perfil->getLastId() + 1;
        Transaction::close();

        return $IdPerfil;
    }
    #endregion    

    #region setToken
    private function setToken()
    {
        $token = Session::getValue("Csrf_Token_Session");
        echo "<script> setTimeout(function(){ generateTokenCSRF('{$token}') }, 1000); </script>";
    }
    #endregion

    #endregion

    #endregion
}