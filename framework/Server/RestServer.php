<?php

namespace KaiokenFramework\Server;

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

/**
 * Servidor Rest Embarcado
 * @author Willian Brito (h1s0k4)
*/
class RestServer
{
    #region run
    public function run($request)
    {
        $response = NULL;        
        $api = $request["api"];

        try
        {
            $service = array_shift($api) . "Service";
            $method = array_shift($api);
            $params = $api;
            
            if (class_exists($service))
            {
                $service = new $service;

                if (method_exists($service, $method))
                {
                    $request["params"] = $params;
                    $response = call_user_func(array($service, $method), $request);
                    http_response_code(200);
                    return json_encode( array('status' => 'success', 'data' => $response), JSON_UNESCAPED_UNICODE);
                }
                else
                {
                    http_response_code(404);
                    $error_message = "Método {$service}->{$method} não encontrado";
                    return json_encode( array('status' => 'error', 'data' => $error_message), JSON_UNESCAPED_UNICODE);
                }
            }
            else
            {
                http_response_code(404);
                $error_message = "API {$service} não encontrada";
                return json_encode( array('status' => 'error', 'data' => $error_message), JSON_UNESCAPED_UNICODE);
            }
        }
        catch (\Exception $e)
        {
            http_response_code(500);
            return json_encode( array('status' => 'error', 'data' => $e->getMessage()), JSON_UNESCAPED_UNICODE);
        }
    }
    #endregion
}
