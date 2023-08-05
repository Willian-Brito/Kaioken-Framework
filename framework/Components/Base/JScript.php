<?php

namespace KaiokenFramework\Components\Base;

/**
 * Classe Base para Execução de JavaScript
 * @author Willian Brito (h1s0k4)
*/
class JScript
{
    #region Metodos

    #region run
    /**
     * Cria e executa código JavaScript
     * @param $code source code
     * @param $timeout Tempo de espera para execução
     */
    public static function run( $code, $timeout = null )
    {
        if ($timeout)
        {
            $code = "setTimeout( function() { $code }, $timeout )";
        }
        
        $script = new Element('script');
        $script->type = 'text/javascript';
        $script->add( str_replace( ["\n", "\r"], [' ', ' '], $code) );

        $script->show();
    }
    #endregion

    #region openFile
    /** 
    * Executa Abrir arquivo no navegador
    * @param $path caminho do arquivo
    * @param $timeout Tempo de espera para a execução
    */
    public static function openFile($path, $timeout = 0)
    {
        $script = "function openFile() { var win = window.open('$path', '_blank');  win.focus(); } openFile()";
        JScript::run($script, $timeout);
    }
    #endregion

    #region redirect
    /**
     * Redireciona para um link 
     * @param $link URL a ser aberta
     * @param $timeout Tempo de espera para execução
     */
    public static function redirect($link, $timeout = 0)
    {
        JScript::run("window.location = '$link';", $timeout);
    }
    #endregion

    #region onLoad
    /**
     * Executa script depois que carregar a página
     * @param $script Script a ser executado depois do carregamento da página
     */
    public static function onLoad($script)
    {
        $exec = "window.onload = function() { $script }";
        JScript::run($exec);
    }
    #endregion

    #region windowClose
    /**
     * Fechar Formulário
     */
    public static function windowClose()
    {
        JScript::run("window.close();", 100);
    }
    #endregion

    #region reload
    /**
     * Recarrega a pagina atual
     */
    public static function reload()
    {
        JScript::run("window.location.reload();", 100);
    }
    #endregion

    #region isMobile
    /**
     * Verifica se o navegador é um dispositivo móvel
    */
    public static function isMobile()
    {
        $isMobile = FALSE;
        
        if (PHP_SAPI == 'cli')
        {
            return FALSE;
        }
        
        if (isset($_SERVER['HTTP_X_WAP_PROFILE']) or isset($_SERVER['HTTP_PROFILE']))
        {
            $isMobile = TRUE;
        }
        
        $mobiBrowsers = array('android',   'audiovox', 'blackberry', 'epoc',
                              'ericsson', ' iemobile', 'ipaq',       'iphone', 'ipad', 
                              'ipod',      'j2me',     'midp',       'mmp',
                              'mobile',    'motorola', 'nitro',      'nokia',
                              'opera mini','palm',     'palmsource', 'panasonic',
                              'phone',     'pocketpc', 'samsung',    'sanyo',
                              'series60',  'sharp',    'siemens',    'smartphone',
                              'sony',      'symbian',  'toshiba',    'treo',
                              'up.browser','up.link',  'wap',        'wap',
                              'windows ce','htc');
                              
        foreach ($mobiBrowsers as $mb)
        {
            if (strpos(strtolower($_SERVER['HTTP_USER_AGENT']),$mb) !== FALSE)
            {
             	$isMobile = TRUE;
            }
        }
        
        return $isMobile;
    }
    #endregion

    #region importFromFile
    /**
     * Import script
     * @param $script nome do arquivo javascript
     */
    public static function importFromFile( $script, $timeout = null )
    {
        JScript::run('$.getScript("'.$script.'");', $timeout);
    }
    #endregion

    #region setDataForm
    public static function setDataForm($id, $value, $timeout = 100)
    {
        $script = "setDataForm('$id', '$value')";
        JScript::run($script, $timeout);
    }    
    #endregion

    #region downloadFile
    /**
     * Possibilita que o usuário faça download de algum arquivo
     * @param $file Caminho completo do arquivo para download
     * @param $basename diretorio do arquivo
     */
    public static function downloadFile($file, $basename = null)
    { 
        // # Modo de Uso
        // $arquivoDownload = PATH_FRAMEWORK_DOWNLOAD . basename($filename);
        // JScript::downloadFile($arquivoDownload, PATH_FRAMEWORK_DOWNLOAD);
        
        JScript::run("downloadFile('$file', '$basename');", 100) ;
    }
    #endregion

    #region jQuery

    #region text
    /**
     * Atribui um valor atraves do id
     * @param $id identificador do elemento
     * @param $value valor do elemento
     */
    public static function text($id, $value)
    {
        JScript::run("$('#$id').text('$value')", 100);
    }
    #endregion

    #region textName
    /**
     * Atribui um valor atraves do identificador Name
     * @param $name nome do atributo
     * @param $value valor do atributo
     */
    public static function textName($name, $value)
    {
        JScript::run("$('[name=\"$name\"]').text('$value')", 100);
    }
    #endregion

    #region hide
    /**
     * Esconde o elemento na pagina
     * @param $id ID do elemento
     */
    public static function hide($id)
    {
        JScript::run("$('#$id').hide('medium')", 100);
    }
    #endregion
    
    #region show
    /**
     * Mostrar o elemento na pagina
     * @param $id ID do elemento
     */
    public static function show($id)
    {
        JScript::run("$('#$id').show('medium')", 100);
    }
    #endregion

    #region checked
    /**
     * Marcar elemento checkbox
     * @param $id ID do elemento
     */
    public static function checked($id)
    {
        $script = "$('#$id').prop('checked', true).prop('value', '1')";
        JScript::run($script, 100);
    }
    #endregion

    #region unchecked
    /**
     * Desmarcar elemento checkbox
     * @param $id ID do elemento
     */
    public static function unchecked($id)
    {
        $script = "$('#$id').prop('checked', false).prop('value', '0')";
        JScript::run($script, 100);
    }
    #endregion

    #region addAttr
    /**
     * Adiciona atributo no elemento da página
     * @param $id ID do elemento
     * @param $attr Atributo que queira adicionar no elemento
     * @param $value Valor que queira adicionar no elemento
     */
    public static function addAttr($id, $attr, $value)
    {
        JScript::run("$('#$id').attr('$attr', $value)", 100);
    }
    #endregion

    #region removeAttr
    /**
     * Adiciona atributo no elemento da página
     * @param $id ID do elemento
     * @param $attr Atributo que queira remover no elemento
     */
    public static function removeAttr($id, $attr)
    {
        JScript::run("$('#$id').removeAttr('$attr')", 100);
    }
    #endregion
    
    #region html
    /**
     * Adiciona html no elemento da página
     * @param $id ID do elemento
     * @param $value que queira adicionar no elemento
     */
    public static function html($id, $value)
    {
        JScript::run("html('$id', '$value')", 100);
    }
    #endregion

    #region addClass
    /**
     * Adiciona classe CSS no elemento da página
     * @param $id ID do elemento
     * @param $classe que queira adicionar no elemento
     */
    public static function addClass($id, $class)
    {
        JScript::run("$('#$id').addClass('$class')", 100);
    }
    #endregion

    #region removeClass
    /**
     * Adiciona classe CSS no elemento da página
     * @param $id ID do elemento
     * @param $classe que queira remover no elemento
     */
    public static function removeClass($id, $class)
    {
        JScript::run("$('#$id').removeClass('$class')", 100);
    }
    #endregion

    #region clicked
    /**
     * Simula um click no elemento da página
     * @param $id ID do elemento
     * @param $timeout Tempo de espera para a execução
     */
    public static function clicked($id, $timeout = 0)
    {
        JScript::run("$('#$id').trigger('click')", $timeout);
    }
    #endregion

    #region enabled
    /**
     * Habilita edição no elemento da página
     * @param $id ID do elemento
     */
    public static function enabled($id)
    {
        JScript::addAttr($id, 'disabled', 'disabled');
    }
    #endregion

    #region disabled
    /**
     * Desabilita edição no elemento da página
     * @param $id ID do elemento
     */
    public static function disabled($id)
    { 
        JScript::removeAttr($id, 'disabled');
    }
    #endregion

    #endregion

    #endregion
}