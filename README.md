📑 S3 & RDS Document Manager

Este projeto foi desenvolvido como um laboratório prático para consolidar conhecimentos em AWS Cloud Computing, integrando serviços de armazenamento de objetos, banco de dados gerenciado e segurança de acesso.

O objetivo principal foi criar um fluxo completo de gerenciamento de documentos (Upload, Listagem e Visualização) utilizando as melhores práticas da AWS.
🚀 Tecnologias e Serviços Utilizados

    AWS S3 (Simple Storage Service): Armazenamento escalável para os arquivos.

    AWS RDS (Relational Database Service): Banco de dados MySQL gerenciado para persistência de metadados.

    PHP 7.4+: Lógica de backend e integração com AWS SDK.

    Docker & Docker Compose: Containerização completa do ambiente de desenvolvimento.

    AWS Signature Version 4: Implementação de URLs pré-assinadas para acesso seguro.

🛠️ Decisões de Arquitetura & Segurança
1. Segurança via Presigned URLs

Os arquivos no S3 não estão públicos. Para garantir a segurança dos documentos, o sistema gera uma URL Pré-assinada com validade de 5 minutos sempre que um usuário solicita a visualização. Isso garante que o link expire e não possa ser compartilhado indevidamente.
2. Gestão de Variáveis de Ambiente

Toda a configuração sensível (Credentials, Endpoints e DB pass) é injetada via variáveis de ambiente (.env), seguindo os princípios do The Twelve-Factor App, evitando o hardcoding de segredos no código-fonte.
3. Visualização In-App

Utilização de um Modal dinâmico com iframe para renderização de documentos diretamente na aplicação, melhorando a experiência do usuário (UX) sem necessidade de download físico obrigatório.

 *** Como rodar o projeto

1. Clone o repositório:
    git clone https://github.com/seu-usuario/seu-repositorio.git

2. Configure suas credenciais: Renomeie o arquivo .env.example para .env e preencha com suas chaves da AWS e endpoint do RDS.

3. Suba os containers:
   docker-compose up -d
   
4. Acesse no navegador: http://localhost:8080


 *** Autor:
Douglas Almeida
In: https://www.linkedin.com/in/douglasalmeidadev94/












   
