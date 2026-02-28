# 💻 API ENDPOINTS

This document lists all API endpoints for the Blood Donation Management System.

- **Local URL(Developement):** `http://127.0.0.1:8000/api/v1`

---

## 👮 Authentication APIs

| Method | Endpoint    | Description                |
| ------ | ----------- | -------------------------- |
| POST   | `/login`    | Login and get access token |
| POST   | `/register` | Register a new user        |
| POST   | `/logout`   | Logout current user        |

---

## 👨 User APIs

| Method | Endpoint                 | Description      |
| ------ | ------------------------ | ---------------- |
| GET    | `/users`                 | List all users   |
| GET    | `/users/{id}`            | Show user detail |
| POST   | `/users`                 | New create user  |
| PUT    | `/users/{id}`            | Update user      |
| DELETE | `/users/{id}`            | Delete user      |
| PATCH  | `/users/{id}/deactivate` | Deactivate user  |
| PATCH  | `/users/{id}/activate`   | Activate user    |

---

## 👨‍🦱 Donor APIs

| Method | Endpoint                  | Description       |
| ------ | ------------------------- | ----------------- |
| GET    | `/donors`                 | List all donors   |
| GET    | `/donors/{id}`            | Show donor detail |
| POST   | `/donors`                 | New create donor  |
| PUT    | `/donors/{id}`            | Update donor      |
| DELETE | `/donors/{id}`            | Delete donor      |
| PATCH  | `/donors/{id}/deactivate` | Deactivate donor  |
| PATCH  | `/donors/{id}/activate`   | Activate donor    |

---
