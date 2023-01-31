<?php
namespace KaiokenFramework\Components\Form;

use KaiokenFramework\Page\Action;
use KaiokenFramework\Page\IAction;
use KaiokenFramework\Components\Base\Element;

/**
 * Representa um botão
 * @author Willian Brito (h1s0k4)
 */
class Button extends Field implements IFormElement
{
    #region Propriedades da Classe

    private $action;
    private $label;
    private $formName;
    #endregion
    
    #region Metodos

    #region setAction

    /**
     * Define a ação do botão (função a ser executada)
     * @param $action = ação do botão
     * @param $label    = rótulo do botão
    */
    public function setAction(IAction $action, $label)
    {
        if(!$this->getUseJS())
            $this->action = $action;
            
        $this->label = $label;
    }
    #endregion
    
    #region setFormName

    /**
     * Define o nome do formulário para a ação botão
     * @param $name = nome do formulário
    */
    public function setFormName($name)
    {
        $this->formName = $name;
    }
    #endregion
    
    #region show

    /**
     * exibe o botão
    */
    public function show()
    {
        // define as propriedades do botão
        $tag = new Element('button');
        $tag->name    = $this->name;    // nome da TAG
        $tag->type    = 'button';       // tipo de input
        $tag->add($this->label);
        
        if(!$this->getUseJS())
        {
            $url = $this->action->serialize();

            // define a ação do botão
            $tag->onclick =	"document.{$this->formName}.action='{$url}'; ".
                                    "document.{$this->formName}.submit()";
        }
                                
        if ($this->properties)
        {
            foreach ($this->properties as $property => $value)
            {
                $tag->$property = $value;
            }
        }
        
        // exibe o botão
        $tag->show();
    }
    #endregion

    #endregion
}