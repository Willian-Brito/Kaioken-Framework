<?php

namespace KaiokenFramework\Components\Form;

use KaiokenFramework\Components\Base\Element;

/**
 * Representa um grupo de CheckButtons
 * @author Willian Brito (h1s0k4)
 */
class CheckGroup extends Field implements IFormElement
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
     * Adiciona itens ao check group
     * @param $items = um vetor indexado de itens
    */
    public function addItems($items)
    {
        $this->items = $items;
    }
    #endregion
    
    #region show

    /**
     * exibe o componente na tela
    */
    public function show()
    {
        if ($this->items)
        {
            // percorre cada uma das opções do rádio
            foreach ($this->items as $index => $label)
            {
                $button = new CheckButton("{$this->name}[]");
                $button->setValue($index);
                
                // verifica se deve ser marcado
                if (in_array($index, (array) $this->value))
                {
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
                    echo "\n";
                }
            }
        }
    }
    #endregion

    #endregion
}
