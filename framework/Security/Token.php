<?php

#region Imports
namespace KaiokenFramework\Security;

use KaiokenFramework\Database\Criteria;
use KaiokenFramework\Database\Repository;
use KaiokenFramework\Router\Router;
use KaiokenFramework\Session\Session;
#endregion

/**
 * Classe responsavel por gerar Tokens de Sessão
 * @author Willian Brito (h1s0k4)
*/
class Token
{
    #region Metodos

    #region CSRF (Cross Site Request Forgery)

    #region generateTokenCsrf
    /**
     * Gera Token contra ataques CSRF (Cross Site Request Forgery - Falsificação de solicitação entre sites).
    */
    public static function generateTokenCsrf()
    {
        if(self::ehFormulario())
        {
            if(isset($_SESSION['Csrf_Token_Session']))
                unset($_SESSION['Csrf_Token_Session']);        
    
            Session::setValue('Csrf_Token_Session', md5(uniqid()));
            $token = Session::getValue("Csrf_Token_Session");
    
            echo "<script> window.onload = function() { generateTokenCSRF('$token'); } </script>";
        }
    }
    #endregion

    #region validateTokenCSRF
    /**
     * Valida se o formulario contem token do formulario é igual ao token de sessão.
    */
    public static function validateTokenCSRF()
    {
        if(!isset($_SESSION['Csrf_Token_Session']))
        {
            $class = $_GET['class'];
            Router::redirect("index.php?class=$class", 0);
            die();
        }

        if(!self::isValidToken())
        {
            $class = $_GET['class'];
            Router::redirect("index.php?class=$class", 0);
            die();
        }
        
        unset($_SESSION['Csrf_Token_Session']);
        self::generateTokenCsrf();

        return true;
    }
    #endregion

    #region isValidToken
    /**
     * Token de Sessão: É gerado cada vez que o usuário entra na pagina (unico para cada usuário).
     * Token do Formulario: Garantir que a origem do formulario é a mesma da aplicação.
     * 
     * Explicação: Em caso de um ataque essa validação não deixará que o ataque seja bem sucedido, pois 
     * mesmo que o atacante tenha acesso ao token do formulario a validação é comparada pelo token de sessão
     * individual de cada conexão com o computador do cliente.
     * 
     * Exemplo: Duas conexão com o mesmo usuario logado pelo mesmo computador com 2 abas abertas uma normal e 
     * outra como privado, o token de sessão para cada conexão é diferente, portanto a comparação entre o token 
     * do form e sessão é individual para cada conexão com o servidor.
     */
    private static function isValidToken()
    {
        $tokenForm = $_POST['Csrf_Token_Form'];
        $tokenSession = Session::getValue('Csrf_Token_Session');

        return $tokenSession == $tokenForm;
    }
    #endregion

    #region ehLista
    private static function ehFormulario()
    {
        $class = $_GET['class'];

        $criteria = new Criteria;
        $criteria->add('ArquivoFormulario', '=', $class);

        $repo = new Repository('Formulario');
        $formulario = $repo->load($criteria);

        if(count($formulario))
            return true;
        
        return false;
    }
    #endregion

    #endregion

    #endregion
}