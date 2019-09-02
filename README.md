# Gestão de Salas
API criada para realizar a gestão de salas e agendamentos de seu uso.

# Instalação

Para o correto funcionamento, é necessário a instalação dos programas:

* PHP na versão 7.3.*;
* Composer na sua ultima versão disponível;
* Banco de dados MySql;

Todos devem estar na acessíveis na variável PATH.

Faça o download ou o clone do projeto, e na pasta do mesmo execute o comando:

```
composer install
```

Certifique-se de realizar a configuração do MySql, e criar um banco de dados para ser utilizado pela API. Na pasta do projeto, abra o arquivo *.env* e edite a linha com o conteúdo:

```
DATABASE_URL=mysql://4linux:4linux@127.0.0.1:3306/gestao_salas
```

Altere as configurações de acesso a base de dados, conforme o endereço IP, usuário, senha e base criada no mesmo.

Ainda na pasta do projeto, execute os comandos:

```
php bin\console doctrine:migration:migrate
php bin\console doctrine:fixtures:load
```
**Pode ser solicitado a confirmação da execução e até mesmo informar que dados serão perdidos, mas pode continuar normalmente.**

Ao executa-los, será criado as tabelas necessárias para correto funcionamento da API e populado a tabela de usuários com dados para podermos autenticar na API.

Para realizar a autenticação, utilize os dados:

* **Usuário**: 4linux
* **Senha**: 4linux

Para se autenticar, acesse o endereço do seu servidor + */login*, utilizando o método POST passando no corpo da requisição os dados de login. Algo como:

```
{
	"usuario": "4linux",
	"senha": "4linux"
}
```

Será retornado um access token, que deverá ser enviado nas demais autenticações via Bearer Authentication.

# Documentação

A documentação dos endpoints pode ser acessada pelo endereço */api/doc*, baseado na URL do seu servidor. Algo como:

```
http://127.0.0.1:8080/api/doc
```
