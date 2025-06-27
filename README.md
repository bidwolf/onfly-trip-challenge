# Onfly Trip Challenge

Gerenciamento de pedidos de viagens corporativas. Uma solução administrativa para empresas que desejam controlar e otimizar suas viagens corporativas.

## Tecnologias Utilizadas
Abaixo estão listadas as principais tecnologias utilizadas no desenvolvimento deste projeto, testes e configuração do ambiente:
<!--
- **Backend**: PHP 8.2, Laravel 12
- **Frontend**: Vue.js 3, shadcnvue, Tailwind CSS, Axios
- **Banco de Dados**: MySQL 8.0
- **Testes**: PHPUnit, Pest
- **Ambiente de Desenvolvimento**: Docker, Composer, NPM
- **Controle de Versão**: Git, GitHub -->

## Pré-requisitos
Para executar este projeto, você precisa ter instalado em sua máquina:
- **Docker**: Para criar e gerenciar contêineres.
- **Docker Compose**: Para orquestrar os contêineres.
- **Git**: Para controle de versão e clonagem do repositório.

## Instalação
Para instalar e configurar o ambiente de desenvolvimento do projeto, siga os passos abaixo:

1. Clone o repositório e entre no diretório do projeto:
    ```bash
    git clone https://github.com/bidwolf/onfly-trip-challenge.git
    cd onfly-trip-challenge

    ```
### Variáveis de ambiente no backend (Laravel)
1. Navegue até a pasta backend e Crie um arquivo `.env` a partir do arquivo `.env.example`:
    ```bash
    cd backend
    cp .env.example .env
    ```
2. Configure as variáveis de ambiente no arquivo `.env` conforme necessário, especialmente as configurações do banco de dados.
  >[!NOTE]
  > Certifique-se que as variáveis DB_* estejam configuradas corretamente no docker-compose.yml. Qualquer alteração no arquivo `.env` deve ser refletida no `docker-compose.yml`.

### Variáveis de ambiente no frontend (Vue.js)
1. Navegue até a pasta frontend e copie o arquivo `.env.example` para `.env`:
    ```bash
    cd ../frontend
    cp .env.example .env
    ```

2. Configure as variáveis de ambiente no arquivo `.env` conforme necessário, especialmente as configurações de API.
   >[!NOTE]
    > Certifique-se que a variável VITE_API_URL esteja configurada corretamente no docker-compose.yml. Qualquer alteração no arquivo `.env` deve ser refletida no `docker-compose.yml`.

### Setup do Docker
1. Navegue até a raiz do projeto e execute o comando para iniciar os contêineres:
    ```bash
    cd ..
    docker-compose up -d
    ```
2. Aguarde até que todos os contêineres estejam em execução. Você pode verificar o status dos contêineres com:
    ```bash
    docker-compose ps
    ```
3. Instale as dependências do Laravel:
    ```bash
    docker-compose exec onfly_trip_challenge_backend composer install
    ```
4. Gere a API_KEY do Laravel e o secret do JWT:
    ```bash
    docker-compose exec onfly_trip_challenge_backend php artisan key:generate
    docker-compose exec onfly_trip_challenge_backend php artisan jwt:secret
    ```
5. Execute as migrations do banco de dados:
    ```bash
    docker-compose exec onfly_trip_challenge_backend php artisan migrate
    ```

>[!TIP]
> Adicionei um arquivo Makefile na raiz do projeto para facilitar alguns comandos comuns, como entrar no contêiner do backend, executar testes, entre outros. Você pode usar comandos como `make backend` para acessar o contêiner do backend ou `make test` para executar os testes.
> Para ver todos os comandos disponíveis, execute:
```bash
make help
```