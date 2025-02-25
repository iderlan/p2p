<?php
require 'vendor/autoload.php';

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;
use Ratchet\Http\HttpServer;

class SignalingServer implements MessageComponentInterface {
    private $clients = [];

    public function onOpen(ConnectionInterface $conn) {
        // Gera um ID único para cada cliente ao conectar
        $id = uniqid();
        $this->clients[$id] = $conn;
        $conn->send(json_encode(["type" => "id", "id" => $id]));
        echo "Novo cliente conectado com ID: $id\n";
        $this->broadcastUserList();
    }

    public function onMessage(ConnectionInterface $from, $msg) {
        $data = json_decode($msg, true);
        
        // Alteração de ID
        if (isset($data["type"]) && $data["type"] == "changeId") {
            $newId = trim($data["newId"]);
            if (isset($this->clients[$newId])) {
                $from->send(json_encode([
                    "type" => "error",
                    "message" => "ID '$newId' já está em uso."
                ]));
                return;
            } else {
                $oldId = null;
                foreach ($this->clients as $id => $client) {
                    if ($client === $from) {
                        $oldId = $id;
                        break;
                    }
                }
                if ($oldId !== null) {
                    unset($this->clients[$oldId]);
                }
                $this->clients[$newId] = $from;
                $from->send(json_encode([
                    "type" => "idChanged",
                    "newId" => $newId
                ]));
                echo "Cliente com ID $oldId alterou para $newId\n";
                $this->broadcastUserList();
                return;
            }
        }
        
        // Encaminha a oferta de conexão
        if (isset($data["type"]) && $data["type"] == "connect" && isset($this->clients[$data["target"]])) {
            $target = $this->clients[$data["target"]];
            $target->send(json_encode([
                "type"  => "offer", 
                "offer" => $data["offer"], 
                "from"  => $data["from"]
            ]));
        }

        // Encaminha a resposta da conexão
        if (isset($data["type"]) && $data["type"] == "answer" && isset($this->clients[$data["target"]])) {
            $target = $this->clients[$data["target"]];
            $target->send(json_encode([
                "type"   => "answer", 
                "answer" => $data["answer"], 
                "from"   => $data["from"]
            ]));
        }

        // Encaminha os candidatos ICE
        if (isset($data["type"]) && $data["type"] == "candidate" && isset($this->clients[$data["target"]])) {
            $target = $this->clients[$data["target"]];
            $target->send(json_encode([
                "type"      => "candidate", 
                "candidate" => $data["candidate"], 
                "from"      => $data["from"]
            ]));
        }

        // Encaminha os metadados do arquivo
        if (isset($data["type"]) && $data["type"] == "fileMetadata" && isset($this->clients[$data["target"]])) {
            $target = $this->clients[$data["target"]];
            $target->send(json_encode([
                "type"     => "fileMetadata", 
                "metadata" => $data["metadata"], 
                "from"     => $data["from"]
            ]));
        }
    }

    public function onClose(ConnectionInterface $conn) {
        foreach ($this->clients as $id => $client) {
            if ($client === $conn) {
                unset($this->clients[$id]);
                echo "Cliente desconectado: $id\n";
                break;
            }
        }
        $this->broadcastUserList();
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "Erro: " . $e->getMessage() . "\n";
        $conn->close();
    }
    
    private function broadcastUserList() {
        $userList = array_keys($this->clients);
        foreach ($this->clients as $client) {
            $client->send(json_encode([
                "type"  => "users",
                "users" => $userList
            ]));
        }
    }
}

$server = IoServer::factory(
    new HttpServer(
        new WsServer(new SignalingServer())
    ),
    12345
);

echo "Servidor WebSocket rodando na porta 12345...\n";
$server->run();
