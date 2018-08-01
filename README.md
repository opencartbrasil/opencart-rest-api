[![license][licenca-badge]][LICENSE]

### Apresentação

API REST para OpenCart 2.1 ou superior, que permite o acesso a todas as tabelas do banco de dados incluindo as que não são nativas do OpenCart.

O controle de acesso a API REST é feito através da Chave da API que é cadastrada na administração do OpenCart.

Projetos incluídos (Related projects):

  - [PHP-CRUD-API](https://github.com/mevdschee/php-crud-api): Script PHP que adiciona uma API REST com acesso direto ao Banco de dados (Single file PHP script that adds a REST API).

### Requisitos (Requirements)

 1. PHP 5.3 ou superior.
 2. Biblioteca PDO habilitada no PHP.
 3. OpenCart 2.1 ou superior.

### Instalação (Installation)

 1. Faça o download: https://github.com/opencartbrasil/opencart-rest-api/archive/master.zip
 2. Descompacte o arquivo zip, e envie por FTP para o diretório raiz de sua loja os arquivo **api.php** e **config_api.php**.

**Pronto!**
 
### Configuração (Configuration)

 1. Acesse a administração de sua loja, e vá no menu **Configurações→Gerenciar Usuários→API** (System→Users→API).
 2. Clique no botão "**Novo**" (Add New), no campo "**Nome da API**" (API Name) coloque "**API REST**", logo abaixo, clique no botão "**Gerar**" (Generate) para criar sua "**Chave da API**", no campo "**Situação**" (Status) selecione a opção "**Habilitar**" (Enabled), e clique no botão "**Salvar**" (Save).
 
### Configurações extras (Extra Configuration)

#### Restringir o acesso a API por IP cadastrado através da administração da loja OpenCart (Restrict access IP):

Acesse a administração de sua loja, e vá no menu **Configurações→Gerenciar Usuários→API** (System→Users→API), localize a API com o nome "**API REST**", clique no botão "**Editar**" (Edit), clique na aba "**Endereço IP**" (IP Addresses), clique no botão "**Adicionar IP**" (Add IP), adicione o IP que você deseja que tenha acesso a API, e clique no botão "**Salvar**" (Save).
 
Agora edite o arquivo "**config_api.php**", e localize a linha:

```php
define('RESTRICT_IP', false);
```

E altere para:

```php
define('RESTRICT_IP', true);
```

Por último, salve as alterações no arquivo.

#### Gravar no log de sessões da API do OpenCart os acessos feitos através da API (Log Access API):

Edite o arquivo "**config_api.php**", e localize a linha:

```php
define('SESSION_LOG', false);
```

E altere para:

```php
define('SESSION_LOG', true);
```

Salve as alterações no arquivo, sendo que você poderá visualizar os logs de acesso através da administração de sua loja, no no menu **Configurações→Gerenciar Usuários→API** (System→Users→API), localize a API com o nome "**API REST**", clique no botão "**Editar**" (Edit), e clique na aba "**Sessão**" (Session).

### Utilização (Usage)

Acesse a URL da sua loja, incluindo no final o arquivo api.php, conforme o exemplo:

```http
http://www.seudominio.com.br/api.php
```

É necessário informar a sua Chave da API em todas as URLs da API que serão acessadas, passando-a no cabeçalho da requisição com o nome key tendo como valor a sua Chave da API.

Exemplo:

```js
key = mMAnvMIaP7zPHnF7hBi23ebGyIU6sp2eWRfdi08yNWOo8wRXPAWgCol
```

Caso contrário você receberá a mensagem de erro:

```js
{"API Error":"Key not found!"}
```

Qualquer tabela do banco de dados estará acessivel pela API, independente de ser nativa ou não do OpenCart, para acessar os dados ou enviar dados, deve-se solicitar ou enviar requisições HTTP utilizando os verbos GET, POST, PUT ou DELETE.

As URLs são formadas seguindo o padrão (The URLs are formed following the pattern):

```http
http://dominio/api.php/nome_tabela/{id} 
```

```http
http://domain/api.php/table_name/{id}
```

No exemplo abaixo, solicitamos todos os dados de produtos da tabela oc_product:

```http
GET http://www.seudominio.com.br/api.php/oc_product/
```

Neste outro exemplo, solicitamos os dados do produto com a coluna product_id igual a 40 da tabela oc_product:

```http
GET http://www.seudominio.com.br/api.php/oc_product/40
```

#### Documentação completa (Documentation)

[MANUAL DO PHP-CRUD-API](https://github.com/mevdschee/php-crud-api/blob/master/README.md)

### Acesso a API REST do OpenCart (Access to the REST API OpenCart)

#### - Listando os produtos: Em PHP (PHP Script) GET:

```php
<?php
$headers = array();
$headers[] = 'Content-Type: application/json';
$headers[] = 'key: mMAnvMIaP7zPHnF7hBi23ebGyIU6sp2eWRfdi08yNWOo8wRXPAWgCol'; // // Replace key value for API key OpenCart (Only numbers and letters)

$ch = curl_init();
curl_setopt_array($ch, [
	CURLOPT_URL            => 'http://www.seudominio.com.br/api.php/oc_product/', // Replace domain and table name
	CURLOPT_HTTPHEADER     => $headers,
	CURLOPT_CUSTOMREQUEST  => 'GET',
	CURLOPT_RETURNTRANSFER => true,
	CURLOPT_SSL_VERIFYHOST => false,
	CURLOPT_SSL_VERIFYPEER => false
]);
$out = curl_exec($ch);
curl_close($ch);
print_r( $out ); // Result json
```

#### - Cadastrando um departamento: Em PHP (PHP Script) POST:

```php
<?php
$headers = array();
$headers[] = 'Content-Type: application/json';
$headers[] = 'key: mMAnvMIaP7zPHnF7hBi23ebGyIU6sp2eWRfdi08yNWOo8wRXPAWgCol'; // // Replace key value for API key OpenCart (Only numbers and letters)

$data = array('name' => 'Samsung', 'image' => '', 'sort_order' => '0');

$ch = curl_init();
curl_setopt_array($ch, [
	CURLOPT_URL            => 'http://www.seudominio.com.br/api.php/oc_manufacturer/', // Replace domain and table name
        CURLOPT_HTTPHEADER     => $headers,
        CURLOPT_CUSTOMREQUEST  => 'POST',
        CURLOPT_POSTFIELDS     => json_encode($data),
        CURLOPT_POST           => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_SSL_VERIFYPEER => false
]);
$out = curl_exec($ch);
curl_close($ch);
print_r( $out ); // Result json
```

### Corrigindo o erro "Deprecated: Automatically populating $HTTP_RAW_POST_DATA..."

Se ao utilizar a API, você receber o erro abaixo:

**Deprecated: Automatically populating $HTTP_RAW_POST_DATA is deprecated and will be removed in a future version. To avoid this warning set 'always_populate_raw_post_data' to '-1' in php.ini and use the php://input stream instead. in Unknown on line 0**

No arquivo de configurações do PHP, que geralmente é o "**php.ini**", descomente ( apague o ; ) a linha abaixo:
```php
;always_populate_raw_post_data = -1
```

E reinicie o servidor web. 

#### Importante: Este erro costuma aparecer a partir da versão 5.6 do PHP.

[licenca-badge]: https://img.shields.io/badge/licença-GPLv3-blue.svg
[LICENSE]: ./LICENSE
