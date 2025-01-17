# API Products

[![English](https://img.shields.io/badge/lang-English-blue)](README.md)
[![Português](https://img.shields.io/badge/lang-Português-green)](README.pt-BR.md)

> This API was created to learn about PHP and improve my skills.

The **API Products** is a system designed as a shopping list, helping users to organize their cart. With features to manage users and products, it’s ideal for practicing RESTful API development with PHP.

---

## Endpoints

### **User Endpoints**

| Method   | Endpoint         | Required Parameters          | Description                |
| -------- | ---------------- | ---------------------------- | -------------------------- |
| `POST`   | `/users/register`| `name, email, password`      | Register a new user        |
| `POST`   | `/users/login`   | `email, password`            | Log in a user              |
| `PUT`    | `/users/edit`    | `user_id`                   | Edit user information      |
| `GET`    | `/users/list`    | `user_id`                   | Retrieve user information  |
| `DELETE` | `/users/delete`  | `email, password`           | Delete a user account      |

---

### **Product Endpoints**

| Method   | Endpoint            | Required Parameters                   | Description                       |
| -------- | ------------------- | ------------------------------------- | --------------------------------- |
| `POST`   | `/products/register`| `user_id, name, amount, metric, value`| Register a new product            |
| `PUT`    | `/products/edit`    | `user_id, prod_id`                   | Edit a product                    |
| `GET`    | `/products/list`    | `user_id`                            | Retrieve products for a user      |
| `GET`    | `/products/total`   | `user_id`                            | Calculate total value of products |
| `DELETE` | `/products/delete`  | `user_id, prod_id`                   | Delete a product                  |

---

## Base URL

The API is hosted at:  
`https://apiprodutosphp.dev.br`

---

## Authentication

It is necessary to log in before consuming other endpoints. The system links all actions to the logged-in user and their products.

---

## Objective

This project serves as a **shopping list** application to:
- Help users manage their shopping carts.
- Provide an example of a RESTful API built with PHP.

---

[🔄 Translate this README to Portuguese](https://translate.google.com/translate?sl=en&tl=pt&u=https://github.com/devpaulorcc/API-Products)