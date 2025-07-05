# Onfly Trip Challenge

Gerenciamento de pedidos de viagens corporativas. Uma solução administrativa para empresas que desejam controlar e otimizar suas viagens corporativas.



https://github.com/user-attachments/assets/88fe4cd8-dad4-40c8-9815-3d806d7045c3

## Tecnologias Utilizadas
Abaixo estão listadas as principais tecnologias utilizadas no desenvolvimento deste projeto, testes e configuração do ambiente:

- **Backend**: PHP 8.3 com Laravel 12 
- **Frontend**: Vue.js 3, shadcnvue, Tailwind CSS, Axios, VueRouter, reverb
- **Banco de Dados**: MySQL 8.0
- **Testes**: PHPUnit, Pest
- **Ambiente de Desenvolvimento**: Docker, Composer, NPM, nginx
- **Controle de Versão**: Git, GitHub

## Pré-requisitos

Para executar este projeto, você precisa ter instalado em sua máquina:
- [**Docker**](https://docs.docker.com/engine/install/): Para criar e gerenciar contêineres.
- **Docker Compose**: Para orquestrar os contêineres.
- [**Git**](https://git-scm.com/book/en/v2/Getting-Started-Installing-Git): Para controle de versão e clonagem do repositório.

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
    docker-compose build 
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
> Idealmente utilizando sistem unix linux/macos
>
> Caso não conseguir utilizar os comandos configurados podem ser de grande ajuda no futuro


### Rodando a aplicação

Ao rodar os containers do docker, você já consegue utilizar os containers adequadamente ao acessar:
- Frontend: http://localhost:6162/frontend
- Backend: http://localhost:6162/api

Entretanto, para concluir o setup, você precisa habilitar as queues do Laravel e o WebSocket do reverb.
Para isso, execute o seguinte comando:

```sh
docker comopse exec onfly_trip_challenge_backend php artisan queue:work
docker compose exec onfly_trip_challenge_backend php artisan reverb:serve
```
>[!IMPORTANT]
> Esses comandos devem ser executados em terminais separados, pois eles ficarão rodando em background para processar as filas e as notificações em tempo real.


>[!NOTE]
>Você também pode optar por executar o sistema localmente com suas próprias configurações de ambiente.
> Porém seria necessário ter o PHP instalado, além do nodeJS, e o composer.
## Testando a aplicação

Uma vez que você chegou nesse ponto parabéns você já avançou significantemente, e no cenário atual, você pode executar os testes rodando o comando abaixo:

```sh
	docker-compose run --rm onfly_trip_challenge_backend php artisan test
```
>[!IMPORTANT]
> ## Testando permissões de administrador
> Um detalhe que deve ser observado é que por padrão não há uma forma de criar um usuário administrador apenas pela API
> Isso deve ser feito diretamente com acesso ao sistema ou banco de dados.
> Deixei configurado nas seeders um usuário e senha que podem ser utilizados como acesso administrador
> Para rodar a seeder basta utilizar o comando artisan abaixo:
> ```sh
> docker compose exec onfly_trip_challenge_backend php artisan db:seed
> ```
> #### Credenciais
> `email` : lucca@testOnfly.com
> `password` :  onflyTest123!
>




>[!TIP]
> Também deixei disponível uma collection do postman com os endpoints desenvolvidos basta importar a collection diretemente usando o arquivo `onfly-trip-challenge.postman_collection.json` na raíz do projeto
