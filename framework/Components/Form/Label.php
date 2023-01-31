<?php

namespace KaiokenFramework\Components\Form;

use KaiokenFramework\Components\Base\Element;

/**
 * Representa um rótulo de texto
 * @author Willian Brito (h1s0k4)
*/
class Label extends Field implements IFormElement
{
    #region Propriedades da Classe
    
    protected $tag;
    #endregion

    #region Construtor

    /**
     * Construtor
     * @param $value text label
     */
    public function __construct($value)
    {
        // set the label's content
        $this->setValue($value);
        
        // create a new element
        $this->tag = new Element('label');
    }
    #endregion
    
    #region Metodos

    #region add

    /**
     * Adiciona conteúdo no label
    */
    public function add($child)
    {
        $this->tag->add($child);
    }
    #endregion
    
    #region show

    /**
     * Exibe o componente
    */
    public function show()
    {
        $this->tag->add($this->value);
        $this->tag->show();
    }
    #endregion

    #endregion
}
