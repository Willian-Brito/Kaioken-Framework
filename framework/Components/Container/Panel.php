<?php

namespace KaiokenFramework\Components\Container;

use KaiokenFramework\Components\Base\Element;

/**
 * Empacota elementos em painel Bootstrap
 * @author Willian Brito (h1s0k4)
*/
class Panel extends Element
{

    #region Propriedades da Classe

    private $body;
    private $footer;
    #endregion
    
    #region Construtor

    /**
     * Constrói o painel
    */
    public function __construct($panel_title = NULL)
    {
        parent::__construct('div');
        $this->class = 'panel panel-default';
        
        if ($panel_title)
        {
            $head = new Element('div');
            $head->class = 'panel-heading';
        
            $label = new Element('h4');
            $label->add($panel_title);
            
            $title = new Element('div');
            $title->class = 'panel-title';
            $title->add( $label );
            $head->add($title);
            parent::add($head);
        }
        
        $this->body = new Element('div');
        $this->body->class = 'panel-body';
        parent::add($this->body);
        
        $this->footer = new Element('div');
        $this->footer->{'class'} = 'panel-footer';
        
    }
    #endregion

    #region Metodos

    #region add

    /**
     * Adiciona conteúdo
    */
    public function add($content)
    {
        $this->body->add($content);
    }
    #endregion

    #region addFooter

    /**
     * Adiciona rodapé
    */
    public function addFooter($footer)
    {
        $this->footer->add( $footer );
        parent::add($this->footer);
    }
    #endregion

    #endregion

}
