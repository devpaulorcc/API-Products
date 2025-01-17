# API Products

[![English](https://img.shields.io/badge/lang-English-blue)](README.md)
[![Português](https://img.shields.io/badge/lang-Português-green)](README.pt-BR.md)

> Esta API foi criada para aprender e melhorar conhecimentos em PHP.

A **API Products** é um sistema projetado como uma lista de compras, ajudando os usuários a organizarem seu carrinho. Com funcionalidades para gerenciar usuários e produtos, é ideal para praticar o desenvolvimento de APIs RESTful com PHP.

---

## Endpoints

### **Endpoints de Usuários**

| Método   | Endpoint          | Parâmetros Obrigatórios      | Descrição                |
| -------- | ----------------- | ---------------------------- | ------------------------ |
| `POST`   | `/users/register` | `name, email, password`      | Registrar um novo usuário|
| `POST`   | `/users/login`    | `email, password`            | Fazer login              |
| `PUT`    | `/users/edit`     | `user_id`                   | Editar informações do usuário |
| `GET`    | `/users/list`     | `user_id`                   | Recuperar informações do usuário |
| `DELETE` | `/users/delete`   | `email, password`           | Deletar a conta do usuário |

---

### **Endpoints de Produtos**

| Método   | Endpoint            | Parâmetros Obrigatórios                   | Descrição                       |
| -------- | ------------------- | ----------------------------------------- | ------------------------------- |
| `POST`   | `/products/register`| `user_id, name, amount, metric, value`    | Registrar um novo produto       |
| `PUT`    | `/products/edit`    | `user_id, prod_id`                       | Editar um produto               |
| `GET`    | `/products/list`    | `user_id`                                | Recuperar produtos de um usuário|
| `GET`    | `/products/total`   | `user_id`                                | Calcular o valor total dos produtos |
| `DELETE` | `/products/delete`  | `user_id, prod_id`                       | Deletar um produto              |

---

## URL Base

A API está hospedada em:  
`https://apiprodutosphp.dev.br`

---

## Autenticação

É necessário fazer login antes de consumir outros endpoints. O sistema vincula todas as ações aos produtos do usuário logado.

---

## Objetivo

Este projeto funciona como uma aplicação de **lista de compras** para:
- Ajudar os usuários a gerenciarem seus carrinhos de compras.
- Fornecer um exemplo de API RESTful criada com PHP.
