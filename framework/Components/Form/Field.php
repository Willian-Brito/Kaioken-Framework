<?php

namespace KaiokenFramework\Components\Form;

use KaiokenFramework\Components\Base\Element;

/**
 * Representa um campo de um formulário
 * @author Willian Brito (h1s0k4)
*/
abstract class Field implements IFormElement
{
    #region Propriedades da Classe

    protected $name; 
    protected $size;
    protected $value;
    protected $editable;
    protected $formLabel;
    protected $properties;
    protected $useJS;
    #endregion

    #region Construtor

    /**
     * Instancia um campo do formulario
     * @param $name = nome do campo
    */
    public function __construct($name)
    {
        // define algumas características iniciais
        self::setEditable(true);
        self::setName($name);
    }
    #endregion

    #region Metodos

    #region Magicos

    #region set

    /**
     * Intercepta a atribuição de propriedades
     * @param $name     Nome da propriedade
     * @param $value    Valor da propriedade
    */
    public function __set($name, $value)
    {
        // Somente valores escalares
        if (is_scalar($value))
        {              
            // Armazena o valor da propriedade
            $this->setProperty($name, $value);
        }
    }
    #endregion

    #region get

    /**
     * Retorna o valor da propriedade
     * @param $name Nome da propriedade
    */
    public function __get($name)
    {
        return $this->getProperty($name);
    }
    #endregion

    #endregion
    
    #region Publico

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
     * Define o nome do componente
     * @param $name     = nome do componente
    */
    public function setName($name)
    {
        $this->name = $name;
    }
    #endregion
    
    #region getName

    /**
     * Retorna o nome do componente
    */
    public function getName()
    {
        return $this->name;
    }
    #endregion
    
    #region setLabel

    /**
     * Define o label do componente
     * @param $label = componente label
    */
    public function setLabel($label)
    {
        $this->formLabel = $label;
    }
    #endregion
    
    #region getLabel
    /**
     * Retorna o label do componente
    */
    public function getLabel()
    {
        return $this->formLabel;
    }
    #endregion

    #region setValue

    /**
     * Define o valor de um campo
     * @param $value    = valor do campo
    */
    public function setValue($value)
    {
        $this->value = $value;
    }
    #endregion
    
    #region getValue

    /**
     * Retorna o valor de um campo
    */
    public function getValue()
    {
        return $this->value;
    }
    #endregion
    
    #region setEditable

    /**
     * Define se o campo poderá ser editado
     * @param $editable = TRUE ou FALSE
    */
    public function setEditable($editable, $value = "")
    {
        $this->setValue($value);
        $this->editable= $editable;
    }
    #endregion
    
    #region getEditable

    /**
     * Retorna o valor da propriedade $editable
    */
    public function getEditable()
    {
        return $this->editable;
    }
    #endregion
    
    #region setProperty

    /**
     * Define uma propriedade para o campo
     * @param $name = nome da propriedade
     * @param $valor = valor da propriedade
    */
    public function setProperty($name, $value)
    {
        $this->properties[$name] = $value;
    }
    #endregion
    
    #region getProperty

    /**
     * Retorna uma propriedade do campo
    */
    public function getProperty($name)
    {
        return $this->properties[$name];
    }
    #endregion
    
    #region setSize

    /**
     * Define a largura do componente
     * @param $width = largura em pixels
     * @param $height = altura em pixels (usada em TText)
    */
    public function setSize($width, $height = NULL)
    {
        $this->size = $width;
    }
    #endregion

    #endregion

    #endregion
}
