## Estartar\Core

Core é um micro framework MVC feito em PHP, construído como estudo do padrão de arquitetura e do ciclo de vida da aplicação.
Muito fácil de configurar e usar, facilita tarefas comuns e permite a criação de sistemas em pouco tempo, com pouco código.
Um ótimo ponto de partida para o entendimento de conceitos que irão facilitar a migração para frameworks mais robustos como Laravel, Zend.

## Instalação

```bash
composer require estartar/core
```

## Configuração

Para começar a usar, precisamos de um arquivo de configurações como o modelo

```php
define( 
    "CONFIG", [
        "BASEDIR"           => str_replace('\\', '/', dirname(__FILE__)),
        "BASEURL"           => "http://localhost:8080",
        "BASEFOLDER"        => "", 
        "BASECONTROLLER"    => "Home", 
        "BASEMETHOD"        => "Index", 
        "BASEPARAMS"        => [],

        "DBDRIVER"          => "mysql",
        "DBHOST"            => "",
        "DBPORT"            => "3306",
        "DBNAME"            => "",
        "DBUSER"            => "",
        "DBPASSWORD"        => "",
        "DBOPTIONS"         => [
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8",
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
            PDO::ATTR_CASE => PDO::CASE_NATURAL,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
)    
```

## License

The MIT License (MIT). 