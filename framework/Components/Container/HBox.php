<?php
namespace KaiokenFramework\Components\Container;

use KaiokenFramework\Components\Base\Element;

/**
 * Caixa horizontal
 * @author Willian Brito (h1s0k4)
 */
class HBox extends Element
{
    #region Construtor

    /**
     * Método construtor
    */
    public function __construct()
    {
        parent::__construct('div');
    }
    #endregion

    #region Metodos

    #region add

    /**
     * Adiciona um elemento filho
     * @param $child Objeto filho
    */
    public function add($child)
    {
        $wrapper = new Element('div');
        $wrapper->{'style'} = 'display:inline-block; width: 50%;';
        $wrapper->add($child);
        parent::add($wrapper);
        
        return $wrapper;
    }
    #endregion

    #endregion
}
