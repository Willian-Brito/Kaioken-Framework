<?php

use KaiokenFramework\Database\Criteria;
use KaiokenFramework\Database\Record;
use KaiokenFramework\Database\Repository;

class Cidade extends Record
{
    #region Propriedades da Classe
    const TABLENAME = 'Cidade';
    private $estado;
    #endregion
    
    #region Metodos

    #region get_estado 
    public function get_estado()
    {
        if (empty($this->estado))
        {
            $this->estado = new Estado($this->IdEstado);
        }
        
        return $this->estado;
    }
    #endregion

    #region get_nome_estado
    
    public function get_nome_estado()
    {
        if (empty($this->estado))
        {
            $this->estado = new Estado($this->IdEstado);
        }
        
        return $this->estado->Descricao;
    }
    #endregion

    #region getCidadeByCodigoIBGE
    public static function getCidadeByCodigoIBGE($codigoIBGE)
    {
        $criteria = new Criteria;
        $criteria->add('CodigoIBGE', '=', $codigoIBGE);

        $repo = new Repository('Cidade');
        $cidade = $repo->load($criteria);

        if(count($cidade) > 0)
            return $cidade[0]->IdCidade;
    }
    #endregion

    #endregion
}
