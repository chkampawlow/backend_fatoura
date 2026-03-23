# 🖥️ Factorator API

<p align="center">
  <img src="https://img.shields.io/badge/PHP-7.4+-777BB4?logo=php" />
  <img src="https://img.shields.io/badge/MySQL-Database-4479A1?logo=mysql" />
  <img src="https://img.shields.io/badge/Auth-JWT-black" />
  <img src="https://img.shields.io/badge/Security-Static%20Token-red" />
  <img src="https://img.shields.io/badge/Status-Active-success" />
</p>

---

## 🚀 Overview

Factorator API is a lightweight and secure backend built with **PHP + MySQL** for managing:

- 🧾 Invoices  
- 🧑 Clients  
- 📦 Products  
- 👤 User profiles  

---

## ✨ Features

- 🔐 JWT Authentication (access + refresh tokens)
- 🔑 Static API Token protection
- 🧾 Invoice CRUD (create / fetch / cancel)
- 🧑 Client management (company & individual)
- 📦 Product management
- ⚡ Fast & framework-free backend
- 🌐 Cloudflare tunnel support (for remote testing)

---

## 📁 Project Structure

```text
php-api/
├── auth/
├── clients/
├── products/
├── invoices/
├── invoice_items/
├── user/
├── config/
│   ├── db.php
│   ├── env.php
│   └── response.php
├── static_token.php
└── .env
