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
        $id = uniqid(); // Gera um ID único para cada cliente
        $this->clients[$id] = $conn;
        $conn->send(json_encode(["type" => "id", "id" => $id]));

        echo "Novo cliente conectado com ID: $id\n";
    }

    public function onMessage(ConnectionInterface $from, $msg) {
        $data = json_decode($msg, true);
        
        // Se o cliente quer se conectar a outro cliente
        if ($data["type"] == "connect" && isset($this->clients[$data["target"]])) {
            $target = $this->clients[$data["target"]];
            $target->send(json_encode(["type" => "offer", "offer" => $data["offer"], "from" => $data["from"]]));
        }

        // Se o cliente está respondendo uma oferta
        if ($data["type"] == "answer" && isset($this->clients[$data["target"]])) {
            $target = $this->clients[$data["target"]];
            $target->send(json_encode(["type" => "answer", "answer" => $data["answer"], "from" => $data["from"]]));
        }

        // Se um cliente quer enviar candidatos ICE
        if ($data["type"] == "candidate" && isset($this->clients[$data["target"]])) {
            $target = $this->clients[$data["target"]];
            $target->send(json_encode(["type" => "candidate", "candidate" => $data["candidate"], "from" => $data["from"]]));
        }

        // Envia metadados do arquivo (nome e tamanho)
        if ($data["type"] == "fileMetadata" && isset($this->clients[$data["target"]])) {
            $target = $this->clients[$data["target"]];
            $target->send(json_encode(["type" => "fileMetadata", "metadata" => $data["metadata"], "from" => $data["from"]]));
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
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "Erro: " . $e->getMessage() . "\n";
        $conn->close();
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
