<?php

namespace KaiokenFramework\Log;

use KaiokenFramework\Session\Session;

/**
 * Implementa o algoritmo de LOG em XML
 * @author Willian Brito (h1s0k4)
*/
class LoggerXML extends Logger
{
    #region Metodos

    #region write

    /*
     * método write()
     * escreve uma mensagem no arquivo de LOG
     * @param $evento = evento que será registrado.
    */
    public function write($evento)
    {
        date_default_timezone_set('America/Sao_Paulo');
        $usuario = Session::getValue("UsuarioLogado");
        $time = date("Y-m-d H:i:s");
        
        // monta a string
        $text = "<log>\n";
        $text.= "   <usuario>$usuario</usuario>\n";
        $text.= "   <time>$time</time>\n";
        $text.= "   <evento>$evento</evento>\n";
        $text.= "</log>\n";
        
        // adiciona ao final do arquivo
        $handler = fopen($this->filePath, 'a+');
        fwrite($handler, $text);
        fclose($handler);
    }
    #endregion

    #endregion
}
