<?php

namespace KaiokenFramework\Log;

use KaiokenFramework\Session\Session;

/**
 * Implementa o algoritmo de LOG em HTML
 * @author Willian Brito (h1s0k4)
 */
class LoggerHTML extends Logger
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
        date_default_timezone_set('America/Sao_Paulo');
        $usuario = Session::getValue("UsuarioLogado");
        $time = date("Y-m-d H:i:s");
        
        // monta a string
        $text = "\r\n";
        $text .= "<p>\n";
        $text .= "   <b>$usuario</b> : \n";
        $text .= "   <b>$time</b> : \n";
        $text .= "   <i>$evento</i> <br>\n";
        $text .= "</p>\n";
        
        // adiciona ao final do arquivo
        $arquivo = fopen($this->filePath, "a+");
        fwrite($arquivo, $text);
        fclose($arquivo);
    }
    #endregion

    #endregion
}
