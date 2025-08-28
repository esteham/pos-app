# 🛒 POS Application (Laravel 10 + React + Sanctum Breeze)

A modern **Point of Sale (POS) System** built with **Laravel 10** for the backend API and **React** for the frontend.  
Authentication and session handling are managed securely with **Laravel Sanctum** and **Breeze**.  

---

## 🚀 Features
- 🔑 Authentication with Sanctum & Breeze (Login, Register, Logout)
- 📦 Product & Inventory Management
- 🛍️ Sales & Billing System
- 👥 User & Role Management (Admin / Cashier)
- 📊 Dashboard with sales overview
- 🔗 RESTful API for frontend consumption
- ⚡ React-based modern UI

---

## 🛠️ Tech Stack
**Backend (API):**
- Laravel 10  
- Sanctum (API Authentication)  
- Breeze (Starter Auth Scaffolding)  
- MySQL / PostgreSQL (Database)  

**Frontend (UI):**
- React  
- Axios (API Requests)  
- Tailwind CSS (Styling)  

---

## 📦 Installation & Setup

### 1️⃣ Clone Repository
```bash
git clone https://github.com/esteham/pos-api.git
cd pos-api
```
2️⃣ Backend Setup (Laravel API)
```bash
cd backend   # if backend folder exists, otherwise stay in root
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve
```
Backend will be available at: http://127.0.0.1:8000
3️⃣ Frontend Setup (React)
```bash
cd frontend
npm install
npm run dev
```
Frontend will be available at: 
```bash
http://localhost:5173
```
🔑 Authentication

    Uses Laravel Sanctum for SPA authentication

    Breeze provides scaffolding for Login, Register, Forgot Password

    Token-based session handling

📡 API Endpoints (Sample)
Method	Endpoint	Description
POST	/api/login	User login
POST	/api/register	New user registration
GET	/api/products	Fetch all products
POST	/api/sales	Create new sale
🤝 Contributing

Pull requests are welcome! For major changes, please open an issue first to discuss what you’d like to change.
📜 License

MIT License
