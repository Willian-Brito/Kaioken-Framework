<?php


class Login 
{
    #region Propriedades da Classe

    public $QuantidadeTentativa;
    public $DataProximaTentativa;
    #endregion

    #region Metodos
    
    #region Getters and Setters
    
    #region QuantidadeTentativa

    #region getQuantidadeTentativa

    public function getQuantidadeTentativa()
    {
        return $this->QuantidadeTentativa;
    }
    #endregion

    #region setQuantidadeTentativa
    public function setQuantidadeTentativa($value)
    {
        $this->QuantidadeTentativa = $value;
    }
    #endregion

    #endregion
    
    #region DataProximaTentativa

    #region getQuantidadeTentativa

    public function getDataProximaTentativa()
    {
        return $this->DataProximaTentativa;
    }
    #endregion

    #region setDataProximaTentativa
    public function setDataProximaTentativa($value)
    {
        $this->DataProximaTentativa = $value;
    }
    #endregion    

    #endregion

    #endregion
    
    #endregion
}