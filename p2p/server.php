<?php
require __DIR__ . '/vendor/autoload.php';

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;

class P2PFileServer implements MessageComponentInterface {
    protected $clients;
    protected $userIDs;           // Mapeia resourceId para ID do usuário
    protected $userConnections;   // Mapeia ID do usuário para a conexão

    public function __construct() {
        $this->clients = new \SplObjectStorage;
        $this->userIDs = [];
        $this->userConnections = [];
    }
    
    // Envia para todos os clientes a lista de usuários conectados
    private function broadcastUserList() {
        $list = array_keys($this->userConnections);
        foreach ($this->clients as $client) {
            $client->send(json_encode(['type' => 'usersList', 'users' => $list]));
        }
    }

    public function onOpen(ConnectionInterface $conn) {
        // Adiciona a nova conexão
        $this->clients->attach($conn);
        // Gera um ID único para o usuário (pode ser aprimorado conforme a necessidade)
        $userId = uniqid();
        $this->userIDs[$conn->resourceId] = $userId;
        $this->userConnections[$userId] = $conn;
        // Envia o ID para o usuário
        $conn->send(json_encode(['type' => 'id', 'id' => $userId]));
        echo "Novo usuário conectado: {$userId}\n";
        // Atualiza a lista de usuários para todos
        $this->broadcastUserList();
    }

    public function onMessage(ConnectionInterface $from, $msg) {
        $data = json_decode($msg, true);
        if (!$data) return;

        // Tratamento para atualização do ID do usuário
        if (isset($data['type']) && $data['type'] === 'updateID') {
            if (isset($data['newId'])) {
                $oldId = $this->userIDs[$from->resourceId];
                $newId = $data['newId'];
                // Verifica se o novo ID já está em uso
                if (isset($this->userConnections[$newId])) {
                    $from->send(json_encode(['type' => 'error', 'message' => 'ID já em uso.']));
                } else {
                    // Atualiza o mapeamento
                    unset($this->userConnections[$oldId]);
                    $this->userIDs[$from->resourceId] = $newId;
                    $this->userConnections[$newId] = $from;
                    $from->send(json_encode(['type' => 'update', 'message' => 'ID atualizado com sucesso!', 'id' => $newId]));
                    echo "Usuário {$oldId} atualizou seu ID para {$newId}\n";
                    // Atualiza a lista de usuários para todos
                    $this->broadcastUserList();
                }
            }
            return;
        }

        // Processamento para envio de arquivo: espera receber { to, fileName, fileData }
        if (isset($data['to'], $data['fileName'], $data['fileData'])) {
            $to = $data['to'];
            if (isset($this->userConnections[$to])) {
                $destConn = $this->userConnections[$to];
                $senderId = $this->userIDs[$from->resourceId];
                $payload = [
                    'type' => 'file',
                    'from' => $senderId,
                    'fileName' => $data['fileName'],
                    'fileData' => $data['fileData']
                ];
                $destConn->send(json_encode($payload));
                echo "Arquivo '{$data['fileName']}' enviado de {$senderId} para {$to}\n";
            } else {
                // Informa o remetente que o destinatário não foi encontrado
                $from->send(json_encode(['type' => 'error', 'message' => 'Destinatário não encontrado.']));
            }
        }
    }

    public function onClose(ConnectionInterface $conn) {
        // Remove a conexão e o mapeamento de usuário
        $this->clients->detach($conn);
        $userId = $this->userIDs[$conn->resourceId];
        unset($this->userIDs[$conn->resourceId]);
        unset($this->userConnections[$userId]);
        echo "Usuário desconectado: {$userId}\n";
        // Atualiza a lista de usuários para todos
        $this->broadcastUserList();
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "Erro: " . $e->getMessage() . "\n";
        $conn->close();
    }
}

// Inicia o servidor WebSocket na porta 8080
$server = IoServer::factory(
    new HttpServer(
        new WsServer(
            new P2PFileServer()
        )
    ),
    8080
);

echo "Servidor iniciado na porta 8080...\n";
$server->run();
