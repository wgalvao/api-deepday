<?php

class DbHandler {

    private $conn;

    function __construct() {
        require_once dirname(__FILE__) . '/DbConnect.php';
        // abrindo conexão
        $db = new DbConnect();
        $this->conn = $db->connect();
    }

    /* ------------- `clientes` table method ------------------ */

    public function cad_cliente($nome,$telefone){
        $stmt = $this->conn->prepare("INSERT INTO clientes(nome, telefone) values(?, ?)");
        $stmt->bind_param("ss", $nome, $telefone);
        $result = $stmt->execute();
        $stmt->close();  
        return true;
    }

    public function getClientebyid($id){
        $stmt = $this->conn->prepare("SELECT id, nome, telefone FROM clientes WHERE id = ?");
        $stmt->bind_param("s", $id);
        if ($stmt->execute()) {            
            $stmt->bind_result($id, $nome, $telefone);
            $stmt->fetch();
            $clientes = array();
            $clientes["id"] = $id;
            $clientes["nome"] = $nome;
            $clientes["telefone"] = $telefone;
            $stmt->close();

            if($clientes["id"] == NULL){
                return NULL;
            }

            return $clientes;
        } else {
            return NULL;
        }
    }

    public function getAllClientes(){
        $stmt = $this->conn->prepare("SELECT * FROM clientes");
        $stmt->execute();
        $tasks = $stmt->get_result();
        $stmt->close();
        return $tasks;
    }

    public function deleteCliente($id){
        $stmt = $this->conn->prepare("DELETE FROM clientes WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $num_affected_rows = $stmt->affected_rows;
        $stmt->close();
        return $num_affected_rows > 0;
    }

    public function updateCliente($id, $nome, $telefone){
        $stmt = $this->conn->prepare("UPDATE clientes set nome = ?, telefone = ? WHERE id = ?");
        $stmt->bind_param("ssi", $nome, $telefone, $id);
        $stmt->execute();
        $num_affected_rows = $stmt->affected_rows;
        $stmt->close();
        return $num_affected_rows > 0;
    }

}

?>
