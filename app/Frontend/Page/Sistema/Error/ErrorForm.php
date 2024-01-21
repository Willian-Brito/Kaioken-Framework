<?php

#region Imports

use KaiokenFramework\Page\Page;
#endregion

/**
 * Tela de Erro do Sistema
 */
class ErrorForm extends Page
{
    #region Objetos

    private $html;
    #endregion 

    #region Construtor

    /**
     * Construtor da página
     */
    public function __construct()
    {
        parent::__construct();
        $this->createPage();
    }
    #endregion

    #region Metodos

    #region createPage
    private function createPage()
    {
        #region HTML

        $template = __DIR__ . DIRECTORY_SEPARATOR . "ErrorForm.html";
        $this->html = file_get_contents($template);


        parent::add($this->html);
        #endregion
    }
    #endregion

    #region show

    /**
     * exibe a página
     */
    public function show()
    {
        parent::show();
    }
    #endregion

    #endregion
}
