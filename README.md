# Árvores de Escrevivência – Site Institucional IFRN

Aplicação web para gerenciamento de conteúdos institucionais (posts, imagens, vídeos e comentários) desenvolvida em PHP com MySQL, voltada para uso em ambiente educacional.

## Índice

- Sobre o projeto
- Funcionalidades
- Tecnologias
- Arquitetura e organização
- Requisitos
- Como executar o projeto
- Configuração do banco de dados
- Estrutura de usuário e permissões
- Testes e qualidade
- Contribuição
- Licença

## Sobre o projeto

Este projeto é um site institucional que permite cadastrar, listar e gerenciar posts com mídia (imagem ou vídeo), bem como um sistema de comentários com respostas em posts e vídeos.  
O foco é servir como plataforma de divulgação de conteúdos acadêmicos, com área administrativa protegida por autenticação.

## Funcionalidades

- Cadastro, edição e exclusão de posts com título, descrição, tema e tipo (imagem ou vídeo).  
- Upload de mídias com limite de tamanho total otimizado para hospedagens com ~100MB.  
- Listagem de conteúdos em seções (carrossel, grade de imagens, vídeos em destaque).  
- Sistema de comentários com suporte a respostas (threaded replies) em posts e vídeos.  
- Painel administrativo para gestão de posts, mídias e comentários.  
- Controle de acesso por autenticação (admin e demais usuários, conforme sua regra).  

## Tecnologias

- PHP (backend e regras de negócio).  
- MySQL/MariaDB (persistência dos dados).  
- HTML5, CSS3 e JavaScript (interface e interações dinâmicas).  
- XAMPP ou similar (Apache + PHP + MySQL em ambiente local).  
- Git e GitHub para versionamento de código.  

## Arquitetura e organização

- `public/` – arquivos acessíveis via navegador (index.php, assets, etc.).  
- `app/` – código de aplicação (controladores, modelos, serviços).  
- `views/` – templates de interface (listagem de posts, detalhes, formulários, etc.).  
- `config/` – arquivos de configuração (conexão com banco, constantes).  
- `database/` – scripts SQL para criação e alteração de tabelas.  

## Requisitos

- PHP 8.x ou superior.  
- MySQL ou MariaDB 10.x ou superior.  
- XAMPP.  
- Composer. 

## Como executar o projeto

1. Clone este repositório:
   - `git clone https://github.com/seu-usuario/seu-repo.git`
2. Coloque o projeto na pasta do servidor web: `htdocs` do XAMPP).
3. Configure o arquivo de ambiente (`config/config.php` ou `.env`) com as credenciais do banco.
4. Importe o script SQL em `logs_bd/` no MySQL/MariaDB.
5. execute o arquivo setup.bat para configuração automatica do composer e do xampp
5. Inicie o servidor (Apache + MySQL) e acesse:
   - `http://localhost/`

## Configuração do banco de dados

- Tabelas principais:
  - `users` – usuários e credenciais de acesso.  
  - `posts` – posts com título, descrição, tema, tipo (image|video) e link da mídia.  
  - `comments` – comentários com suporte a respostas (campo `parent_id`).  
- Verifique o arquivo `database/schema.sql` para a estrutura completa.  

## Estrutura de usuário e permissões

- Usuário administrador com acesso ao painel para gerenciar posts e comentários.  
- Usuários autenticados (se aplicável) podem comentar e responder a comentários.  
- Visitantes podem apenas visualizar conteúdos públicos.  

## Testes e qualidade

- Testes manuais das rotas principais (criação, edição, exclusão e visualização de posts).  
- Verificação de limites de upload e validação de formulários de comentários.  
- (Opcional) Scripts de testes automatizados em PHP para futuras evoluções.  

## Contribuição

- Faça um fork do projeto.  
- Crie uma branch com sua feature ou correção.  
- Abra um Pull Request descrevendo as alterações.  

## Licença

Este projeto está sob a licença MIT.  
Consulte o arquivo `LICENSE` para mais detalhes.
