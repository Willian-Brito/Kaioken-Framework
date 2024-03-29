<?php

namespace KaiokenFramework\Database;

/**
 * Permite definição de critérios
 * @author Willian Brito (h1s0k4)
*/
class Criteria
{
    #region Propriedades da Classe

    private $filters;        // Armazena a lista de filtros
    private $properties;
    private static $paramCounter;
    #endregion
    
    #region Constantes

    const CAMPO = 0;         // Posição do CAMPO no vetor de filtros
    const OP_COMPARACAO = 1; // Posição do OPERADOR DE COMPARAÇÂO no vetor de filtros
    const VALOR = 2;         // Posição do VALOR no vetor de filtros
    const OP_LOGICO = 3;     // Posição do OPERADOR LÓGICO no vetor de filtros
    const PAR_RAND = 4;      // Posição de PARAMETROS RANDÔMICOS no vetor de filtros
    #endregion

    #region Construtor

    /**
     * Método Construtor
    */
    function __construct()
    {
        $this->filters = array();
    }
    #endregion
   
    #region Metodos

    #region [+] Public

     #region add

    /**
     * Adiciona uma expressão ao critério
     * @param $variable           Variável/campo
     * @param $compare_operator   Operador de comparação
     * @param $value              Valor a ser comparado
     * @param $logic_operator     Operador lógico
    */
    public function add($variable, $compare_operator, $value, $logic_operator = 'and')
    {
        // na primeira vez, não precisamos concatenar
        if (empty($this->filters))
            $logic_operator = NULL;        
        
        // $this->filters[] = [$variable, $compare_operator, $this->transform($value), $logic_operator];
        $paramRandom = $this->getPreparedVars($variable);
        $this->filters[] = [$variable, $compare_operator, $value, $logic_operator, $paramRandom];
    }
    #endregion

    #region dump

    /**
     * Retorna a expressão final
    */
    public function dump()
    {
    
        $ForUmArray = is_array($this->filters);
        $TemElementos = count($this->filters) > 0;        
        
        if ($ForUmArray and $TemElementos)
        {
            $result = '';
            $result = trim($this->prepareFilters());

            return "({$result})";
        }
    }
    #endregion

    #region prepareBinds
    public function prepareBinds($cmd)
    {
        $command = $cmd;

        foreach($this->filters as $filter)
        {
            // $column = $filter[self::CAMPO];
            $column = $filter[self::PAR_RAND];
            $value = $filter[self::VALOR];

            $command->bindValue($column, $value);
        }

        return $command;
    }
    #endregion

    #region getCommand
    public function getCommand($query)
    {

        $result = $query;

        foreach ($this->filters as $filter)
        {
            // $column = $filter[self::CAMPO];
            $column = $filter[self::PAR_RAND];
            $value = $filter[self::VALOR];

            // $result = str_replace(":{$column}","{$value}", $result);
            $result = str_replace($column,"{$value}", $result);
        }

        return $result;
    }
    #endregion

    #region setProperty

    /**
     * Define o valor de uma propriedade
     * @param $property = propriedade
     * @param $value    = valor
    */
    public function setProperty($property, $value)
    {
        if (isset($value))
        {
            $this->properties[$property] = $value;
        }
        else
        {
            $this->properties[$property] = NULL;
        }
    }
    #endregion

    #region getProperty
    
    /**
     * Retorna o valor de uma propriedade
     * @param $property = propriedade
    */
    public function getProperty($property)
    {
        if (isset($this->properties[$property]))
        {
            return $this->properties[$property];
        }
    }
    #endregion

    #endregion

    #region [-] Private

    #region prepareFilters
    private function prepareFilters()
    {
        $query = "";

        foreach ($this->filters as $filter)
        {
            $query .= $filter[self::OP_LOGICO] . ' ' . 
                        $filter[self::CAMPO] . ' ' . 
                        $filter[self::OP_COMPARACAO] . ' '. 
                    //   ' :' . $filter[self::CAMPO] . ' ';
                        $filter[self::PAR_RAND] . ' ';
        }

        return $query;
    }
    #endregion

    #region getPreparedVars
    /**
     * Return the prepared vars
     */
    private function getPreparedVars($variable)
    {
        $paramRandom = ' :par_'.$this->getRandomParameter() . '_' . $variable . ' ';
        return trim($paramRandom);
    }
    #endregion

    #region getRandomParameter
    /**
     * Retorna parametro randômico
     */
    private function getRandomParameter()
    {
        self::$paramCounter ++;
        return self::$paramCounter;
    }
    #endregion

    #region transform (Não Utilizar)
    
    /**
     * Recebe um valor e faz as modificações necessárias
     * para ele ser interpretado pelo banco de dados
     * @param $value = valor a ser transformado
    */
    private function transform($value)
    {
        // caso seja um array
        if (is_array($value))
        {
            // percorre os valores
            foreach ($value as $element)
            {
                // se for um inteiro
                if (is_integer($element))
                {
                    $newArray[]= $element;
                }
                else if (is_string($element))
                {
                    // se for string, adiciona aspas
                    $newArray[]= "'$element'";
                }
            }
            // converte o array em string separada por ","
            $result = '(' . implode(',', $newArray) . ')';
        }
        // caso seja uma string
        else if (is_string($value))
        {
            // adiciona aspas
            $result = "'$value'";
        }
        // caso seja valor nullo
        else if (is_null($value))
        {
            // armazena NULL
            $result = 'NULL';
        }        
        // caso seja booleano
        else if (is_bool($value))
        {
            // armazena TRUE ou FALSE
            $result = $value ? 'TRUE' : 'FALSE';
        }
        else
        {
            $result = $value;
        }

        return $result;
    }
    #endregion

    #endregion

    #endregion
}
