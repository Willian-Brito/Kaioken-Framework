<?php

use KaiokenFramework\Page\Page;
use KaiokenFramework\Session\Session;

class Template extends Page
{
    #region Propriedades da Classe
    private static $html;
    #endregion

    #region Construtor
    public function __construct()
    {
        $this->create();
    }
    #endregion

    #region Metodos

    #region create
    public static function create()
    {
        #region Html

        $template = "Frontend/Template/Template.html";
        self::$html = file_get_contents($template);

        $IdUsuarioLogado = Session::getValue("IdUsuarioLogado");
        $usuario = new Usuario($IdUsuarioLogado);

        $header = Header::loadHeader($usuario);
        $menu   = Sidebar::loadMenu();
        $footer = Footer::loadFooter();

        self::$html = str_replace('{Header}', "$header", self::$html);
        self::$html = str_replace('{Sidebar}', "$menu", self::$html);
        self::$html = str_replace('{Footer}', "$footer", self::$html);

        return self::$html;
        #endregion
    }
    #endregion

    #endregion
}