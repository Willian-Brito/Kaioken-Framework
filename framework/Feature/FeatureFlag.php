<?php

#region Imports

namespace KaiokenFramework\Feature;

use KaiokenFramework\Database\Criteria;
use KaiokenFramework\Database\Record;
use KaiokenFramework\Database\Repository;
use KaiokenFramework\Util\Util;
use Parametro;
#endregion

/**
 * Controle de Feature Flags para os usuários
 * @author Willian Brito (h1s0k4)
*/
class FeatureFlag
{
    #region Propriedades da Classe
    private static $features;
    #endregion

    #region Features

    /**
     * Observação: Se sim, será obrigatório informar o email no cadastro de Usuários.
     * Retorno: Texto
    */
    const Utiliza_Email_Obrigatorio_No_Cadastro_De_Usuarios = 'Utiliza email obrigatorio no cadastro de usuarios';

    #endregion

    #region Metodos

    #region para
    public static function para($IdUsuario)
    {
        $criteria = new Criteria;
        $criteria->add('IdUsuario', '=', $IdUsuario);

        $repo = new Repository('Parametro');
        self::$features = $repo->load($criteria);

        return new static;
    }
    #endregion

    #region estaAtivo
    public function estaAtivo($feature)
    {
        if(self::$features[0])
        {
            $criteria = new Criteria;
            $criteria->setProperty('LIMIT', 1);

            $criteria->add('Descricao', '=', $feature, 'AND');
            $criteria->add('IdUsuario', '=', self::$features[0]->IdUsuario);
            
            $repository = new Repository('Parametro');
            self::$features = $repository->load($criteria);

            if (count(self::$features) > 0)
                return Util::getBoolean(self::$features[0]->Valor);
        }
    }
    #endregion

    #region runAllFeatures
    public static function runAllFeatures()
    {
        Record::executeProcedure("stp_InserirParametros", "");
    }
    #endregion

    #region removeFeatures
    public function removeFeatures($feature = "")
    {
        $criteria = new Criteria;
        $IdUsuario = self::$features[0]->IdUsuario;

        if(!empty($feature))
            $criteria->add('Descricao', '=', $feature, 'AND');

        $criteria->add('IdUsuario', '=', $IdUsuario);

        $repository = new Repository('Parametro');
        self::$features = $repository->load($criteria);

        if (count(self::$features) > 0)
        {
            foreach(self::$features as $feature)
            {
	            $feature->delete();
            }
        }
    }
    #endregion

    #endregion
}