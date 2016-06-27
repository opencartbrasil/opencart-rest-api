### Resumo

API REST para OpenCart 2.1 ou superior, que permite o acesso a todas as tabelas do banco de dados incluindo as que não são nativas do OpenCart.

O controle de acesso a API REST é feito através da Chave da API que é cadastrada na administração do OpenCart.

Projetos incluídos:

  - [PHP-CRUD-API](https://github.com/mevdschee/php-crud-api): Script PHP que adiciona uma API REST com acesso direto ao Banco de dados.

Caso deseje doar um valor para contribuir com este trabalho continuo e sempre gratuito, clique no botão abaixo:

[![alt tag](https://www.paypalobjects.com/pt_BR/BR/i/btn/btn_donateCC_LG.gif)](https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=7G9TR9PXS6G5J)

### Requisitos

 1. PHP 5.3 ou superior.
 2. Biblioteca PDO habilitada no PHP.
 3. OpenCart 2.1 ou superior.

### Instalação

 1. Baixe a API no link: https://github.com/opencartbrasil/opencart-rest-api/releases. Localize a versão mais atual e compatível com sua versão do OpenCart, do arquivo "opencart-rest-api.ocmod.zip".
 2. Na administração da loja acesse o menu Extensions->Extension Installer (Extensões->Instalador).
 3. Na página do instalador, clique no botão Upload e selecione o arquivo 'opencart-rest-api.ocmod.zip' (que você baixou deste repositório), e aguarde a conclusão da instalação automática.
 
### Configuração

 1. Acesse a administração de sua loja, e vá no menu Configurações->Gerenciar Usuários->API.
 2. Clique no botão "Novo", no campo "Nome da API" coloque "API REST", logo abaixo, clique no botão "Gerar" para criar sua "Chave da API", no campo "Situação" selecione a opção "Habilitar", e clique no botão "Salvar".
 
### Configurações extras

#### Restringir o acesso a API por IP cadastrado através da administração da loja OpenCart:

 1. Acesse a administração de sua loja, e vá no menu Configurações->Gerenciar Usuários->API.
 2. Localize a API com o nome "API REST", clique no botão "Editar", clique na aba "Endereço IP", clique no botão "Adicionar IP", adicione o IP que você deseja que tenha acesso a API, e clique no botão "Salvar".
 
Edite o arquivo "config_api.php", e localize a linha:

```php
define('RESTRICT_IP', false);
```

Altere para:

```php
define('RESTRICT_IP', true);
```

E salve as alterações no arquivo.

#### Gravar no log de sessões da API do OpenCart, os acessos feitos através da API:

Edite o arquivo "config_api.php", e localize a linha:

```php
define('SESSION_LOG', false);
```

Altere para:

```php
define('SESSION_LOG', true);
```

E salve as alterações no arquivo, sendo que você poderá visualizar os logs de acesso através da administração de sua loja, no no menu Configurações->Gerenciar Usuários->API, localize a API com o nome "API REST", clique no botão "Editar", e clique na aba "Sessão".

### Utilização

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

### URLs de acesso a API REST

Qualquer tabela do banco de dados estará acessivel pela API, independente de ser nativa ou não do OpenCart, para acessar os dados ou enviar dados, deve-se solicitar ou enviar requisições HTTP utilizando os verbos GET, POST, PUT ou DELETE.

No exemplo abaixo, solicitamos todos os dados de produtos da tabela oc_product:

```http
GET http://www.seudominio.com.br/api.php/oc_product/
```

Neste outro exemplo, solicitamos os dados do produto com a coluna product_id igual a 40 da tabela oc_product:

```http
GET http://www.seudominio.com.br/api.php/oc_product/40
```

Para mais informações sobre todas as funcionalidades da API, incluindo paginação, ordem de exibição, etc, acesse:

[MANUAL DO PHP-CRUD-API](https://github.com/mevdschee/php-crud-api/blob/master/README.md)

### Exemplo de acesso externo a API REST do OpenCart:

#### - Em PHP:

```php
<?php
$headers = array();
$headers[] = 'Content-Type: application/json';
$headers[] = 'key: mMAnvMIaP7zPHnF7hBi23ebGyIU6sp2eWRfdi08yNWOo8wRXPAWgCol'; // // Replace Key Value (Only numbers and letters)

$ch = curl_init();
curl_setopt_array($ch, [
	CURLOPT_URL            => 'http://www.seudominio.com.br/api.php/oc_product/', // Replace domain and table prefix
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

### Como contribuir

 1. Faça um Fork do projeto e edite os arquivos que desejar.
 2. Faça um Pull para que suas sugestões de melhorias sejam avaliadas e aceitas, caso aprovadas.
 3. Abra uma Inssue com sua dúvida ou sugestão.

### Licença

[GNU General Public License version 3 (GPLv3)](https://github.com/opencartbrasil/opencart-rest-api
/blob/master/LICENSE)
