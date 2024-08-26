# FaleAqui - Aplicação de Ordens de Serviço

Este projeto consiste em uma API em Laravel chamada `faleaqui_api` e um aplicativo em React Native (usando Expo) chamado `FaleaquiApp`. O sistema permite o cadastro de usuários, login, criação e visualização de ordens de serviço com localização via GPS.

## Passo a Passo para Clonar e Configurar o Projeto

### 1. Clonar o Repositório

    Primeiro, você deve clonar o repositório que contém tanto a API quanto o aplicativo.
```bash
    git clone https://github.com/seu-usuario/seu-repositorio.git
    cd seu-repositorio
```
### 2. Configurando a API (Laravel)

2.1. Acesse a pasta da API

```bash
    cd faleaqui_api
```
2.2. Instalar Dependências
Instale as dependências do projeto Laravel usando o Composer:

```bash
    composer install
```
2.3. Configuração do Arquivo .env
Copie o arquivo de exemplo .env.example para .env:

```bash
    cp .env.example .env
```

2.3.1. Abra o arquivo .env e configure as variáveis de ambiente, como o banco de dados, por exemplo:

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=faleaqui_db
DB_USERNAME=seu_usuario
DB_PASSWORD=sua_senha

2.4. Gerar a Chave da Aplicação
Gere a chave da aplicação Laravel:

```bash
    php artisan key:generate
```

2.5. Migrar o Banco de Dados
Execute as migrações para criar as tabelas no banco de dados:

```bash
    php artisan migrate
```

2.6. Iniciar o Servidor Local
Inicie o servidor Laravel para testar a API localmente:

```bash
    php artisan serve
```


### 3. Configurando o Aplicativo (React Native)

3.1. Acesse a pasta do aplicativo

```bash
    cd ../FaleaquiApp
```

3.2. Instalar Dependências
Instale as dependências do projeto React Native usando o npm:

```bash
    npm install
```

3.3. Iniciar o Expo
Inicie o servidor Expo para rodar o aplicativo:

```bash
    expo start
```