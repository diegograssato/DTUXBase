DTUXBase
==========

Modulo base para se trabalhar com Zend Framework 2, ele já está apto a trablhar com Doctrine ORM e Doctrine ODM.
Conteúdo:
- Auth/Adapter - Adaptador para autenticação
- Controller/AbstractController - Controller padrão, basta ser extendido.
- Doc - Documentação
- Document - Modelo para entidades que utilizam ODM
- Entity - Modelo para entidades que utilizam ORM
- Logger - Futuramente interface para gravar logs
- Mail - Modelo de envio de e-mail
- Service - Serviços necessários que são utilizado pelo controller
- Test - Testes
- Util - Algumas funções uteis para o dia-a-dia em breve mais
- View - Helpers que auxiliam as views.

Obs.: A única camada que possui conectividade com banco de dados, é camada "Service".

Próximas implementações:
- Verificar a geração de index(ODM)
- Form
- Conectividade em Forms
- Suporte a orientdb-odm
- Terminar Logger
- Novos utilitarios
- Novos helpers

