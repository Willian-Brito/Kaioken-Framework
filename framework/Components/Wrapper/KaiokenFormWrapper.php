<?php

#region Imports

namespace KaiokenFramework\Components\Wrapper;

use KaiokenFramework\Components\Container\Panel;
use KaiokenFramework\Components\Form\Form;
use KaiokenFramework\Components\Form\Button;
use KaiokenFramework\Components\Base\Element;
use KaiokenFramework\Components\Base\Image;
use KaiokenFramework\Components\Form\Hidden;
use KaiokenFramework\Security\Token;
#endregion

/**
 * Decora formulários no formato Kaioken Framework
 * @author Willian Brito (h1s0k4)
*/
class KaiokenFormWrapper
{
    #region Propriedades da Classe

    private $decorated;
    #endregion
    
    #region Construtor

    /**
     * Constrói o decorator
    */
    public function __construct(Form $form)
    {
        $this->decorated = $form;
    }
    #endregion
    
    #region Metodos

    #region Magicos

    #region call

    /**
     * Redireciona chamadas para o objeto decorado (form)
    */
    public function __call($method, $parameters) // É ativado sempre que é chamado um método que não existe
    {
        return call_user_func_array(array($this->decorated, $method),$parameters);
    }
    #endregion

    #endregion

    #region Publico

    #region show
        
    /**
     * Exibe o formulário
    */
    public function show()
    {

        $element = new Element('form');
        $element->class = "form-horizontal";

        $divHidden = new Element('div');
        $divHidden->id = 'divHidden';
        $divHidden->style = "display: none;";
        
        $pageStatus = new Hidden('PageStatus');
        $csrfToken = new Hidden('Csrf_Token_Form');
        $csrfToken->id = 'TokenCsrf';
        
        Token::generateTokenCSRF();

        $divHidden->add($pageStatus);
        $divHidden->add($csrfToken);


        $element->add($divHidden);

        if(!$this->decorated->getUseJS()) 
        {
            $element->enctype = "multipart/form-data";
            $element->method  = 'post';    // método de transferência
        }

        $element->name  = $this->decorated->getName();
        $element->width = '100%';
        
        foreach ($this->decorated->getFields() as $field)
        {
            $group = new Element('div');
            $group->class = 'form-group';
            
            $label = new Element('label');
            $label->class= 'col-sm-2 control-label';
            $label->add($field->getLabel());
            
            $col = new Element('div');
            $col->class = 'col-sm-10';
            $col->add($field);
            $field->class = 'form-control';
            
            $group->add($label);
            $group->add($col);
            $element->add($group);
        }
        
        $group = new Element('div');
        
        $i = 0;
        foreach ($this->decorated->getActions() as $label => $action)
        {
            $name   = strtolower(str_replace(' ', '_', $label));
            $button = new Button($name);
            $button->setFormName($this->decorated->getName());
            
            $useJS = $this->decorated->getUseJS();
            $button->setUseJS($useJS);
            $button->setAction($action, $label);

            $button->class = 'btn ' . ( ($i==0) ? 'btn-success' : 'btn-default');

            if($name == "salvar")
                $button->id = "btnSalvar";
            
            $group->add($button);
            $i ++;
        }
        
        $panel = new Panel($this->decorated->getTitle());
        $panel->add($element);
        $panel->addFooter($group);
        $panel->show();
    }
    #endregion

    #endregion

    #endregion
}
