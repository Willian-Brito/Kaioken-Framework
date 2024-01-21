<?php

$pasta = __DIR__;
$conteudoFinal = '';
$ScriptAtualizacao = $pasta . '/ScriptAtualizacao.sql';
$ScriptFinal = new SplFileObject($ScriptAtualizacao, 'w');

GerarScriptAtualizacao();

#region GerarScriptAtualizacao

function GerarScriptAtualizacao()
{
    global $pasta;
    
    #region Table
    
    $pastaTable = $pasta . '/Scripts/Table';
    $arrayArquivos = getArquivos($pastaTable);

    foreach ($arrayArquivos as $arquivo)
    {     
        escreverNoArquivo($arquivo);        
    }
    #endregion

    #region Change
    $pastaChange = $pasta . '/Scripts/Change';
    $arrayArquivos = getArquivos($pastaChange);
   
    foreach ($arrayArquivos as $arquivo)
    {     
        escreverNoArquivo($arquivo);        
    }
    #endregion

    #region Function
    $pastaFunction = $pasta . '/Scripts/Function';
    $arrayArquivos = getArquivos($pastaFunction);

    foreach ($arrayArquivos as $arquivo)
    {     
        escreverNoArquivo($arquivo);        
    }
    #endregion

    #region Procedure
    $pastaProcedure = $pasta . '/Scripts/Procedure';
    $arrayArquivos = getArquivos($pastaProcedure);

    foreach ($arrayArquivos as $arquivo)
    {     
        escreverNoArquivo($arquivo);        
    }
    #endregion

    #region View
    $pastaView = $pasta . '/Scripts/View';
    $arrayArquivos = getArquivos($pastaView);

    foreach ($arrayArquivos as $arquivo)
    {     
        escreverNoArquivo($arquivo);        
    }
    #endregion

    #region Trigger
    // $pastaTrigger = $pasta . '/Scripts/Trigger';
    // $arrayArquivos = getArquivos($pastaTrigger);

    // foreach ($arrayArquivos as $arquivo)
    // {     
    //     escreverNoArquivo($arquivo);        
    // }
    #endregion

    #region Begin
    $pastaBegin = $pasta . '/Scripts/Begin';
    $arrayArquivos = getArquivos($pastaBegin);

    foreach ($arrayArquivos as $arquivo)
    {     
        escreverNoArquivo($arquivo);        
    }
    #endregion

    #region Parametro
    $pastaParametro = $pasta . '/Scripts/Parametro';
    $arrayArquivos = getArquivos($pastaParametro);

    foreach ($arrayArquivos as $arquivo)
    {     
        escreverNoArquivo($arquivo);        
    }
    #endregion
}
#endregion

#region getArquivos
function getArquivos($pasta)
{
    if(file_exists($pasta))
    {
        $lstArquivos = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($pasta, RecursiveDirectoryIterator::SKIP_DOTS));
        
        $arrayArquivos = converterParaArray($lstArquivos);
        $arrayArquivos = ordenar($arrayArquivos);
    
        return $arrayArquivos;
    }
}
#endregion

#region converterParaArray
function converterParaArray($lstArquivos)
{
    $arrayArquivos = [];

    foreach ($lstArquivos as $arquivo)
    {
        array_push($arrayArquivos, $arquivo);
    }

    return $arrayArquivos;
}
#endregion

#region escreverNoArquivo
function escreverNoArquivo(SplFileInfo $arquivo)
{
    global $ScriptFinal;
    $conteudoFinal = '';

    if($arquivo->getExtension() == 'sql')
    {  
        
        $conteudoArquivo = fopen($arquivo, 'r');
        $pastaAtual = basename($arquivo->getPath());
        
        $conteudoFinal .= '-- Script of ' . $pastaAtual . ' = ' . $arquivo->getFileName() . pularLinha(1);

        while ( !feof($conteudoArquivo) )
        {
            $linha = fgets($conteudoArquivo, 4096);
            $conteudoFinal .= $linha;                 
        }
        
        $conteudoFinal .= pularLinha(3);
        fclose($conteudoArquivo);

        $ScriptFinal->fwrite($conteudoFinal);
    }
}
#endregion

#region pularLinha

function pularLinha($quantidade)
{
    $pularLinha = '';

    for ($i = 1; $i <= $quantidade; $i++)
    {
        $pularLinha .= PHP_EOL;
    }

    return $pularLinha;
}
#endregion

#region ordenar
function ordenar($array)
{
    
    array_multisort(array_map(function ($element){
        return $element->getFileName();
    }, $array), SORT_ASC, $array);

    return $array;
}
#endregion


// drop table TipoPessoa;
// drop table Pessoa;
// drop table TipoIcms;
// drop table RegimeTributario;
// drop table Empresa;
// drop table Estado;
// drop table Cidade;
// drop table Menu;
// drop table Formulario;
// drop table Perfil;
// drop table Usuario;
?>