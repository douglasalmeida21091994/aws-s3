# 📑 S3 & RDS Document Manager

Este projeto foi desenvolvido como um laboratório prático para consolidar conhecimentos em **AWS Cloud Computing**, integrando serviços de armazenamento de objetos, banco de dados gerenciado e segurança de acesso.

O foco técnico foi garantir uma arquitetura escalável, segura e um ambiente de desenvolvimento robusto, simulando um cenário real de engenharia de software.

---

## 🚀 Tecnologias e Serviços Utilizados

* **AWS S3 (Simple Storage Service):** Armazenamento de arquivos com alta disponibilidade e escalabilidade.
* **AWS RDS (Relational Database Service):** Banco de dados MySQL gerenciado para persistência de metadados.
* **PHP 7.2 (Ubuntu 18.04):** Backend robusto integrado ao **AWS SDK for PHP**.
* **Docker & Docker Compose:** Containerização completa da Stack (Apache/PHP), garantindo paridade entre ambientes.
* **AWS Signature Version 4:** Implementação de URLs pré-assinadas (Presigned URLs) para controle de acesso granular.
* **Xdebug:** Ambiente configurado para depuração avançada dentro de containers via VS Code.

---

## 🛠️ Decisões de Arquitetura & Segurança

### 1. Segurança via Presigned URLs

Os arquivos armazenados no S3 não estão públicos. Para garantir a privacidade, o sistema utiliza o SDK da AWS para gerar uma URL temporária com validade de 5 minutos apenas no momento da solicitação. Isso mitiga riscos de compartilhamento indevido e acesso direto aos objetos.

### 2. Gestão de Variáveis de Ambiente

Seguindo as boas práticas do **The Twelve-Factor App**, toda a configuração sensível (Credentials, Endpoints e DB Pass) é injetada via arquivo `.env`. Isso evita o *hardcoding* de segredos no código-fonte e facilita a migração para serviços como o *AWS Secrets Manager*.

### 3. Visualização In-App e UX

Utilização de um Modal dinâmico com **Bootstrap 5** e **SweetAlert2** para renderização de documentos via `iframe`. Essa abordagem melhora significativamente a experiência do usuário (UX), permitindo a conferência do arquivo sem a necessidade de downloads manuais constantes.

### 4. Ambiente de Desenvolvimento Profissional

Diferente de ambientes simples, este projeto conta com **Xdebug** totalmente configurado via Docker, permitindo o uso de *breakpoints* e inspeção de variáveis em tempo real no VS Code, elevando a qualidade técnica do ciclo de desenvolvimento.

---

## 📦 Como rodar o projeto

1. **Clone o repositório:**
```bash
git clone https://github.com/douglasalmeida21091994/aws-s3.git

```

2. **Configure suas credenciais:**
Renomeie o arquivo `.env.example` para `.env` e preencha com suas chaves da AWS e endpoint do RDS.
3. **Suba os containers:**
```bash
docker-compose up -d --build

```

4. **Acesse no navegador:**
http://localhost:8080

---

## 👨‍💻 Autor

**Douglas Almeida**

* **LinkedIn:** https://www.linkedin.com/in/douglasalmeidadev94

