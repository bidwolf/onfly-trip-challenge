.PHONY: build up down backend-bash test

build:
	@echo "Construindo imagens..."
	docker-compose build

up:
	@echo "Subindo containers..."
	docker-compose up -d

down:
	@echo "Derrubando containers..."
	docker-compose down

restart: down up

backend-bash:
	docker-compose exec onfly_trip_challenge_backend bash

setup-backend: build up
	@echo "Configurando ambiente do backend..."
	docker-compose exec onfly_trip_challenge_backend composer install
	docker-compose exec onfly_trip_challenge_backend php artisan key:generate
	docker-compose exec onfly_trip_challenge_backend php artisan migrate --force
	docker-compose exec onfly_trip_challenge_backend php artisan jwt:secret
	docker-compose exec onfly_trip_challenge_backend npm install
	docker-compose exec onfly_trip_challenge_backend npm run build

	@echo "Backend pronto para uso!"
# --- Testes ---

# Executa os testes PHPUnit
# Garante que a imagem do backend esteja atualizada com as extensões necessárias (pdo_sqlite)
# E roda os testes dentro do container
test: build
	@echo "Executando testes PHPUnit..."
	docker-compose run --rm onfly_trip_challenge_backend php artisan test

# --- Limpeza ---

clean:
	@echo "Limpando volumes e imagens..."
	docker-compose down --volumes --rmi all
	# remove todas as imagens construídas pelo compose

