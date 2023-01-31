<?php

namespace KaiokenFramework\Components\Wrapper;

use KaiokenFramework\Components\Container\Panel;
use KaiokenFramework\Components\Datagrid\Datagrid;
use KaiokenFramework\Components\Base\Element;

/**
 * Decora datagrids no formato Kaioken Framework
 * @author Willian Brito (h1s0k4)
*/
class KaiokenDatagridWrapper
{
    #region Propriedades da Classe
    
    private $decorated;
    #endregion

    #region Construtor

    /**
     * Constrói o decorator
     */
    public function __construct(Datagrid $datagrid)
    {
        $this->decorated = $datagrid;
    }
    #endregion
    
    #region Metodos

    #region Magicos
    
    #region call

    /**
     * Redireciona chamadas para o objeto decorado (datagrid)
    */
    public function __call($method, $parameters) // É ativado sempre que é chamado um método que não existe
    {
        return call_user_func_array(array($this->decorated, $method), $parameters);
    }
    #endregion
    
    #region set

    /**
     * Redireciona alterações em atributos
    */
    public function __set($attribute, $value)
    {
        $this->decorated->$attribute = $value;
    }
    #endregion

    #endregion

    #region Publico

    #region show

    /**
     * Exibe a datagrid
    */
    public function show()
    {
        $div = new Element('div');
        $div->style = "overflow-y: unset;overflow-x: scroll;";

        $table = new Element('table');
        $table->class = 'data-grid-view';
        
        #region cria o header
        $thead = new Element('thead');
        $table->add($thead);
        $this->createHeaders($thead);
        #endregion

        #region cria o body
        $tbody = new Element('tbody');
        $tbody->style = "height: 400px;";
        $tbody->data_auto_height="true"; 
        $tbody->data_min_height="125"; 
        $tbody->data_padding_bottom="0";
        $table->add($tbody);
        #endregion

        #region cria as linhas
        $rows = $this->decorated->getRows();
        foreach ($rows as $row)
        {
            $this->createRow($tbody, $row);
        }
        #endregion
        
        #region cria o painel e add a grid
        $panel = new Panel;
        $panel->type = 'datagrid';
        $div->add($table);
        $panel->add($div);
        $panel->show();
        #endregion
        
        #region footer
        $footer = new Element('div');
        $footer->class = 'data-grid-view-footer';
        $footer->show();
        #endregion
    }
    #endregion
    
    #region createHeaders

    /**
     * Cria a estrutura da Grid, com seu cabeçalho
    */
    public function createHeaders($thead)
    {
        // adiciona uma linha à tabela
        $row = new Element('tr');
        $thead->add($row);
        
        $actions = $this->decorated->getActions();
        $columns = $this->decorated->getColumns();

        #region Actions

        // adiciona células para as ações
        if ($actions)
        {
            foreach ($actions as $action)
            {
                $celula = new Element('th');
                $celula->style = "min-width: 48px; max-width: 48px;";
                $celula->width = '48px';
                $row->add($celula);
            }
        }
        #endregion
        
        #region Columns

        // adiciona as células para os títulos das colunas
        if ($columns)
        {
            // percorre as colunas da listagem
            foreach ($columns as $column)
            {

                // obtém as propriedades da coluna
                $label = $column->getLabel();
                $align = $column->getAlign();
                $width = $column->getWidth();
                
                $celula = new Element('th');
                $celula->add($label);
                $celula->style = "text-align: {$align}; min-width: {$width}; max-width: {$width}; white-space: nowrap;";
                $celula->width = $width;
                $row->add($celula);                
    
                // verifica se a coluna tem uma ação
                if ($column->getAction())
                {
                    $url = $column->getAction();
                    $celula->onclick = "document.location='$url'";
                }
                
            }
        }

        #region Ultima Celula
        $element = new Element('th');
        $element->style = "width: 100%;";
        $row->add($element);
        #endregion

        #endregion
    }
    #endregion
    
    #region createRow

    public function createRow($tbody, $item)
    {
        $row = new Element('tr');
        $tbody->add($row);
        
        $actions = $this->decorated->getActions();
        $columns = $this->decorated->getColumns();
        $CheckBox = $this->decorated->getCheckBox();
        
        #region CheckBox

        if($CheckBox)
        {
            $name = $columns[1]->getName();;
            $data     = $item->$name;

            if(!empty($data))
            {
                $field = $CheckBox;
                $ID    = $item->$field;

                $tag = new Element('input');
                $tag->class = 'field';
                $tag->value = $ID;
                $tag->type = 'checkbox';
                $tag->id = "selecionado"; 

                $element = new Element('td');
                $element->add($tag);                
                $element->align = 'center';
                
                // adiciona a célula à linha
                $row->add($element); 
            }
        }
        #endregion

        #region Actions
        // verifica se a listagem possui ações
        if ($actions)
        {
            // percorre as ações
            foreach ($actions as $action)
            {
                // obtém as propriedades da ação
                $url   = $action['action']->serialize();
                $label = $action['label'];
                $image = $action['image'];
                $field = $action['field'];
                
                // obtém o campo do objeto que será passado adiante
                $key    = $item->$field;
                
                // cria um link
                $link = new Element('a');
                $link->href = "{$url}&key={$key}&{$field}={$key}";
                
                // verifica se o link será com imagem ou com texto
                if ($image)
                {                    
                    $i = new Element('i'); 

                    $link->class = $this->getBtn($image);               
                    
                    $i->class = "$image";
                    $i->title = $label;
                    $i->add('');
                    $link->add($i);

                    // $button->add($link);
                }
                else
                {
                    // adiciona o rótulo de texto ao link
                    $link->add($label);
                }
                
                $element = new Element('td');
                $element->add($link);
                $element->align = 'center';
                
                // adiciona a célula à linha
                $row->add($element);
            }
        }
        #endregion
        
        #region Columns
        if ($columns)
        {
            // percorre as colunas da Datagrid
            foreach ($columns as $column)
            {
                // obtém as propriedades da coluna
                $name     = $column->getName();
                $align    = $column->getAlign();
                $width    = $column->getWidth();
                $function = $column->getTransformer();
                $data     = $item->$name;
                
                if(!empty($data))
                {
                    $value = htmlspecialchars($data, double_encode:false);

                    // verifica se há função para transformar os dados
                    if ($function)
                    {
                        // aplica a função sobre os dados
                        $data = call_user_func($function, $value);
                    }
                    
                    $element = new Element('td');
                    $element->title = $value;
                    $element->add($value);
                    $element->align = $align;

                    $element->style = "font-size: 13px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; min-width: {$width}; max-width: {$width};";
                    $element->width = $width;
                    
                    // adiciona a célula na linha
                    $row->add($element);
                }                
            }

            if(!empty($data))
            {
                 #region Ultima Celula
                $element = new Element('td');
                $element->style = "width: 100%;";
                $row->add($element);
                #endregion
            }

           
        }
        #endregion
    }
    #endregion

    #region getBtn
    private function getBtn($image)
    {
        if(str_contains($image, 'fa-pencil'))
            return 'btn ' . 'btn-warning';

        if(str_contains($image, 'fa-trash'))
            return 'btn ' . 'btn-danger';

        if(str_contains($image, 'fa-key'))
            return 'btn ' . 'alert-info';

        if(str_contains($image, 'fa-arrow-down'))
            return 'btn ' . 'btn-download';
    }
    #endregion

    #endregion

    #endregion
}
