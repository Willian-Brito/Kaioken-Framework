<?php

namespace KaiokenFramework\Log;

use KaiokenFramework\Session\Session;
use Usuario;

/**
 * Implementa o algoritmo de LOG em TXT
 * @author Willian Brito (h1s0k4)
*/
class LoggerTXT extends Logger
{
    #region Metodos

    #region write

    /**
     * método write()
     * Escreve uma mensagem no arquivo de LOG
     * @param $evento = evento que será registrado.
    */
    public function write($evento)
    {
        $usuario = Session::getValue("UsuarioLogado");
        $text = 'Usuario:' . $usuario . ' | Data: ' . date('Y-m-d H:i:s') . ' | Evento: ' . $evento;
        $file = fopen($this->filePath, 'a+');

        fwrite($file, $text . "\n");
        fclose($file);
    }
    #endregion

    #endregion
}
