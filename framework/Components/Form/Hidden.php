<?php

namespace KaiokenFramework\Components\Form;

use KaiokenFramework\Components\Base\Element;

/**
 * Representa um campo escondido
 * @author Willian Brito (h1s0k4)
*/
class Hidden extends Field implements IFormElement
{
    #region Propriedades da Classe

    protected $properties;
    #endregion
    
    #region Metodos

    #region show

    /**
     * Exibe o componente na tela
    */
    public function show()
    {
        // atribui as propriedades da TAG
        $tag = new Element('input');
        $tag->class = 'field';		  // classe CSS
        $tag->name = $this->name;     // nome da TAG
        $tag->value = $this->value;   // valor da TAG
        $tag->type = 'hidden';        // tipo de input
        $tag->style = "width:{$this->size}"; // tamanho em pixels
        
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
