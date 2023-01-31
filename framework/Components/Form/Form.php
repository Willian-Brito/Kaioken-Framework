<?php

namespace KaiokenFramework\Components\Form;

use KaiokenFramework\Page\IAction;

/**
 * Representa um formulário
 * @author Willian Brito (h1s0k4)
 */
class Form
{
    #region Propriedades da Classe

    protected $title;
    protected $name;
    protected $fields;
    protected $actions;
    protected $useJS;
    protected $html;
    #endregion
    
    #region Construtor
    
    /**
     * Instancia o formulário
     * @param $name = nome do formulário
     */
    public function __construct($name = 'kaioken_form')
    {
        $this->setName($name); 
    }
    #endregion

    #region Metodos

    #region getUseJS
    /**
     * Retorna se formulario irá utilizar JS separado
    */
    public function getUseJS()
    {
        return $this->useJS;
    }
    #endregion

    #region setUseJS
    /** 
    * Define se formulario irá utilizar JS separado
    * @param $value = utiliza JS separado
    */
    public function setUseJS($value)
    {
        $this->useJS = $value;
    }
    #endregion

    #region setName

    /**
     * Define o nome do formulário
     * @param $name = nome do formulário
     */
    public function setName($name)
    {
        $this->name = $name;
    }
    #endregion
    
    #region getName

    /**
     * Retorna o nome do formulário
     */
    public function getName()
    {
        return $this->name;
    }
    #endregion
    
    #region setTitle

    /**
     * Define o título do formulário
     * @param $title Título
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }
    #endregion
    
    #region getTitle

    /**
     * Retorna o título do formulário
     */
    public function getTitle()
    {
        return $this->title;
    }
    #endregion
    
    #region addField

    /**
     * Add a form field
     * @param $label     Field Label
     * @param $object    Field Object
     * @param $size      Field Size
     */
    public function addField($label, IFormElement $object, $size = '100%')
    {
        $object->setSize($size);
        $object->setLabel($label);
        $this->fields[$object->getName()] = $object;
    }
    #endregion
    
    #region addAction

    /**
     * Adiciona uma ação
     * @param $label  Action Label
     * @param $action TAction Object
     */
    public function addAction($label, IAction $action)
    {
        $this->actions[$label] = $action;
    }
    #endregion

    #region getFields

    /**
     * Retorna os campos
     */
    public function getFields()
    {
        return $this->fields;
    }
    #endregion
    
    #region getActions

    /**
     * Retorna as ações
     */
    public function getActions()
    {
        return $this->actions;
    }
    #endregion

    #region setData

    /**
     * Atribui dados aos campos do formulário
     * @param $object = objeto com dados
    */
    public function setData($object)
    {
        foreach ($this->fields as $name => $field)
        {
            if ($name AND isset($object->$name))
            {
                $value = htmlspecialchars($object->$name, double_encode:false);
                $field->setValue($value);
            }
        }
    }
    #endregion
    
    #region getData

    /**
     * Retorna os dados do formulário em forma de objeto
     */
    public function getData($class = 'stdClass')
    {
        $object = new $class;
        
        foreach ($this->fields as $key => $fieldObject)
        {
            $valor = isset($_POST[$key]) ? $_POST[$key] : '';
            $valorSanitizado = empty($valor) ? '' : strip_tags($valor); //htmlspecialchars($valor, double_encode:false);
            $object->$key = $valorSanitizado;
        }

        // percorre os arquivos de upload
        foreach ($_FILES as $key => $content)
        {
            $object->$key = $content['tmp_name'];
        }
        return $object;
    }
    #endregion

    #region loadData

    /*
    * Carrega dados no formulário
    */
    public function loadData()
    {
        $dados = $this->getData();
        $this->setData($dados);
    }
    #endregion

    #region getHTML
    public function getHTML($html, $object)
    {
        $this->html = $html;

        foreach ($this->getFields() as $name => $field)
        {
            if ($name AND isset($object->$name))
            {
                $value = htmlspecialchars($object->$name, double_encode:false);

                $field->setValue($value);
                $value = "value=\"{$value}\"";

                $this->html = str_replace("{{$name}}", $value, $this->html);
            }
        }
        
        return $this->html;
    }
    #endregion

    #endregion
}
