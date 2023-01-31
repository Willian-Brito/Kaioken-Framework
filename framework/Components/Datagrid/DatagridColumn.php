<?php

namespace KaiokenFramework\Components\Datagrid;

use KaiokenFramework\Page\IAction;

/**
 * Representa uma coluna de uma datagrid
 * @author Willian Brito (h1s0k4)
 */
class DatagridColumn
{
    #region Propriedades da Classe

    private $name;
    private $label;
    private $align;
    private $width;
    private $action;
    private $transformer;
    #endregion

    #region Construtor

    /**
     * Instancia uma coluna nova
     * @param $name = nome da coluna no banco de dados
     * @param $label = rótulo de texto que será exibido
     * @param $align = alinhamento da coluna (left, center, right)
     * @param $width = largura da coluna (em pixels)
    */
    public function __construct($name, $label, $align, $width)
    {
        // atribui os parâmetros às propriedades do objeto
        $this->name = $name;
        $this->label = $label;
        $this->align = $align;
        $this->width = $width;
    }
    #endregion
    
    #region Metodos

    #region getName

    /**
     * Retorna o nome da coluna no banco de dados
    */
    public function getName()
    {
        return $this->name;
    }
    #endregion

    #region getLabel

    /**
     * Retorna o nome do rótulo de texto da coluna
    */
    public function getLabel()
    {
        return $this->label;
    }
    #endregion
    
    #region getAlign

    /**
     * Retorna o alinhamento da coluna (left, center, right)
    */
    public function getAlign()
    {
        return $this->align;
    }
    #endregion
    
    #region getWidth

    /**
     * Retorna a largura da coluna (em pixels)
    */
    public function getWidth()
    {
        return $this->width;
    }
    #endregion

    #region setAction

    /**
     * Define uma ação a ser executada quando o usuário clicar sobre o título da coluna
     * @param $action = objeto TAction contendo a ação
    */
    public function setAction(IAction $action)
    {
        $this->action = $action;
    }
    #endregion
    
    #region getAction

    /**
     * Retorna a ação vinculada à coluna
    */
    public function getAction()
    {
        // verifica se a coluna possui ação
        if ($this->action)
        {
            return $this->action->serialize();
        }
    }
    #endregion
    
    #region setTransformer

    /**
     * Define uma função (callback) a ser aplicada sobre a coluna
     * @param $callback = função do PHP ou do usuário
    */
    public function setTransformer(Callable $callback)
    {
        $this->transformer = $callback;
    }
    #endregion
    
    #region getTransformer

    /**
     * Retorna a função (callback) aplicada à coluna
    */
    public function getTransformer()
    {
        return $this->transformer;
    }
    #endregion

    #endregion
}
