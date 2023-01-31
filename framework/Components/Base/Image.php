<?php

namespace KaiokenFramework\Components\Base;

/**
 * Representa uma imagem
 * @author Willian Brito (h1s0k4)
*/
class Image extends Element
{
    private $source; // localização da imagem
    
    #region Construtor

    /**
     * Instancia uma imagem
     * @param $source = localização da imagem
    */
    public function __construct($source)
    {
        parent::__construct('img');
        
        // atribui a localização da imagem
        $this->src = $source;
        $this->border = 0;
    }
    #endregion
}