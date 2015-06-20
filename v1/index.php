<?php

require_once '../include/DbHandler.php';
require '.././libs/Slim/Slim.php';

\Slim\Slim::registerAutoloader();

$app = new \Slim\Slim();


/**
 * Listing all clientes of particual user
 * method GET
 * url /clientes          
 */
$app->get('/clientes', function() {
            global $user_id;
            $response = array();
            $db = new DbHandler();

            // fetching all user clientes
            $result = $db->getAllClientes();

            $response["error"] = false;
            $response["clientes"] = array();

            // looping through result and preparing clientes array
            while ($task = $result->fetch_assoc()) {
                $tmp = array();
                $tmp["id"] = $task["id"];
                $tmp["nome"] = $task["nome"];
                $tmp["telefone"] = $task["telefone"];
                array_push($response["clientes"], $tmp);
            }

            echoRespnse(200, $response);
        });

/**
 * Listing single task of particual user
 * method GET
 * url /clientes/:id
 * Will return 404 if the task doesn't belongs to user
 */
$app->get('/clientes/:id', function($id) {
            $response = array();
            $db = new DbHandler();

            // fetch task
            $result = $db->getClientebyid($id);

            if ($result != NULL) {
                $response["error"] = false;
                $response["id"] = $result["id"];
                $response["nome"] = $result["nome"];
                $response["telefone"] = $result["telefone"];
                echoRespnse(200, $response);
            } else {
                $response["error"] = true;
                $response["message"] = "O código informado não exite";
                echoRespnse(404, $response);
            }
        });

/**
 * Creating new task in db
 * method POST
 * params - name
 * url - /clientes/
 */
$app->post('/clientes', function() use ($app) {
            // check for required params
            $response = array();

            $nome = $app->request->post('nome');
            $telefone = $app->request->post('telefone');

            $db = new DbHandler();

            // creating new task
            $result = $db->cad_cliente($nome, $telefone);

            if ($result != NULL) {
                $response["error"] = false;
                $response["message"] = "Cliente adicionado";
                echoRespnse(201, $response);
            } else {
                $response["dd"] =  $nome;
                $response["error"] = true;
                $response["message"] = "Falha ao criar, entre em contato com o suporte";
                echoRespnse(200, $response);
            }            
        });

/**
 * Updating existing task
 * method PUT
 * params task, status
 * url - /clientes/:id
 */
$app->put('/clientes/:id', function($id) use($app) {
         
            $nome = $app->request->put('nome');
            $telefone = $app->request->put('telefone');

            $db = new DbHandler();
            $response = array();

            // updating task
            $result = $db->updateCliente($id, $nome, $telefone);
            if ($result) {
                // task updated successfully
                $response["error"] = false;
                $response["message"] = "Cliente atualizado";
            } else {
                // task failed to update
                $response["error"] = true;
                $response["message"] = "Ocorreu uma falha ao atualizar!";
            }
            echoRespnse(200, $response);
        });

/**
 * Deleting task. Users can delete only their clientes
 * method DELETE
 * url /clientes
 */
$app->delete('/clientes/:id', function($id) use($app) {
            global $user_id;

            $db = new DbHandler();
            $response = array();
            $result = $db->deleteCliente($id);
            if ($result) {
                // task deleted successfully
                $response["error"] = false;
                $response["message"] = "O cliente foi deletado";
            } else {
                // task failed to delete
                $response["error"] = true;
                $response["message"] = "Houve um erro ao deletar!";
            }
            echoRespnse(200, $response);
        });

function echoRespnse($status_code, $response) {
    $app = \Slim\Slim::getInstance();
    // Http response code
    $app->status($status_code);

    // setting response content type to json
    $app->contentType('application/json');

    echo json_encode($response);
}

$app->run();
?>