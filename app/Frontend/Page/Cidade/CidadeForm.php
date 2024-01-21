<?php

#region Imports
use KaiokenFramework\Page\Page;
use KaiokenFramework\Page\Action;

use KaiokenFramework\Components\Form\Form;
use KaiokenFramework\Components\Form\Text;
use KaiokenFramework\Components\Container\VBox;
use KaiokenFramework\Components\Wrapper\KaiokenFormWrapper;
use KaiokenFramework\Components\Dialog\Message;
use KaiokenFramework\Components\Form\Combo;
use KaiokenFramework\Components\Form\TextArea;
use KaiokenFramework\Database\Criteria;
use KaiokenFramework\Database\Repository;
use KaiokenFramework\Database\Transaction;

use KaiokenFramework\Traits\EditTrait;
use KaiokenFramework\Traits\ReloadTrait;
use KaiokenFramework\Traits\SaveTrait;
#endregion

/**
 * Tela de Cadastro de cidades
 */
class CidadeForm extends Page 
{
    #region Objetos

    private $form;
    private $loaded;
    private $idInput;
    #endregion 

    #region Traits

    // use ReloadTrait;
    use EditTrait;
    use SaveTrait { onSave as onSaveTrait; }
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

    #region onSave
    public function onSave()
    {
        try
        {
            $this->validarCampos();
            $this->onSaveTrait();
        }
        catch(Exception $ex)
        {
            new Message('error', $ex->getMessage(), 5000, "#$this->idInput");
            $this->form->loadData();
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
            if (isset($param['key']))
            {
                $id = $param['key'];

                Transaction::open(); 

                $cidade = Cidade::find($id);

                if ($cidade) 
                    $this->form->setData($cidade);                

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

    #region show

    /**
     * exibe a página
     */
    public function show()
    {
        parent::show();
    }
    #endregion

    #region createPage
    private function createPage()
    {
        $this->activeRecord = 'Cidade';
        
        #region Criar Formulario

        // instancia um formulário
        $this->form = new KaiokenFormWrapper(new Form('form_cidades'));
        $this->form->setTitle('Cadastro de Cidades');
              
        // cria os campos do formulário
        $codigo    = new Text('IdCidade');
        $codigo->maxlength = "15";

        $descricao = new Text('Descricao');
        $descricao->id = "txtDescricao";
        $descricao->maxlength = "100";

        $codigoIBGE = new Text('CodigoIBGE');
        $codigoIBGE->id = "txtCodigoIBGE";  
        $codigoIBGE->maxlength = "10";

        $estado    = new Combo('IdEstado');
        $estado->id = "cbEstado";

        Transaction::open();

        $nextCode = $this->newCode();
        $codigo->setEditable(FALSE, $nextCode);

        $estados = Estado::all();
        $items = array();

        foreach ($estados as $obj_estado)
        {
            $items[$obj_estado->IdEstado] = $obj_estado->Descricao;
        }
        
        Transaction::close();
        
        $estado->addItems($items);

        $this->form->addField('Código', $codigo, '15%');
        $this->form->addField('Descrição', $descricao, '70%');
        $this->form->addField('Código IBGE', $codigoIBGE, '20%');
        $this->form->addField('Estado', $estado, '70%');
        
        $this->form->addAction('Salvar', new Action(array($this, 'onSave')));
        $this->form->addAction('Cancelar', new Action(array($this, 'onEdit')));
        #endregion
        
        // monta a página através de uma tabela
        $box = new VBox;
        $box->style = 'display:block';
        $box->add($this->form);

        parent::add($box);
    }
    #endregion

    #region validarCampos
    private function validarCampos()
    {    
        foreach ($this->form->getData() as $indice => $value) 
        {    
            
            #region Campos Vazios

            if($indice == "IdCidade" && empty($value))
            {
                $this->idInput = $this->getIdInput($indice);
                throw new Exception("Preencha o campo Código");
            }

            if($indice == "IdEstado" && empty($value)) 
            {                    
                $this->idInput = $this->getIdInput($indice);
                throw new Exception("Preencha o campo Estado!");
            }
            
            if(empty($value))
            {
                $this->idInput = $this->getIdInput($indice);
                throw new Exception("Preencha o campo $indice!");
            }
            #endregion 

            #region Quantidade de Caracteres

            #region Codigo
            if($indice == "IdCidade" && strlen($value) > 10)
            {
                $this->idInput = $this->getIdInput($indice);
                throw new Exception("Código deve conter menos de 10 caracteres");
            }
            #endregion

            #region Descricao

            if($indice == "Descricao" && strlen($value) > 100)
            {
                $this->idInput = $this->getIdInput($indice);
                throw new Exception("Descrição deve conter menos de 100 caracteres");
            }
            #endregion

            #region CodigoIBGE

            if($indice == "CodigoIBGE" && strlen($value) > 10)
            {
                $this->idInput = $this->getIdInput($indice);
                throw new Exception("Código IBGE deve conter menos de 10 caracteres");
            }
            #endregion

            #endregion          
        }
    }
    #endregion

    #region getIdInput
    function getIdInput($indice) {

        $field = $this->form->getFields()[$indice];
        return $field->getProperty("id");
    }
    #endregion

    #region newCode
    private function newCode()
    {
        $cidade = new Cidade();
        return $cidade->getLastId() + 1;
    }
    #endregion

    #endregion
}