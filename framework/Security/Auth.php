<?php

#region Imports

namespace KaiokenFramework\Security;

use Exception;
use KaiokenFramework\Database\Criteria;
use KaiokenFramework\Database\Repository;
use KaiokenFramework\Session\Session;
use Usuario;
#endregion

/**
 * Classe responsável pela validação de permissão dos usuários
 * @author Willian Brito (h1s0k4)
*/
class Auth
{
    #region Construtor

    /**
     * Private para impedir que se crie instâncias de Cryptography
    */
    private function __construct() {}
    #endregion

    #region Metodos

    #region Authorization
    public static function Authorization($object, $idRegistro)
    {
        $IdTabela = 'Id' . get_class($object);
        $IdUsuario = isset($object->IdUsuario) ? $object->IdUsuario : 0;
        
        if($IdUsuario != 0)
        {
            $IdUsuarioLogado = Session::getValue("IdUsuarioLogado");

            if($IdUsuarioLogado != Usuario::getIdUsuarioSuporte())
            {    
                $criteria = new Criteria();
                $criteria->add($IdTabela, '=', $idRegistro, 'AND');
                $criteria->add('IdUsuario', '=', $IdUsuarioLogado);
        
                $repo = new Repository( get_class($object) );
                $temPermissao = count($repo->load($criteria)) > 0;
    
                if(!$temPermissao)
                    throw new Exception("Usuário não tem permissão para acessar este recurso!");            
            }
        }
    }
    #endregion

    #endregion
}