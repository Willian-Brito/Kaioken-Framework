<?php

namespace KaiokenFramework\Backup;

use Exception;
use KaiokenFramework\Database\Configuration;
use ZipArchive;

/**
 * Classe de criação de backups do banco de dados (MySQL) do sistema
 * @author Willian Brito (h1s0k4)
*/
class MySqlBackup implements IBackup
{
    #region Propriedades da Classe
    private $result;
    #endregion

    #region Metodos

    #region [+] Publicos

    #region export
    public function export($path)
    {
        $config = Configuration::getInstance();
        $diretorio = $this->criarDiretorio($path);
        $nomeArquivo = $this->getNomeArquivo($diretorio);        
        
        system("mysqldump -u {$config['user']} -p{$config['pass']} {$config['name']} > {$nomeArquivo}");
        
        $arquivoZip = $this->compactar($nomeArquivo);
        $this->gerarDownload($arquivoZip);
        $this->deletarArquivos($nomeArquivo, $arquivoZip);
    }
    #endregion

    #endregion

    #region [-] Privados

    #region getNomeArquivo
    private function getNomeArquivo($diretorio)
    {
        $now = date('Y-m-d-h-i-s');
        $nomeArquivo = $diretorio . "db_backup_" . $now . '.sql';

        return $nomeArquivo;
    }
    #endregion

    #region criarDiretorio
    private function criarDiretorio($path)
    {
        $diretorio = empty($path) ? 'Backend/Tmp/backup/' : $path;

        if(!is_dir($diretorio))
        {
            mkdir($diretorio, 0777, true);
            chmod($diretorio, 0777);
        }

        return $diretorio;
    }
    #endregion

    #region compactar
    private function compactar($fullPath)
    {
        /*
            # php.ini

            * Upload_max_filesize - 1500M
            * Max_input_time - 1000
            * Memory_limit - 640M
            * Max_execution_time - 1800
            * Post_max_size - 2000M
        */

        $zip = new ZipArchive();
        $fullPath = str_replace(".sql","", $fullPath);

        try
        {
            if($zip->open($fullPath . '.zip', ZipArchive::CREATE))
            {
                $zip->addFile($fullPath . '.sql');
                $zip->close();
            }
            else
            {
                throw new Exception("Diretório inexistente!");
            }
        }
        catch (Exception $ex)
        {
            throw new Exception("Erro ao compactar arquivo: " . $ex->getMessage());
        }

        return $fullPath . '.zip';
    }
    #endregion

    #region gerarDownload
    private function gerarDownload($arquivoZip)
    {
        if(file_exists($arquivoZip))
        {
            ob_end_clean();

            // $this->criarHeadersDownload($arquivoZip);
            $this->criarHeadersZip($arquivoZip);

            readfile($arquivoZip);
        }
        else
        {
            throw new Exception("Erro ao exportar arquivo!");
        }
    }
    #endregion

    #region criarHeadersZip
    private function criarHeadersZip($arquivoZip)
    {
        header($_SERVER['SERVER_PROTOCOL'].' 200 OK');
        header("Content-Type: application/zip");
        header("Content-Transfer-Encoding: Binary");
        header("Content-Length: ".filesize($arquivoZip));
        header("Content-Disposition: attachment; filename=\"" . basename($arquivoZip) . "\";");
    }
    #endregion

    #region deletarArquivos
    private function deletarArquivos($nomeArquivo, $arquivoZip)
    {
        unlink($arquivoZip);
        unlink($nomeArquivo);

        exit;
        ob_start();    
    }
    #endregion

    #endregion

    #region Export Manual

    #region exportManual
    public function exportManual($path)
    {
        $arquivoConfiguracao = Configuration::getInstance();
        $conn = $this->getConnection($arquivoConfiguracao);
        $tabelas = $this->getTodasTabelas($conn);

        foreach($tabelas as $tabela)
        {
            $colunas = $this->getTodasColunas($conn, $tabela);
            $numeroColunas = mysqli_num_fields($colunas);

            $this->result .= $this->criarTabelas($conn, $tabela);
            $this->result .= $this->popularTabelas($tabela, $colunas, $numeroColunas);
        }

        $diretorio = $this->criarDiretorio($path);
        $download = $this->criarArquivo($diretorio);
        $arquivoZip = $this->compactar($download);

        if(file_exists($arquivoZip))
        {
            ob_end_clean();

            // $this->criarHeadersDownload($arquivoZip);
            $this->criarHeadersZip($arquivoZip);    

            readfile($arquivoZip);
            exit;
        }
        else
        {
            throw new Exception("Erro ao exportar arquivo!");
        }

        ob_start();
    }
    #endregion

    #region getConnection
    private function getConnection($fileConfig)
    {
        // lê as informações contidas no arquivo
        $user = isset($fileConfig['user']) ? $fileConfig['user'] : NULL;
        $pass = isset($fileConfig['pass']) ? $fileConfig['pass'] : NULL;
        $dbname = isset($fileConfig['name']) ? $fileConfig['name'] : NULL;
        $host = isset($fileConfig['host']) ? $fileConfig['host'] : NULL;
        $port = isset($fileConfig['port']) ? $fileConfig['port'] : NULL;

        $conn = mysqli_connect($host, $user, $pass, $dbname, $port);

        return $conn;
    }
    #endregion

    #region getTodasTabelas

    private function getTodasTabelas($conn)
    {
        $result_tabela = "SHOW TABLES";
        $resultado_tabela = mysqli_query($conn, $result_tabela);
        while($row_tabela = mysqli_fetch_row($resultado_tabela)){
            $tabelas[] = $row_tabela[0];
        }

        return $tabelas;
    }
    #endregion

    #region getTodasColunas
    private function getTodasColunas($conn, $tabela)
    {
        $query = "SELECT * FROM " . $tabela;
        $colunas = mysqli_query($conn, $query);

        return $colunas;
    }
    #endregion

    #region getTodosTiposDasColunas
    private function getTodosTiposDasColunas($conn, $tabela)
    {
        $query = "SHOW CREATE TABLE " . $tabela;
        $typesColumns = mysqli_query($conn, $query);

        return $typesColumns;
    }
    #endregion

    #region criarTabelas
    private function criarTabelas($conn, $tabela)
    {
        //Criar a intrução para apagar a tabela caso a mesma exista no BD
        $script = 'DROP TABLE IF EXISTS '.$tabela.';';

        //Pesquisar como a coluna é criada
        $tiposColunas = $this->getTodosTiposDasColunas($conn, $tabela);
        $row_cr_col = mysqli_fetch_row($tiposColunas);
        $script .= "\n\n" . $row_cr_col[1] . ";\n\n";

        return $script;
    }
    #endregion

    #region popularTabelas
    private function popularTabelas($tabela, $colunas, $numeroColunas)
    {
        $script = "";

        for($i = 0; $i < $numeroColunas; $i++)
        {
            //Ler o valor de cada coluna no bando de dados
            while($dados = mysqli_fetch_row($colunas))
            {
                //Criar a intrução da Query para inserir os dados
                $script .= 'INSERT INTO ' . $tabela . ' VALUES(';

                $script .= $this->lerDadosTabela($dados, $numeroColunas);

                $script .= ");\n";
            }
        }

        $script .= "\n\n";

        return $script;
    }
    #endregion

    #region lerDadosTabela
    private function lerDadosTabela($dados, $numeroColunas)
    {
        $values = "";

        for($j = 0; $j < $numeroColunas; $j++)
        {
            if(!empty($dados[$j]))
            {
                //addslashes — Adiciona barras invertidas a uma string
                $dados[$j] = addslashes($dados[$j]);

                //str_replace — Substitui todas as ocorrências da string \n pela \\n
                $dados[$j] = str_replace("\n", "\\n", $dados[$j]);
            }

            if(isset($dados[$j]))
            {
                if(!empty($dados[$j]))
                    $values .= '"' . $dados[$j].'"';
                else
                    $values .= 'NULL';
            }
            else
            {
                $values .= 'NULL';
            }

            if($j < ($numeroColunas - 1))
                $values .=',';
        }

        return $values;
    }
    #endregion

    #region criarArquivo
    private function criarArquivo($diretorio)
    {
        //Nome do Arquivo
        $now = date('Y-m-d-h-i-s');
        $nomeArquivo = $diretorio . "db_backup_".$now;

        $arquivo = fopen($nomeArquivo . '.sql', 'w+');
        fwrite($arquivo, $this->result);
        fclose($arquivo);

        return $nomeArquivo . ".sql";
    }
    #endregion

    #region criarHeadersDownload
    private function criarHeadersDownload($download)
    {
        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Cache-Control: private", false);
        header("Content-Type: application/force-download");
        header("Content-Disposition: attachment; filename=\"" . basename($download) . "\";");
        header("Content-Transfer-Encoding: binary");
        header("Content-Length: " . filesize($download));
    }
    #endregion

    #endregion

    #endregion
}