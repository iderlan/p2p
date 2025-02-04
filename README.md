# Chat P2P com WebRTC e WebSocket

Este projeto implementa um sistema de chat peer-to-peer (P2P) utilizando **WebRTC** para a comunicação direta entre navegadores e **WebSocket** para a sinalização. A ideia é que dois usuários possam se conectar diretamente, trocando mensagens sem que o tráfego precise passar continuamente pelo servidor, o que reduz a latência e aumenta a privacidade.

## Visão Geral

- **Servidor de Sinalização (server.php):**  
  Utiliza PHP e a biblioteca [Ratchet](http://socketo.me/) para criar um servidor WebSocket. O servidor é responsável por:
  - Gerar um ID único para cada cliente que se conecta.
  - Encaminhar as mensagens de oferta, resposta e candidatos ICE entre os clientes para facilitar a negociação do WebRTC.

## Instalação e Configuração (windows)
  1. Baixar os arquivos (server.php) e (index.html) dentro da pasta (p2p), e colocar a pasta dentro do htdocs do xampp 
  
  1.2. O Composer precisa do git para baixar pacotes. Se o Git não estiver instalado, baixe e instale:
    https://git-scm.com/downloads
  Após a instalação, reinicie o CMD e teste se o Git está instalado digitando:
    git --version
  Se aparecer a versão do Git, então ele está instalado corretamente.

  1.3. O Composer precisa da extensão zip do PHP. Para ativá-la:

    Abra o arquivo php.ini do XAMPP (está em C:\xampp\php\php.ini).
    Procure por esta linha:
      ;extension=zip
    E remova o ";", ficando desta forma:
      extension=zip
    Salve e reinicie o servidor apache o xampp
    
  1.4. Instalação das Dependências do Servidor
     *Se não tiver, baixar e intalar o composer pelo link:https://getcomposer.org/
     *Abrir o cmd na pasta (htdocs/p2p)
     *Usar o comando: (composer require cboden/ratchet) para instalar as dependências

  2. Testando a conexão:
     2.1 Executando o servidor:
       No cmd, na pasta (p2p) inicie o servidor com o comando:
         php server.php
       Se aparecer: 
         Servidor WebSocket rodando na porta 12345...
       O servidor foi aberto
     2.2 executando o cliente:
       Abra uma nova aba no navegador e digite
         http://localhost/p2p/
     Faça isso em duas janelas diferentes.
  3. Testando a troca de mensagens
     cada aba terá um ID único, distribuido pelo servidor, para conectar:
       1. em um cliente coloque o ID do outro cliente
       2. clique em "conectar"
       3. No outro cliente aparecerá uma mensagem dizendo que tem um dispositivo querendo se conectar,
          permita essa conecxão apertando em "permitir"
       5. escreva algo e mande no chat, que a mensagem irá aparecer no outro cliente
    
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

