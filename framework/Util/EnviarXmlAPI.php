<?php

$i = 0;
$path = __DIR__ . DIRECTORY_SEPARATOR . 'xml/';
$api = 'http://localhost:8081/Apps/AreaContador/api/NFeXml/import/40129685000195';
$apiProducao = 'https://www.msystemsoftware.com.br/Apps/AreaContador/index.php/api/NFeXml/import/40129685000195';
$directory = new DirectoryIterator($path);

foreach($directory as $file)
{
    if($file->getExtension() == "xml")
    {
        
        $filename = $path . $file->getFilename();
        $xml = file_get_contents($filename);
    
        $dadosParaEnviar = http_build_query( array('xml' => $xml) );
        $opcoes = array('http' =>
            array(
                'method'  => 'POST',
                'header'  => 'Content-Type: application/x-www-form-urlencoded',
                'content' => $dadosParaEnviar
            )
        );
    
        $contexto = stream_context_create($opcoes);
        $result   = file_get_contents($api, false, $contexto);
        $i++;

        echo "0$i - $result\n";  
    }
}

 