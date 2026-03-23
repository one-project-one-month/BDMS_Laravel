# 💻 API ENDPOINTS

This document lists all API endpoints for the Blood Donation Management System.

- **Local URL(Developement):** `http://127.0.0.1:8000/api/v1`

---
# Admin / Staff

## 👮 Authentication APIs

| Method | Endpoint    | Description                | Note |
| ------ | ----------- | -------------------------- | ---- |
| POST   | `/login`    | Login and get access token |      |
| POST   | `/register` | Register a new user        |      |
| POST   | `/logout`   | Logout current user        |      |

---

## 👨 User APIs

| Method | Endpoint                   | Description               | Note       |
| ------ | -------------------------- | ------------------------- | ---------- |
| GET    | `/users`                   | List all users            |            |
| GET    | `/users/{id}`              | Show user detail          |            |
| POST   | `/users`                   | New create user           |            |
| PUT    | `/users/{id}`              | Update user               |            |
| DELETE | `/users/{id}`              | Delete user               |            |
| PATCH  | `/users/{id}/deactivate`   | Deactivate user           |            |
| PATCH  | `/users/{id}/activate`     | Activate user             |            |
| GET    | `/users/trashed`           | List all soft delete user | Admin Only |
| GET    | `/users/{id}/restore`      | Restore soft delete user  | Admin Only |
| GET    | `/users/{id}/force-delete` | Force Delete user         | Admin Only |

---

## 👨‍🦱 Donor APIs

| Method | Endpoint                    | Description              | Note       |
| ------ | --------------------------- | ------------------------ | ---------- |
| GET    | `/donors`                   | List all donors          |            |
| GET    | `/donors/{id}`              | Show donor detail        |            |
| POST   | `/donors`                   | New create donor         |            |
| PUT    | `/donors/{id}`              | Update donor             |            |
| DELETE | `/donors/{id}`              | Delete donor             |            |
| PATCH  | `/donors/{id}/deactivate`   | Deactivate donor         |            |
| PATCH  | `/donors/{id}/activate`     | Activate donor           |            |
| GET    | `/donors/{id}/restore`      | Restore soft delete user | Admin Only |
| GET    | `/donors/{id}/force-delete` | Force Delete user        | Admin Only |

---

## 👨‍🦱 Donation APIs

| Method | Endpoint          | Description          | Note |
| ------ | ----------------- | -------------------- | ---- |
| GET    | `/donations`      | List all donations   |      |
| GET    | `/donations/{id}` | Show donation detail |      |
| POST   | `/donations`      | New create donation  |      |
| PUT    | `/donations/{id}` | Update donation      |      |
| DELETE | `/donations/{id}` | Delete donation      |      |
| PATCH  | `/donations/{id}` | Change status        |      |

---

## 🩸 Blood Requests APIs

| Method | Endpoint               | Description               | Note |
| ------ | ---------------------- | ------------------------- | ---- |
| GET    | `/blood-requests`      | List all blood-requests   |      |
| GET    | `/blood-requests/{id}` | Show blood-request detail |      |
| POST   | `/blood-requests`      | New create blood-request  |      |
| PUT    | `/blood-requests/{id}` | Update blood-request      |      |
| DELETE | `/blood-requests/{id}` | Delete blood-request      |      |
| PATCH  | `/blood-requests/{id}` | Change status             |      |

---

## 📝 Appointments APIs

| Method | Endpoint             | Description              | Note |
| ------ | -------------------- | ------------------------ | ---- |
| GET    | `/appointments`      | List all appointments    |      |
| GET    | `/appointments/{id}` | Show appointments detail |      |
| POST   | `/appointments`      | New create appointments  |      |
| PUT    | `/appointments/{id}` | Update appointments      |      |
| DELETE | `/appointments/{id}` | Delete appointments      |      |
| PATCH  | `/appointments/{id}` | Change status            |      |

---

## 📊 Medical Records APIs

| Method | Endpoint                | Description                | Note |
| ------ | ----------------------- | -------------------------- | ---- |
| GET    | `/medical-records`      | List all medical-records   |      |
| GET    | `/medical-records/{id}` | Show medical-record detail |      |
| POST   | `/medical-records`      | New create medical-record  |      |
| PUT    | `/medical-records/{id}` | Update medical-record      |      |
| DELETE | `/medical-records/{id}` | Delete medical-record      |      |
| PATCH  | `/appointments/{id}`    | Change status              |      |

---

## 📥 Blood Inventories APIs

| Method | Endpoint                  | Description                 | Note |
| ------ | ------------------------- | --------------------------- | ---- |
| GET    | `/blood-inventories`      | List all blood-inventories  |      |
| GET    | `/blood-inventories/{id}` | Show blood-inventory detail |      |
| PUT    | `/blood-inventories/{id}` | Update blood-inventory      |      |

---

## 📢 Announcements APIs

| Method | Endpoint                         | Description              | Note                 |
| ------ | -------------------------------- | ------------------------ | -------------------- |
| GET    | `/announcements`                 | List all announcements   | admin / staff / user |
| GET    | `/announcements/{id}`            | Show announcement detail | admin / staff / user |
| POST   | `/announcements`                 | New create announcement  |                      |
| PUT    | `/announcements/{id}`            | Update announcement      |                      |
| DELETE | `/announcements/{id}`            | Delete announcement      |                      |
| PATCH  | `/announcements/{id}/deactivate` | Deactivate announcement  |                      |
| PATCH  | `/announcements/{id}/activate`   | Activate announcement    |                      |

---

## 📄 Certificates APIs

| Method | Endpoint             | Description             | Note |
| ------ | -------------------- | ----------------------- | ---- |
| GET    | `/certificates`      | List all certificates   |      |
| GET    | `/certificates/{id}` | Show certificate detail |      |
| POST   | `/certificates`      | New create certificate  |      |
| PUT    | `/certificates/{id}` | Update certificate      |      |
| DELETE | `/certificates/{id}` | Delete certificate      |      |

---

# User API Endpoints

## ⛺ Home APIs

| Method | Endpoint | Description             | Note |
| ------ | -------- | ----------------------- | ---- |
| GET    | `/home`  | Get user home page data |      |

## 🙍‍♂️ Profiles APIs

| Method | Endpoint                 | Description         | Note |
| ------ | ------------------------ | ------------------- | ---- |
| GET    | `/users/{userId}`        | Get user profiles   |      |
| PUT    | `/users/{userId}`        | Update user profile |      |
| GET    | `/users/{userId}/donors` | GET donor detail    |      |
| POST   | `/users/{userId}/donors` | Create donor        |      |

---

## 🎉 Donations APIs

| Method | Endpoint                          | Description              | Note |
| ------ | --------------------------------- | ------------------------ | ---- |
| GET    | `/{userId}/donations`             | Get user all donations   |      |
| POST   | `/{userId}/donations`             | Create donation          |      |
| GET    | `/{userId}/donations/{id}`        | Get user donation Detail |      |
| PATCH  | `/{userId}/donations/{id}/cancel` | Cancel donation          |      |

---

## 🧾 Blood Request APIs

| Method | Endpoint                        | Description                   | Note |
| ------ | ------------------------------- | ----------------------------- | ---- |
| GET    | `/{userId}/blood-requests`      | Get user all blood request    |      |
| POST   | `/{userId}/blood-requests`      | Create blood request          |      |
| GET    | `/{userId}/blood-requests/{id}` | Get user blood request Detail |      |
| PATCH  | `/{userId}/blood-requests/{id}` | Cancel blood request          |      |

---

## 📝 Apponitments APIs

| Method | Endpoint                             | Description                 | Note |
| ------ | ------------------------------------ | --------------------------- | ---- |
| GET    | `/{userId}/appointments`             | Get user all appointments   |      |
| GET    | `/{userId}/appointments/{id}`        | Get user appointment Detail |      |
| PATCH  | `/{userId}/appointments/{id}/cancel` | Cancel appointment          |      |

---

## 📤 Blood Inventories APIs

| Method | Endpoint                  | Description                 | Note |
| ------ | ------------------------- | --------------------------- | ---- |
| GET    | `/blood-inventories/`     | List all blood-inventories  |      |
| GET    | `/blood-inventories/{id}` | Show blood-inventory detail |      |
| PUT    | `/blood-inventories/{id}` | Update blood-inventory      |      |

---

## 🧾 Certificates APIs

| Method | Endpoint                     | Description               | Note |
| ------ | ---------------------------- | ------------------------- | ---- |
| GET    | `{userId}/certificates`      | Get user all certificates |      |
| GET    | `{userId}/certificates/{id}` | Get certificate detail    |      |

---
