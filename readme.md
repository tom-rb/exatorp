#Curso Exato ERP

Esse projeto visa a construção de um sistema personalizado para gerenciar o [Curso Exato](http://www.cursoexato.preac.unicamp.br). Para iniciar um ambiente de desenvolvimento, são necessários:

* [GIT](https://git-scm.com/) e [PHP](http://windows.php.net/download/) - dispensam apresentações
* [Composer](https://getcomposer.org/) - gerenciador de dependências php
* [Npm](https://www.npmjs.com/get-npm) - gerenciador de dependências javascript (e outras funcionalidades)

Tenha todos instalados e, de preferência, disponíveis no path do seu usuário. É recomendado dar um update nessas
ferramentas, principalmente se instalou usando apt-get ou similares (eles costumam estar com versão atrasada).

	$ sudo -H composer self-update
	$ sudo npm install -g npm

Depois, comece clonando o repositório.

	$ git clone ssh://git@github.com:tom-rb/exatorp.git
	$ cd exato-erp

###Configurando .env

Antes de instalar as dependências, configure um arquivo .env para sua máquina copiando o .env.example existente. A principal mudança necessária é a configuração do banco de dados. Se tiver um MySQL instalado na máquina, apenas insira as credenciais corretas. É possível também usar o SQLite com:

    -- No seu .env
    
    DB_CONNECTION=sqlite
    
    -- Crie um arquivo vazio para iniciar o DB local
    $ mkdir exato-rp/storage/database
    $ touch exato-rp/storage/database/local.sqlite
    
    -- Crie também o banco usado para testes
    $ touch exato-rp/storage/database/testing.sqlite

É necessário **ativar a extensão de sqlite no seu php editando o php.ini**, mesmo que use MySQL, pois os testes estão configurados para rodar no sqlite mesmo. 

###Instalando dependências

Use o composer e o npm para baixar e instalar as dependências:

	$ composer install
	$ npm install --no-bin-links 

A última opção `--no-bin-links` é útil apenas se estiver numa máquina Windows, para Unix pode não incluir essa opção.

###Hora do play

Antes de começar a interagir com a aplicação, é necessário criar no banco de dados as tabelas que o sistema utiliza. Para isso, há um comando da ferramenta Artisan do Laravel (ver Frameworks abaixo):

     $ php artisan migrate --seed
     Migrating: 2014_10_12_000000_create_members_table
     Migrated:  2014_10_12_000000_create_members_table
     ...
     

Esse comando irá migrar o banco de dados, ou seja, aplicar a construção de tabelas já definidas, e ainda irá popular (`--seed`) a mesma com dados fantasia.
 
Por fim, um servidor web precisa ser lançado. Um jeito simples é usar o próprio servidor embutido do php configurado pelo artisan:

    $ php artisan serve
    Laravel development server started: <http://127.0.0.1:8000>
    ...

Acesse o endereço retornado pelo comando e o sistema deverá estar rodando.

##Frameworks

O desenvolvimento é feito sobre o [Laravel](https://laravel.com/docs/5.4) versão 5.4. A próxima versão (5.5) vai exigir o PHP7 e o servidor que o Exato dispõe ainda não dá suporte a isso, logo devemos ficar no 5.4 mesmo.

O CSS está utilizando o [Bulma](http://bulma.io) como base, um framework mais simples que o Bootstrap. Para javascript, a base escolhida é o [Vue.js](https://vuejs.org/).

Os testes seguem a estrutura proposta pelo Laravel, mais detalhes na [documentação](https://laravel.com/docs/5.4/testing) deles.

##Aprendizado

Grande parte do workflow e filosofia de desenvolvimento foi inspirada no trabalho de Jeffrey Way no site de video-aulas [laracasts.com](https://laracasts.com). Sugiro fortemente assistir algumas séries para ir captando a ideia. Para começar, veja [Laravel 5.4 from scratch](https://laracasts.com/series/laravel-from-scratch-2017).