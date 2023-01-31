<?php

namespace KaiokenFramework\Components\Form;

use KaiokenFramework\Components\Base\Element;

/**
 * Representa um grupo de Radio Buttons
 * @author Willian Brito (h1s0k4)
*/
class RadioGroup extends Field implements IFormElement
{
    #region Propriedades da Classe

    private $layout = 'vertical';
    private $items;
    #endregion

    #region Metodos

    #region setLayout

    /**
     * Define a direção das opções (vertical ou horizontal)
    */
    public function setLayout($dir)
    {
        $this->layout = $dir;
    }
    #endregion
    
    #region addItems

    /**
     * Adiciona itens (botões de rádio)
     * @param $items = array indexado contendo os itens
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
        if ($this->items)
        {
            // percorre cada uma das opções do rádio
            foreach ($this->items as $index => $label)
            {
                $button = new RadioButton($this->name);
                $button->setValue($index);
                // se o índice coincide
                if ($this->value == $index)
                {
                    // marca o radio button
                    $button->setProperty('checked', '1');
                }
                
                $obj = new Label($label);
                $obj->add($button);
                $obj->show();
                if ($this->layout == 'vertical')
                {
                    // exibe uma tag de quebra de linha
                    $br = new Element('br');
                    $br->show();
                }
                echo "\n";
            }
        }
    }
    #endregion

    #endregion
}
