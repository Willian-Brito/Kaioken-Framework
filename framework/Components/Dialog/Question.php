<?php

namespace KaiokenFramework\Components\Dialog;

use KaiokenFramework\Page\Action;
use KaiokenFramework\Components\Base\Element;

/**
 * Exibe perguntas ao usuário
 * @author Willian Brito (h1s0k4)
 */
class Question
{
    #region Construtor
    
    /**
     * Instancia o questionamento
     * @param $message = pergunta ao usuário
     * @param $action_yes = ação para resposta positiva
     * @param $action_no = ação para resposta negativa
    */
    function __construct($message, Action $action_yes, Action $action_no = NULL)
    {
        $div = new Element('div');
        $div->class = 'material-alert info question';
        
        // converte os nomes de métodos em URL's
        $url_yes = $action_yes->serialize();
        
        $div_link = new Element('div');

        $link_yes = new Element('a');
        $link_yes->href = $url_yes;
        $link_yes->class = 'btn btn-default';
        $link_yes->style = 'float:right';
        $link_yes->add('Sim');
        
        if ($action_no)
        {
            $url_no = $action_no->serialize();
            
            $link_no = new Element('a');
            $link_no->href = $url_no;
            $link_no->class = 'btn btn-default';
            $link_no->style = 'float:right';
            $link_no->add('Não');
        }
        
        $div_link->add($link_no);
        $div_link->add($link_yes);

        $div->add($message);
        $div->add($div_link);

        $div->show();
    }
    #endregion
}
