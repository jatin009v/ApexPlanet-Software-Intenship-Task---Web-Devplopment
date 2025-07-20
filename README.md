Digital Wallet System - PHP/MySQL Project

Project Overview
This is a complete digital wallet system built with PHP and MySQL for the ApexPlanet Software Internship Project. The system includes user registration, login, money transfer, QR code payment, transaction history, and profile management functionalities.

Features
User registration and authentication

Balance management

Send/receive money

QR code generation for payments

Transaction history

Responsive user interface

Secure payment processing

Technology Stack
Frontend: HTML5, CSS3, JavaScript, Bootstrap

Backend: PHP

Database: MySQL

Server: XAMPP/WAMP

QR Code Generation: PHP QR Code library

Installation Guide
Prerequisites
XAMPP/WAMP server installed

PHP 7.0 or higher

MySQL 5.6 or higher

Web browser (Chrome, Firefox recommended)


Setup Instructions
Clone or Download the Project

bash
git clone https://github.com/jatin009v/ApexPlanet-Software-Intenship-Task---Web-Devplopment.git
Database Setup

Open phpMyAdmin in your browser (http://localhost/phpmyadmin)

Create a new database named digital_wallet

Import the SQL file located at /database/digital_wallet.sql

Configuration

Open includes/config.php and update the database credentials:

php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'digital_wallet');
QR Code Setup

Create a folder named qr-codes in the project root directory

Make sure it has write permissions (chmod 755)

Start the Application

Start your XAMPP/WAMP server

Open browser and navigate to: http://localhost/digital-wallet

Project Structure

/digital-wallet/
├── assets/
│   ├── css/           # CSS stylesheets
│   ├── js/            # JavaScript files
│   └── images/        # Static images
├── includes/
│   ├── config.php     # Database configuration
│   ├── functions.php  # Core functions
│   └── auth.php       # Authentication functions
├── database/
│   └── digital_wallet.sql  # Database schema
├── qr-codes/          # Generated QR codes storage
├── index.php          # Home page
├── register.php       # User registration
├── login.php          # User login
├── dashboard.php      # User dashboard
├── send_money.php     # Money transfer
├── generate_qr.php    # QR code generation
├── scan_pay.php       # QR code payment
├── transactions.php   # Transaction history
└── profile.php        # User profile
OR download the ZIP file and extract it to your XAMPP/WAMP htdocs folder


How It Works
1. User Authentication
Registration: Users can create an account with email, phone, and password

Login: Secure session-based authentication

2. Wallet Operations
Add Money: Users can deposit funds to their wallet

Send Money: Transfer funds to other users

QR Payments: Generate/scan QR codes for instant payments

3. Transaction Flow
User authenticates

Accesses dashboard to view balance

Chooses to send money or generate QR code

System processes the transaction

Updates balances and records transaction

4. Security Features
Password hashing

CSRF protection

Input validation

Transaction rollback on failure

Usage Guide
Registration

Navigate to register.php

Fill in required details

Click "Create Account"

Sending Money

Login and go to dashboard

Click "Send Money"

Enter recipient email/phone and amount

Confirm transaction

QR Payments

Go to "Generate QR" to create your payment QR

Or go to "Scan & Pay" to scan someone else's QR

View Transactions

Click "Transaction History" to view all transactions

Troubleshooting
Common Issues
Database Connection Error

Verify database credentials in config.php

Check if MySQL service is running

QR Code Not Generating

Ensure qr-codes directory exists and is writable

Check PHP GD library is enabled

Session Problems

Clear browser cookies

Check session.save_path in php.ini

Future Enhancements
Mobile app integration

Two-factor authentication

Payment gateway integration

Admin dashboard

Transaction notifications



License
This project is licensed under the MIT License - see the LICENSE.md file for details.

Contact
For any queries regarding this project, please contact Me.
