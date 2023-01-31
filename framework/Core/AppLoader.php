<?php

namespace KaiokenFramework\Core;

use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;
use Exception;

/**
 * Carrega a classe da aplicação
 * @author Willian Brito (h1s0k4)
 */
class AppLoader
{
    #region Propriedades da Classe

    protected $directories;
    #endregion

    #region Metodos

    #region addDirectory

    /**
     * Adiciona um diretório a ser vasculhado
    */
    public function addDirectory($directory)
    {
        $this->directories[] = $directory;
    }
    #endregion
    
    #region register

    /**
     * Registra o AppLoader
    */
    public function register()
    {
        spl_autoload_register(array($this, 'loadClass'));
    }
    #endregion
    
    #region loadClass

    /**
     * Carrega uma classe
    */
    public function loadClass($class)
    {
        $folders = $this->directories;

        foreach ($folders as $folder)
        {
            
            if (file_exists("{$folder}/{$class}.php"))
            {
                require_once "{$folder}/{$class}.php";
                return TRUE;
            }
            else
            {
                if (file_exists($folder))
                {
                    foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($folder),
                                                           RecursiveIteratorIterator::SELF_FIRST) as $entry)
                    {
                        if (is_dir($entry))
                        {
                            if (file_exists("{$entry}/{$class}.php"))
                            {
                                require_once "{$entry}/{$class}.php";
                                return TRUE;
                            }
                        }
                    }
                }
            }
        }
    }
    #endregion

    #endregion
}
