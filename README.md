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

**O usuário deve ter acesso total a base criada, pois será utilizado para criar tabelas.**

Ainda na pasta do projeto, execute os comandos:

```
php bin/console doctrine:migration:migrate
php bin/console doctrine:fixtures:load
```
**Obs: Pode ser solicitado a confirmação da execução e até mesmo informar que dados serão perdidos, mas pode continuar normalmente.**

**Obs2: A direção das barras pode variar dependendo do SO onde está realizando a configuração.**

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

# Execução

Para iniciar o projeto, ainda na pasta dele, basta executar o comando:

```
php -S 0.0.0.0:8080 -t public/
```

Ele será iniciado com a URL base <http://localhost:8080>

Para acessar a tela onde poderá realizar buscas, basta acessar a URL diretamento <http://localhost:8080/>.
# Documentação

A documentação dos endpoints pode ser acessada pelo endereço */api/doc*, baseado na URL do seu servidor. Algo como <http://localhost:8080/api/doc>.

# MySql

Um exemplo de como realizar a configuração necessário do banco.

Conectado ao shell do MySql, com usuário *root* ou um que possua acesso a criar bases e usuários, execute os comandos:

```
create database gestao_salas;
CREATE USER '4linux'@'localhost' IDENTIFIED WITH mysql_native_password BY '4linux';
GRANT ALL PRIVILEGES ON gestao_salas.* TO '4linux'@'localhost';
flush privileges;
```

Com esses comandos, o banco já estará preparado, e não será necessário alterar nada no arquivo *.env*.
