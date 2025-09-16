# API de CEP

A API de CEP disponibiliza uma API RESTful que permite consultar dados detalhados sobre endereços, incluindo nome da rua, bairro, cidade, estado e região.

* **951.053** Endereços
* **5.308** Cidades
* **27** Estados

### Construído Com

Abaixo está as tecnologias usadas no desenvolvimento desse projeto.

* [Mysql](https://www.mysql.com/)
* [Laravel 11](https://laravel.com/docs/11.x)

## Respostas

| Código | Descrição |
|---|---|
| `200` | Requisição executada com sucesso (success).|
| `400` | CEP informado não existe no sistema.|
| `404` | CEP pesquisado não encontrado (Not found).|
 
## Consultar CEP

`GET /cep/{cep}`

Resposta:

`200 OK`: Retorna um objeto JSON contendo informações detalhadas sobre o endereço correspondente ao CEP fornecido.

Exemplo de resposta:
   ```js
    {
        "cep": "01001000",
        "rua": "Praça da Sé",
        "bairro": "Sé",
        "cidade": "São Paulo",
        "uf": "SP",
        "regiao": "Sudeste"
    }
   ```
`404 Not Found`: Se nenhum endereço for encontrado para o CEP fornecido.
Exemplo de resposta:
   ```js
    {
        "error":"Endereço não encontrado para o CEP fornecido 99999999"}
    }
   ```


## Buscar Cidades

`GET /cidades/{uf}`

Resposta:

`200 OK`: Retorna um objeto JSON contendo uma lista de todas as cidades correspondente ao UF fornecido.


Exemplo de resposta:
   ```js
   [
      {
         "cidade": "Arapiraca"
      },
      {
         "cidade": "Maceió"
      },
      {
         "cidade": "Palmeira dos Índios"
      }
   ]
   ```
`404 Not Found`: Se nenhuma cidade for encontrado para o UF fornecido.
Exemplo de resposta:
   ```js
    {
        "error":"Nenhuma Cidade foi encontrada com o UF fornecido: BU"}
    }
   ```

## Buscar Cidade

`GET /cidade/{cidade}/{uf?}`

Resposta:

`200 OK`: Retorna um objeto JSON contendo uma lista de todas as cidades.


Exemplo de resposta sem UF:
   ```js
   [
      {
         "cidade": "São Paulo",
         "uf": "SP"
      },
      {
         "cidade": "São Paulo",
         "uf": "RS"
      }
   ]
   ```

Exemplo de resposta com UF:
   ```js
      {
         "cidade": "São Paulo",
         "uf": "SP"
      }
   ```

`404 Not Found`: Se nenhuma cidade for encontrada.

Exemplo de resposta sem UF:
   ```js
    {
        "error":'Nenhuma cidade encontrada com o nome "São Paulos".'}
    }
   ```

Exemplo de resposta com UF:

   ```js
    {
        "error":'Nenhuma cidade encontrada com o nome "São Paulos" e UF "RJ".'}
    }
   ```

## Começando

Veja abaixo todos os passos para fazer a instalação corretamente da aplicação.

### Pré-requisitos

* **PHP 8.1+**
* **MariaDB 10.6** database
* **Nginx** Servidor Web

### Instalação Docker

Antes de começar será necessário instalar o [Docker](https://www.docker.com/) no seu servidor/máquina.
com o docker instalado siga os próximos passos abaixo.

1. Clone o repositório
   ```sh
   git clone https://github.com/brunoribeiro-lab/api-cep.git
   ```
2. Copie o .env.example para .env e abra o arquivo `.env` e substitua pelas informações corretas:
   ```sh
   cp .env.example .env
   ```
   Defina as configurações no arquivo .env
   ```js
    DB_HOST=127.0.0.1               // IP do banco de dados
    DB_PORT=3336                    // porta do banco de dados
    DB_DATABASE=api_cep             // nome do banco de dados
    DB_USERNAME=root                // nome de usuário do banco de dados
    DB_PASSWORD=senha_do_banco      // senha do banco de dados
    APP_URL=http://localhost:8000   // url da aplicação + porta (caso seja diferente de 80 não precisa informar a porta)
    APP_PORT=8080                   // porta da aplicação   ex: 8080
   ```

   Caso você esteja usando o docker compose (que não é recomendado em produção), as credenciais do banco criada será a mesma do arquivo .env

3. Executando o Docker Composer
   ```sh
   docker compose up -d --build start
   ```

4. Instalando as bibliotecas
   ```sh
   docker compose run --rm composer update
   ```

5. Gerando uma chave do Laravel
   ```sh
   docker compose run -rm artisan key:generate
   ```

6. Importando as tabelas
   ```sh
   docker compose run --rm artisan migrate
   ```

7. Importando os dados do endereço
   ```sh
   docker compose run --rm artisan db:seed
   ```
8. Rodar os testes
   ```sh
   docker compose run --rm artisan test
   ```

Portas expostas detalhadas para o .env de exemplo
 
- **nginx** - `:8080`
- **mysql** - `:3336`