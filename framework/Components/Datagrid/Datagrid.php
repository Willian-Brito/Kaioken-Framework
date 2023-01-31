<?php

namespace KaiokenFramework\Components\Datagrid;

use KaiokenFramework\Components\Form\CheckButton;
use KaiokenFramework\Page\IAction;

/**
 * Representa uma Datagrid
 * @author Willian Brito (h1s0k4)
 */
class Datagrid
{
    #region Propriedades da Classe

    private $columns;
    private $rows;
    private $actions;
    private $checkBox;
    #endregion

    #region Metodos

    #region addColumn
    /**
     * Adiciona uma coluna à datagrid
     * @param $object = objeto do tipo DatagridColumn
    */
    public function addColumn(DatagridColumn $object)
    {
        $this->columns[] = $object;
    }
    #endregion

    #region addAction

    /**
     * Adiciona uma ação à datagrid
     * @param $label  = rótulo
     * @param $action = ação
     * @param $field  = campo
     * @param $image  = imagem
    */
    public function addAction($label, IAction $action, $field, $image = null)
    {
        $this->actions[] = ['label' => $label, 'action'=> $action, 'field' => $field, 'image' => $image];
    }
    #endregion

    #region addChecked
    /**
     * Seleciona a linha
     * @param $checkButton = seleção
     * @param $action = ação
    */
    public function addCheckBox($field)
    {
        $this->checkBox = $field;
    }
    #endregion

    #region addRow

    /**
     * Adiciona uma linha na grid
     * @param $object = Objeto que contém os dados
    */
    public function addRow($object)
    {
        $this->rows[] = $object;
        
        foreach ($this->columns as $column)
        {
            $name = $column->getName();
            if (!isset($object->$name))
            {
                // chama o método de acesso
                $object->$name;
            }
        }
    }
    #endregion
    
    #region getColumns

    /**
     * Return columns
    */
    public function getColumns()
    {
        return $this->columns;
    }
    #endregion
    
    #region getItems

    /**
     * Return rows
    */
    public function getRows()
    {
        return $this->rows;
    }
    #endregion

    #region getActions

    /**
     * Return actions
    */
    public function getActions()
    {
        return $this->actions;
    }
    #endregion

    #region getActions

    /**
     * Return checkeds
    */
    public function getCheckBox()
    {
        if($this->checkBox)
        {
            return $this->checkBox;
        }
    }
    #endregion
    
    #region clear

    /**
     * Limpa os rows
    */
    function clear()
    {
        $this->rows = [];
    }
    #endregion

    #endregion
}
