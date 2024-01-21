<?php

#region Imports

use KaiokenFramework\Components\Base\JScript;
use KaiokenFramework\Components\Dialog\Message;
use KaiokenFramework\Components\Form\Combo;
use KaiokenFramework\Components\Form\Form;
use KaiokenFramework\Components\Form\Text;
use KaiokenFramework\Components\Form\TextArea;
use KaiokenFramework\Components\Wrapper\KaiokenFormWrapper;

use KaiokenFramework\Database\Transaction;

use KaiokenFramework\Enum\PageStatusEnum;

use KaiokenFramework\Page\Action;
use KaiokenFramework\Page\Page;
use KaiokenFramework\Security\Token;
use KaiokenFramework\Session\Session;

use KaiokenFramework\Traits\EditTrait;
use KaiokenFramework\Traits\ReloadTrait;
#endregion

class ParametroForm extends Page
{
    #region Propriedades da Classe
    
    private $form;
    private $datagrid;
    private $loaded;
    private $activeRecord;
    private $filters;
    private $idInput;
    private $html;
    private $parametro;
    #endregion

    #region Traits
    use ReloadTrait;
    use EditTrait;
    #endregion

    #region Construtor
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
        $this->activeRecord = 'Parametro';

        #region Html

        $template = __DIR__ . DIRECTORY_SEPARATOR . "ParametroForm.html";
        $this->html = file_get_contents($template);

        #region Criar Formulário
        $this->form = new KaiokenFormWrapper(new Form('form_parametro'));

        #region criando campos

        $cbUsuario = new Combo('IdUsuario');
        $cbUsuario->id = "cbUsuario";
        $cbUsuario->class = "form-control";

        $cbTipoParametro = new Combo('IdTipoParametro');
        $cbTipoParametro->id = "cbTipoParametro";
        $cbTipoParametro->class = "form-control";

        $txtDescricao = new Text('Descricao');
        $txtDescricao->id = "txtDescricao";
        $txtDescricao->maxlength = "200";

        $txtValor = new Text('Valor');
        $txtValor->id = "txtValor";
        $txtValor->maxlength = "50";

        $txtObservacao = new TextArea('Observacao');
        $txtObservacao->id = "txtObservacao";
        $txtObservacao->maxlength = "1000";
        $txtObservacao->setSize('100%', '70px');


        $this->setComboBoxUsuario($cbUsuario);
        $this->setComboBoxTipoParametro($cbTipoParametro);
        #endregion

        #region adicionando campos

        $this->form->addField('Usuario', $cbUsuario, '100%');
        $this->form->addField('TipoParametro', $cbTipoParametro, '100%');
        $this->form->addField('Descricao', $txtDescricao, '80%');
        $this->form->addField('Valor', $txtValor, '80%');
        $this->form->addField('Observacao', $txtObservacao, '100%');

        $this->form->addAction('Salvar', new Action(array($this, 'onSave')));
        $this->form->addAction('Cancelar', new Action(array($this, 'onEdit')));
        #endregion

        #endregion

        #endregion

        if( !isset($_GET['method']) || ($_GET['method'] == 'onEdit') )
            Token::generateTokenCsrf();

        if(!isset($_GET["IdParametro"]))
            parent::add($this->html);
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
            parent::resetForm();
        }
        catch(Exception $ex)
        {
            Transaction::rollback();
            new Message('error', $ex->getMessage(), 5000, IdFoco: "#$this->idInput");

            $this->loadDataForm();
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
                $IdParametro = $param["key"];

                Transaction::open(); 

                $parametro = Parametro::find($IdParametro);
                Session::setValue('KAIOKEN', clone $parametro);
                Session::getValue('KAIOKEN')->IdParametro = $parametro->IdParametro;
                    
                if ($parametro) 
                    $this->setDataByForm($parametro);               

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

        $parametro = new Parametro(); 
        $dados = $this->form->getData();        

        $IdUsuarioLogado = Session::getValue("IdUsuarioLogado");
        $usuarioLogado = Usuario::find($IdUsuarioLogado);
        
        $parametro->fromArray( (array) $dados);

        if($this->ehStatusAlterar())
            $parametro = $this->getValues($parametro);
        
        $parametro->DataAlteracao =  date('Y-m-d H:i');
        $parametro->UsuarioAlteracao = $usuarioLogado->Usuario;
        
        $parametro->save();
        
        Transaction::close();
        Session::setValue('KAIOKEN', NULL);
    }
    #endregion

    #region ComboBox

    #region setComboBoxUsuario
    private function setComboBoxUsuario($comboBox)
    {
        Transaction::open();
        
        $usuarios = Usuario::all();
        $items = array();

        foreach ($usuarios as $obj)
        {
            $items[$obj->IdUsuario] = $obj->Usuario;
        }
        
        Transaction::close();
        
        $comboBox->addItems($items);

        $this->html = str_replace('{cbUsuario}', $this->toHTML($comboBox), $this->html);
    }
    #endregion

    #region setComboBoxTipoParametro
    private function setComboBoxTipoParametro($comboBox)
    {
        Transaction::open();
        
        $tipos = TipoParametro::all();
        $items = array();

        foreach ($tipos as $obj)
        {
            $items[$obj->IdTipoParametro] = $obj->Descricao;
        }
        
        Transaction::close();
        
        $comboBox->addItems($items);

        $this->html = str_replace('{cbTipoParametro}', $this->toHTML($comboBox), $this->html);
    }
    #endregion

    #endregion

    #region getIdInput
    private function getIdInput($indice) {

        $field = $this->form->getFields()[$indice];
        return $field->getProperty("id");
    }
    #endregion

    #region validarCampos
    private function validarCampos()
    {
        $campos =  $this->form->getData();

        foreach ($campos as $indice => $value) 
        {
            if($this->ehStatusNovo())
            {
                #region Campos Vazios
                if($indice == "IdUsuario" && empty($value))
                {
                    $this->idInput = $this->getIdInput($indice);
                    throw new Exception("Preencha o campo Usuário!");
                }
    
                if($indice == "IdTipoParametro" && empty($value))
                {
                    $this->idInput = $this->getIdInput($indice);
                    throw new Exception("Preencha o campo Tipo Parâmetro!");
                }
    
                if(empty($value))
                {
                    $this->idInput = $this->getIdInput($indice);
                    throw new Exception("Preencha o campo $indice!");
                }
                #endregion

                #region Quantidade de Caracteres

                #region Descricao

                if($indice == "Descricao" && strlen($value) > 200)
                {
                    $this->idInput = $this->getIdInput($indice);
                    throw new Exception("Descrição deve conter menos de 200 caracteres");
                }
                #endregion

                #region Observacao

                if($indice == "Observacao" && strlen($value) > 1000)
                {
                    $this->idInput = $this->getIdInput($indice);
                    throw new Exception("Observação deve conter menos de 1000 caracteres");
                }
                #endregion

                #endregion
            }

            if($indice == "Valor" && empty($value))
            {
                $this->idInput = $this->getIdInput($indice);
                throw new Exception("Preencha o campo $indice!");
            }

            if($indice == "Valor" && strlen($value) > 50)
            {
                $this->idInput = $this->getIdInput($indice);
                throw new Exception("Valor deve conter menos de 50 caracteres");
            }
        }
    }
    #endregion

    #region loadDataForm
    private function loadDataForm()
    {
        foreach ($this->form->getData() as $indice => $value)
        {
            if(!empty($value))
            {
                $id = $this->getIdInput($indice);
                JScript::setDataForm($id, $value);
            }
        }
    }
    #endregion

    #region setDataByForm
    private function setDataByForm($parametro)
    {
        $template = __DIR__ . DIRECTORY_SEPARATOR . "ParametroForm.html";
        $template = file_get_contents($template);

        $this->popularComboBox();
        $this->html = $this->form->getHTML($template, $parametro);

        $template = $this->getHtmlComboBox($this->html);    

        parent::add($template);

        $this->setTextAreaObservacao();
        $this->disabled();
    }
    #endregion

    #region popularComboBox
    private function popularComboBox()
    {
        foreach($this->form->getFields() as $name => $field)
        {
            #region cbUsuario
            if($name == "IdUsuario")
            {
                Transaction::open();
        
                $usuarios = Usuario::all();
                $items = array();

                foreach ($usuarios as $usuario)
                {
                    $items[$usuario->IdUsuario] = $usuario->Usuario;
                }
                
                Transaction::close();
                
                $field->addItems($items);
            }
            #endregion

            #region cbTipoParametro
            if($name == "IdTipoParametro")
            {
                Transaction::open();
        
                $tiposParametro = TipoParametro::all();
                $items = array();

                foreach ($tiposParametro as $tipo)
                {
                    $items[$tipo->IdTipoParametro] = $tipo->Descricao;
                }
                
                Transaction::close();
                
                $field->addItems($items);
            }
            #endregion
        }
    }
    #endregion

    #region getHtmlComboBox
    private function getHtmlComboBox($template)
    {
        #region cbUsuario
        $cbUsuario = $this->form->getFields()["IdUsuario"];        
        $html = str_replace('{cbUsuario}', $this->toHTML($cbUsuario), $template);
        #endregion

        #region cbTipoParametro
        $cbTipoParametro = $this->form->getFields()["IdTipoParametro"];        
        $html = str_replace('{cbTipoParametro}', $this->toHTML($cbTipoParametro), $html);
        #endregion

        return $html;
    }
    #endregion

    #region setTextAreaObservacao
    private function setTextAreaObservacao()
    {
        $txtObservacao = $this->form->getFields()["Observacao"];
        $id = $txtObservacao->id;
        $value = $txtObservacao->getValue();

        JScript::setDataForm($id, $value);
    }
    #endregion
    
    #region disabled

    private function disabled()
    {
        $campos =  $this->form->getFields();

        foreach ($campos as $name => $field) 
        {
            if($name != "Valor")
            {
                $script = "disabled('$field->id')";
                JScript::run($script, 100);
            }
        }
    }
    #endregion
    
    #region ehStatusNovo
    private function ehStatusNovo()
    {
        $ehNovo = Session::getValue('PageStatus') == PageStatusEnum::Novo->value;
        return $ehNovo;
    }
    #endregion

    #region ehStatusAlterar
    private function ehStatusAlterar()
    {
        $ehAlterar = Session::getValue('PageStatus') == PageStatusEnum::Alterar->value;
        return $ehAlterar;
    }
    #endregion

    #region getValues
    private function getValues($parametro)
    {
        if(isset(Session::getValue('KAIOKEN')->IdParametro))
            $parametro->IdParametro = Session::getValue('KAIOKEN')->IdParametro;
        
        $parametro->IdUsuario = Session::getValue('KAIOKEN')->IdUsuario;
        $parametro->IdTipoParametro = Session::getValue('KAIOKEN')->IdTipoParametro;
        $parametro->Descricao = Session::getValue('KAIOKEN')->Descricao;
        $parametro->Observacao = Session::getValue('KAIOKEN')->Observacao;

        return $parametro;
    }
    #endregion

    #endregion

    #endregion
}