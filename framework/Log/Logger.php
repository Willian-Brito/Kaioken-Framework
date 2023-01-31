<?php

namespace KaiokenFramework\Log;

/**
 * Fornece uma interface abstrata para definição de algoritmos de LOG
 * @author Willian Brito (h1s0k4)
 */

abstract class Logger
{
    #region Propriedades da Classe

    protected $filePath;
    #endregion

    #region Construtor

    /**
     * Instancia um logger
     * @param $filePath = local do arquivo de LOG
    */
    public function __construct($filePath)
    {
        $this->filePath = $filePath;

        // reseta o conteúdo do arquivo
        // file_put_contents($filePath, '');
    }
    #endregion

    // define o método write como obrigatório
    abstract function write($evento);
}
