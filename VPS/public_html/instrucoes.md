# Estratégia de Desenvolvimento e Deploy com Git

Este documento descreve o fluxo de trabalho recomendado para fazer alterações no site, testá-las em um ambiente seguro e publicá-las na versão final (produção) de forma organizada e segura usando Git.

## Conceitos Principais

1.  **Branch `master` (ou `main`):** Esta branch é um espelho fiel do seu site que está no ar. É a versão oficial e estável. O trabalho nunca é feito diretamente nela. O diretório `/public_html` no servidor deve sempre refletir o conteúdo desta branch.

2.  **Branches de Funcionalidade (Feature Branches):** Para cada nova alteração (um novo recurso, uma correção de bug, etc.), cria-se uma "cópia" temporária do projeto, chamada de *branch*. É nesta cópia isolada que todo o desenvolvimento acontece.

3.  **Ambiente de Teste:** Um diretório no servidor (ex: `/public_html/teste`) que é usado para visualizar as alterações de uma *feature branch* antes de mesclá-las à branch principal.

## Requisitos

*   **Git instalado na sua máquina local.**
*   **Git instalado no seu servidor/VPS.** Para verificar, acesse seu servidor via SSH e execute `git --version`.
    *   Se não estiver instalado em um sistema Ubuntu/Debian, use: `sudo apt-get update && sudo apt-get install git -y`
    *   Se não estiver instalado em um sistema CentOS/RHEL, use: `sudo yum install git -y`

## Fluxo de Trabalho Passo a Passo

### 1. Iniciar o Trabalho (Máquina Local)

Antes de começar qualquer alteração, crie uma branch específica para a tarefa.

```bash
# 1. Volte para a branch principal e garanta que ela está atualizada.
git checkout master
git pull origin master

# 2. Crie uma nova branch para sua alteração e mude para ela.
#    Use um nome descritivo. Exemplo:
git checkout -b altera-pagina-contato
```

### 2. Desenvolver (Máquina Local)

*   Abra o projeto no seu editor (Dreamweaver, VS Code, etc.).
*   Faça todas as modificações necessárias nos arquivos.
*   Salve os arquivos normalmente.

### 3. Salvar e Enviar as Alterações (Máquina Local)

Após concluir uma parte do trabalho, salve um "ponto na história" (commit) e envie sua branch para o repositório no GitHub.

```bash
# 1. Adicione os arquivos modificados para o próximo commit.
git add .

# 2. Crie um commit com uma mensagem clara e descritiva.
git commit -m "Ajusta o texto e o telefone na página de contato"

# 3. Envie a sua branch para o repositório remoto (GitHub).
git push origin altera-pagina-contato
```

### 4. Testar as Alterações (Servidor/VPS)

Configure o seu ambiente de teste para refletir o conteúdo da sua nova branch.

*   Acesse o terminal do seu servidor (via SSH).
*   Navegue até a pasta de teste (ex: `cd /public_html/teste/`).
*   Execute os seguintes comandos:

```bash
# 1. Busque todas as atualizações e novas branches do GitHub.
git fetch origin

# 2. Mude para a branch que você quer testar.
git checkout altera-pagina-contato

# 3. Garanta que a branch está com a versão mais recente.
git pull origin altera-pagina-contato
```

Agora, o endereço `seusite.com/teste` exibirá o site com as suas novas alterações.

### 5. Publicar as Alterações (GitHub e Servidor/VPS)

Quando os testes forem aprovados, é hora de mesclar as alterações na branch `master` e atualizar o site principal.

1.  **Criar um Pull Request (PR):**
    *   Vá para a página do seu repositório no GitHub.
    *   O GitHub geralmente mostrará um aviso sobre sua nova branch com um botão "Compare & pull request". Clique nele.
    *   Revise as alterações e confirme a criação do PR. Este é um pedido formal para mesclar a branch `altera-pagina-contato` na `master`.

2.  **Mesclar o Pull Request:**
    *   Dentro do Pull Request no GitHub, clique no botão "Merge pull request".

3.  **Atualizar o Site Principal (Produção):**
    *   Acesse o terminal do seu servidor novamente.
    *   Navegue até o diretório principal do site (ex: `cd /public_html/`).
    *   Puxe a versão mais recente da branch `master`, que agora contém suas alterações.

    ```bash
    git pull origin master
    ```

Pronto! Seu site principal foi atualizado de forma segura e com todo o histórico de alterações registrado.
