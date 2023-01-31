<?php

namespace KaiokenFramework\Components\Container;

use KaiokenFramework\Components\Base\Element;

/**
 * Caixa vertical
 * @author Willian Brito (h1s0k4)
 */
class VBox extends Element
{
    #region Construtor

    /**
     * Método construtor
    */
    public function __construct()
    {
        parent::__construct('div');
        $this->{'style'} = 'display: inline-block;';
    }
    #endregion
    
    #region add
    
    /**
     * Adiciona um elemento filho
     * @param $child Objeto filho
    */
    public function add($child)
    {
        $wrapper = new Element('div');
        $wrapper->{'style'} = 'clear:both';
        $wrapper->add($child);
        parent::add($wrapper);

        return $wrapper;
    }
    #endregion
}
