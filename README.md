# Chat P2P com WebRTC e WebSocket

Este projeto implementa um sistema de chat peer-to-peer (P2P) utilizando **WebRTC** para a comunicação direta entre navegadores e **WebSocket** para a sinalização. A ideia é que dois usuários possam se conectar diretamente, trocando mensagens sem que o tráfego precise passar continuamente pelo servidor, o que reduz a latência e aumenta a privacidade.

## Visão Geral

- **Servidor de Sinalização (server.php):**  
  Utiliza PHP e a biblioteca [Ratchet](http://socketo.me/) para criar um servidor WebSocket. O servidor é responsável por:
  - Gerar um ID único para cada cliente que se conecta.
  - Encaminhar as mensagens de oferta, resposta e candidatos ICE entre os clientes para facilitar a negociação do WebRTC.

- **Cliente Web (index.html):**  
  Uma interface simples em HTML e JavaScript que:
  - Se conecta ao servidor de sinalização para obter um ID.
  - Permite que o usuário insira o ID de outro cliente para iniciar uma conexão.
  - Exibe notificações para aceitação de conexões.
  - Estabelece uma conexão WebRTC para a troca de mensagens via canal de dados (dataChannel).

## Instalação e Configuração
  1. Baixar os arquivos (server.php) e (index.html) e colocá-los na pasta 
  1. Instalação das Dependências do Servidor
     No diretório 
     composer require cboden/ratchet
  3. 
## Funcionalidades

- **Geração de ID Único:** Ao acessar a página, o cliente recebe um ID único gerado pelo servidor.
- **Sinalização via WebSocket:** Comunicação inicial para a troca de credenciais necessárias à conexão P2P.
- **Conexão P2P com WebRTC:** Após a sinalização, os clientes estabelecem uma conexão direta para troca de mensagens.
- **Notificações de Conexão:** O cliente receptor recebe uma solicitação de conexão e pode aceitar para iniciar a comunicação.
- **Interface Simples e Responsiva:** Um chat básico onde os usuários podem enviar e receber mensagens em tempo real.

## Tecnologias Utilizadas

- **Frontend:** HTML, JavaScript, WebRTC
- **Backend:** PHP, Ratchet (para WebSocket)
- **Ferramentas Auxiliares:**  
  - [Composer](https://getcomposer.org/) para gerenciamento de dependências PHP.

## Pré-requisitos

- PHP 8.1 ou superior
- Composer
- Navegador moderno (Chrome, Firefox, Edge, etc.) com suporte a WebRTC e WebSocket
- Caso deseje expor o serviço publicamente, é necessário configurar encaminhamento de portas ou usar um serviço como ngrok.

