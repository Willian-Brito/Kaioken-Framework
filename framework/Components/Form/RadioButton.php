<?php

namespace KaiokenFramework\Components\Form;

use KaiokenFramework\Components\Base\Element;

/**
 * Representa um RadioButton
 * @author Willian Brito (h1s0k4)
*/
class RadioButton extends Field implements IFormElement
{
    #region Metodos

    #region show

    /**
     * Exibe o componente na tela
    */
    public function show()
    {
        $tag = new Element('input');
        $tag->class = 'field';		  // classe CSS
        $tag->name = $this->name;
        $tag->value = $this->value;
        $tag->type = 'radio';
        
        // se o campo não é editável
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
        
        // exibe a tag
        $tag->show();
    }
    #endregion

    #endregion
}
