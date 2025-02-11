# 🛒 eCommerce Shopping Cart System (PHP & MySQL)

## 📌 About the Project
This project is a **simple eCommerce shopping cart system** built using **PHP (PDO) and MySQL**. Users can:
- Register/Login
- Add products to the shopping cart
- Checkout and make a payment
- View and update their profile

## 📁 Project Folder Structure
```
/ecommerce
│- index.php                  # Homepage
│- test_db.php                # Database connection test
│
├── pages
│   ├── cart.php              # Shopping cart page
│   ├── login.php             # User login page
│   ├── logout.php            # Logout functionality
│   ├── register.php          # User registration page
│   ├── profile.php           # User profile page
│
├── payments
│   ├── checkout.php          # Checkout page
│   ├── process_payment.php   # Handles payment processing
│
├── images                    # Stores product & user images
│
├── includes
│   ├── db.php                # Database connection
│
├── css
│   ├── style.css             # Main styles
│   ├── profile.css           # Profile page styles
|
```

---

## 🛠️ How to Install the Project
### **Step 1: Download the Project**
- **Option 1:** Clone using Git:
  ```sh
  git clone https://github.com/Bhagavan912212/ecommerce.git

### **Step 2: Setup the Database**
1. Open **phpMyAdmin**.
2. Create a new database named **`ecommerce`**.
3. Import the **`ecommerce.sql`** file (provided in the project).

---

### **Step 3: Configure Database Connection**
1. Open `/includes/db.php`.
2. Update the database credentials:
   ```php
   $host = 'localhost';
   $dbname = 'ecommerce';
   $username = 'root';  // Change if necessary
   $password = '';      // Change if necessary
   ```
3. Save the file.

---

### **Step 4: Start the Server**
- **If using XAMPP:** Start **Apache & MySQL**.
- **If using PHP’s built-in server:**  
  Open the terminal and run:
  ```sh
  php -S localhost:8000
  ```
- Open a browser and go to:
  ```
  http://localhost/ecommerce
  ```

---

## 🚀 Features of This Project
✅ **User Registration & Login** (With email verification)  
✅ **Shopping Cart System** (Add, remove, update items)  
✅ **Checkout Process** (Order processing & payment handling)  
✅ **User Profile Page** (Update personal details)  
✅ **Secure Database Queries** (Uses PDO)  
✅ **Mobile Responsive Design**  

## 📜 License
This project is open-source and free to use under the **Apache License**.

---

## ❓ Need Help?
If you face any issues, feel free to ask for help! 🚀  

---

Now, this **README.md** file is:
✔️ **Easy to understand**  
✔️ **Step-by-step**  
✔️ **Beginner-friendly**  

Let me know if you want any changes! 😊
