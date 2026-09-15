# Web Technologies — Summer 2025-26, Section AA

Coursework repository for **CSC 3215: Web Technologies**, American International University-Bangladesh (AIUB).

**Project Title:** RestoFlow (CraveCart) - A Comprehensive Food Ordering and Delivery Management System
**Group:** 10

---

## 📌 Project Overview
RestoFlow is a dynamic, MVC-based web application that facilitates seamless food ordering, restaurant inventory management, and delivery tracking. It connects four distinct types of users—Customers, Restaurant Managers, Delivery Riders, and System Admins—into a single, unified platform. 

The system features real-time data handling via Ajax/JSON, robust PHP/JS validations, and strict role-based access control.

---

## 🗂️ Repository Layout

This project strictly follows the **MVC (Model-View-Controller)** architectural pattern. 

```text
├── assets/
│   ├── style.css           Unified stylesheet for the application UI
│   └── uploads/            Directory for product images and static assets
├── controllers/            Request handling, Ajax/JSON endpoints, and application flow
├── models/                 Data access and business rules (Database.php, User.php)
├── views/                  Presentation templates (login.php, register.php, dashboards)
├── cravecart_db.sql        MySQL database schema and dummy data dump
└── index.php               Front controller and application entry point
