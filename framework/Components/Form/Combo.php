<?php

namespace KaiokenFramework\Components\Form;

use KaiokenFramework\Components\Base\Element;

/**
 * Representa uma combo box
 * @author Willian Brito (h1s0k4)
*/
class Combo extends Field implements IFormElement
{
    #region Propriedades da Classe

    private $items; // array contendo os itens da combo
    protected $properties;
    #endregion
    
    #region Metodos

    #region addItems

    /**
     * Adiciona items à combo box
     * @param $items = array de itens
    */
    public function addItems($items)
    {
        $this->items = $items;
    }
    #endregion
    
    #region show

    /**
     * Exibe o componente na tela
    */
    public function show()
    {
        $tag = $this->getComboBox();        
        $tag->show();
    }
    #endregion

    #region getComboBox
    private function getComboBox()
    {
        $tag = new Element('select');
        $tag->class = 'combo';
        $tag->name = $this->name;
        $tag->style = "width:{$this->size}"; // tamanho em pixels
        
        // cria uma TAG <option> com um valor padrão
        $cont = 1;
        $option = new Element('option');
        $option->add('SELECIONE');
        $option->value = '0';    // valor da TAG
        
        // adiciona a opção à combo
        $tag->add($option);
        if ($this->items)
        {
            // percorre os itens adicionados
            foreach ($this->items as $chave => $item)
            {
                
                // cria uma TAG <option> para o item
                $option = new Element('option');
                $option->value = $chave; // define o índice da opção
                $option->add($item);     // adiciona o texto da opção
                
                if ($cont == 1)
                {
                    $option->selected = 1;
                }

                // caso seja a opção selecionada
                if ($chave == $this->value)
                {
                    // seleciona o item da combo
                    $option->selected = 1;
                } 
                // adiciona a opção à combo
                $tag->add($option);
                $cont++;
            }
        } 
        
        // verifica se o campo é editável
        if (!parent::getEditable())
        {
            // desabilita a TAG input
            $tag->readonly = "1";
        }
        
        if ($this->properties)
        {
            foreach ($this->properties as $property => $value)
            {
                $tag->$property = $value;
            }
        }

        return $tag;
    }
    #endregion

    #region getHtml
    public function getHtml()
    {
        $tag = $this->getComboBox();

        return $tag;
    }
    #endregion

    #endregion
}
