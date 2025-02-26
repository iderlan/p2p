# Comunicação P2P com WebRTC e WebSocket

Este projeto implementa um sistema de chat peer-to-peer (P2P) utilizando **WebRTC** para a comunicação direta entre navegadores e **WebSocket** para a sinalização. A ideia é que dois usuários possam se conectar diretamente, trocando arquivos sem que o tráfego precise passar continuamente pelo servidor, o que reduz a latência e aumenta a privacidade.

---

## 📌 Visão Geral

- **Servidor de Sinalização (`server.php`)**
  - Utiliza PHP e a biblioteca [Ratchet](http://socketo.me/) para criar um servidor WebSocket.
  - Responsável por gerar um **ID único** para cada cliente.
  - Encaminha arquivos de **oferta, resposta e candidatos ICE** entre os clientes para facilitar a negociação WebRTC.
- **Versão 2.0** ⚠️
  - Arquivos podem ser enviados.
  - Até o momento não é possível conectar clientes de diferentes redes
  - Mas para teste, várias abas abertas podem se comunicar em localhost na mesma máquina
  - A página possui estilo css, e design responsivo
  -- Imagem da página html sem css.
  ---![image](https://github.com/user-attachments/assets/a67fa11a-1c86-4ccc-a6b5-44aedb16aabe)
## link Apresentando o projeto.
- **https://youtu.be/QmY2UA-s3vM**.
---

## 🛠 Instalação e Configuração (Windows)

### 1️⃣ Preparação do Ambiente

1. Baixar os arquivos `server.php` e `index.html`, criar uma pasta `p2p`, e colocar essa pasta dentro do diretório `htdocs` do XAMPP.

2. **Instalar o Git** *(necessário para o Composer baixar pacotes)*:
   - Baixe e instale o Git: [Download Git](https://git-scm.com/downloads)
   - Após a instalação, reinicie o CMD e teste se o Git está instalado:
     ```sh
     git --version
     ```
   - Se aparecer a versão do Git, então ele foi instalado corretamente.

3. **Ativar a extensão `zip` do PHP** *(necessário para o Composer funcionar corretamente)*:
   - Abra o arquivo `php.ini` do XAMPP (geralmente localizado em `C:\xampp\php\php.ini`).
   - Procure pela linha:
     ```ini
     ;extension=zip
     ```
   - Remova o `;` no início da linha para ativar a extensão:
     ```ini
     extension=zip
     ```
   - Salve o arquivo e reinicie o **servidor Apache** no XAMPP.


### 2️⃣ Instalação das Dependências do Servidor (Windows)

Se ainda não tiver o **Composer**, baixe e instale através do link: [Download Composer](https://getcomposer.org/)

1. No CMD, navegue até a pasta `p2p` dentro do `htdocs`:
   ```sh
   cd C:\xampp\htdocs\p2p
   ```
2. Instale as dependências necessárias:
   ```sh
   composer require cboden/ratchet
   ```

---
### 2️⃣ Instalação das Dependências do Servidor (Linux)

dentro da pasta p2p abra o terminal
1. instale o php:
   ```sh
   sudo apt update
   sudo apt install php php-cli php-common php-xml php-curl php-zip
   ```
3. instalar o composer
   ```sh
   sudo apt install curl php-cli php-mbstring git unzip
   curl -sS https://getcomposer.org/installer | php
   sudo mv composer.phar /usr/local/bin/composer
   ```
4. verificar se foi tudo devidamente instalado
   ```sh
   composer --version
   ```
5. Instale as dependências necessárias:
   ```sh
   composer require cboden/ratchet
   ```
---

## 🚀 Testando a Conexão

### 1️⃣ Executando o Servidor

1. No CMD, ou no terminal linux dentro da pasta `p2p`, inicie o servidor WebSocket:
   ```sh
   php server.php
   ```
2. Se tudo estiver correto, aparecerá a mensagem:
   ```sh
   Servidor WebSocket rodando na porta 12345...
   ```

### 2️⃣ Executando o Cliente

1. Abra um navegador e digite:
   ```sh
   http://localhost/p2p/
1. ou aperte para abrir o arquivo index.html no navegador:
   ```sh
   http://localhost/p2p/
   ```
2. Abra duas abas diferentes do navegador para simular dois clientes distintos.

### 3️⃣ Testando a Troca de arquivos

1. Cada aba terá um **ID único**, distribuído pelo servidor.
2. Para conectar os clientes:
   - Em um cliente, **insira o ID** do outro cliente.
   - Clique em **"Conectar"**.
   - No outro cliente, aparecerá uma mensagem solicitando permissão para a conexão.
   - Clique em **"Permitir"**.
3. Agora, escreva uma mensagem e envie. A mensagem deve aparecer no outro cliente instantaneamente.

---

## 🎯 Funcionalidades

✅ **Geração de ID Único:** Cada cliente recebe um identificador único ao acessar a página.
✅ **Sinalização via WebSocket:** Comunicação inicial para a troca de credenciais WebRTC.
✅ **Conexão P2P com WebRTC:** Após a sinalização, os clientes estabelecem uma conexão direta.
✅ **Notificações de Conexão:** O cliente receptor recebe uma solicitação e pode aceitar ou rejeitar.
✅ **Interface Simples e Responsiva:** Um chat básico para envio e recebimento de arquivos em tempo real.

---

## 🏗 Tecnologias Utilizadas

- **Frontend:** HTML, JavaScript, WebRTC
- **Backend:** PHP, Ratchet (para WebSocket)
- **Ferramentas Auxiliares:**
  - [Composer](https://getcomposer.org/) para gerenciamento de dependências PHP.

---

## 📌 Pré-requisitos

- PHP **8.1** ou superior
- Composer
- Navegador moderno (Chrome, Firefox, Edge, etc.) com suporte a **WebRTC** e **WebSocket**
