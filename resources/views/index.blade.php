<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Documentação API de CEP</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        h1, h2, h3 {
            color: #333;
        }
        pre {
            background: #f4f4f4;
            border: 1px solid #ddd;
            padding: 10px;
            overflow-x: auto;
        }
        .endpoint {
            background: #efefef;
            padding: 15px;
            margin-bottom: 30px;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <header>
        <h1>API de CEP</h1>
        <p>Esta API disponibiliza endpoints para consultar endereços, buscar cidades por UF e obter informações de cidades.</p>
    </header>
    
    <section class="endpoint">
        <h2>Consultar CEP</h2>
        <p><strong>Método:</strong> GET</p>
        <p><strong>Endpoint:</strong> <code>/cep/{cep}</code></p>
        
        <h3>Resposta de Sucesso (200 OK):</h3>
        <pre>
{
    "cep": "01001000",
    "rua": "Praça da Sé",
    "bairro": "Sé",
    "cidade": "São Paulo",
    "uf": "SP",
    "regiao": "Sudeste"
}
        </pre>
        
        <h3>Resposta de Erro - Não Encontrado (404 Not Found):</h3>
        <pre>
{
    "error": "Endereço não encontrado para o CEP fornecido 99999999"
}
        </pre>
    </section>
    
    <section class="endpoint">
        <h2>Buscar Cidades</h2>
        <p><strong>Método:</strong> GET</p>
        <p><strong>Endpoint:</strong> <code>/cidades/{uf}</code></p>
        
        <h3>Resposta de Sucesso (200 OK):</h3>
        <pre>
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
        </pre>
        
        <h3>Resposta de Erro - Não Encontrado (404 Not Found):</h3>
        <pre>
{
    "error": "Nenhuma Cidade foi encontrada com o UF fornecido: BU"
}
        </pre>
    </section>

    <section class="endpoint">
        <h2>Buscar Cidade</h2>
        <p><strong>Método:</strong> GET</p>
        <p><strong>Endpoint:</strong> <code>/cidade/{cidade}/{uf?}</code></p>
        
        <h3>Resposta sem UF (200 OK):</h3>
        <pre>
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
        </pre>
        
        <h3>Resposta com UF (200 OK):</h3>
        <pre>
{
    "cidade": "São Paulo",
    "uf": "SP"
}
        </pre>
        
        <h3>Resposta de Erro - Não Encontrado (404 Not Found):</h3>
        <p><strong>Sem UF:</strong></p>
        <pre>
{
    "error": "Nenhuma cidade encontrada com o nome \"São Paulos\"."
}
        </pre>
        <p><strong>Com UF:</strong></p>
        <pre>
{
    "error": "Nenhuma cidade encontrada com o nome \"São Paulos\" e UF \"RJ\"."
}
        </pre>
    </section>
</body>
</html>