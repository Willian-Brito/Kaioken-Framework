<?php

// # Habilitar Biblioteca
// sudo pacman -S php sudo pacman -S php-sodium  
// ;extension=sodium

require_once 'Cryptography.php';
require_once '../Database/Configuration.php';

use KaiokenFramework\Database\Configuration;
use KaiokenFramework\Security\Cryptography;

$ARQUIVO_CONFIG = "../../app/Backend/Config/kaioken.ini";

openFile();

#region Metodos

#region openFile
function openFile()
{
    global $ARQUIVO_CONFIG;

    $ExisteArquivoConfiguracao = file_exists($ARQUIVO_CONFIG);

    if ($ExisteArquivoConfiguracao)
    {
        $fileConfig = file_get_contents($ARQUIVO_CONFIG);
        $stringParaArray = explode(', ', $fileConfig);
    }
    else
    {
        echo "Arquivo '". $ARQUIVO_CONFIG ."' não encontrado";
        echo PHP_EOL;
        exit();
    }

    decrypt($stringParaArray);
}
#endregion

#region decrypt

function decrypt($fileConfig)
{
    global $ARQUIVO_CONFIG;

    $chave = Cryptography::createKey();
    $amostraDadosCriptografado = Cryptography::hexToBin($fileConfig[0]);
    $iv =  Cryptography::getIV($amostraDadosCriptografado);

    for($i = 0; $i < count($fileConfig); $i++)
    {
        $dados = Cryptography::hexToBin($fileConfig[$i]);
        $cifra = Cryptography::getCipher($dados);

        $index = Configuration::getIndex($i);
        $fileConfigNew[$index] = Cryptography::decrypt($cifra, $iv, $chave);
    }

    #region Convertendo array para string
    $arrayParaString = "";

    foreach($fileConfigNew as $key => $value)
    {
        $arrayParaString .= "$key = '$value'" . PHP_EOL;
    }
    #endregion

    // Escrevendo no arquivo dados descriptografados
    file_put_contents($ARQUIVO_CONFIG, $arrayParaString);
}
#endregion

#endregion