<?php

// # Habilitar Biblioteca
// sudo pacman -S php sudo pacman -S php-sodium  
// ;extension=sodium

require_once 'Cryptography.php';
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
        // lê o INI e retorna um array
        $fileConfig = readFileConfig();
    }
    else
    {
        echo "Arquivo '". $ARQUIVO_CONFIG ."' não encontrado";
        echo PHP_EOL;
        exit();
    }

    encrypt($fileConfig);
}
#endregion

#region readFileConfig
function readFileConfig()
{
    global $ARQUIVO_CONFIG;
    
    return parse_ini_file($ARQUIVO_CONFIG);
}
#endregion

#region encrypt
function encrypt($fileConfig)
{
    global $ARQUIVO_CONFIG;

    #region lê as informações contidas no arquivo
    $user = $fileConfig['user'];
    $pass = $fileConfig['pass'];
    $name = $fileConfig['name'];
    $host = $fileConfig['host'];
    $port = $fileConfig['port'];
    $type = $fileConfig['type'];
    #endregion

    #region Criptografando dados
    $chave = Cryptography::createKey();
    $iv = Cryptography::createIV();

    $fileConfig['user'] = Cryptography::encrypt($user, $iv, $chave);
    $fileConfig['pass'] = Cryptography::encrypt($pass, $iv, $chave);
    $fileConfig['name'] = Cryptography::encrypt($name, $iv, $chave);
    $fileConfig['host'] = Cryptography::encrypt($host, $iv, $chave);
    $fileConfig['port'] = Cryptography::encrypt($port, $iv, $chave);
    $fileConfig['type'] = Cryptography::encrypt($type, $iv, $chave);
    #endregion

    #region Convertendo array para string e escrevendo no arquivo

    $arrayParaString = implode(", ", $fileConfig);
    file_put_contents($ARQUIVO_CONFIG, $arrayParaString);
    #endregion
}
#endregion

#endregion