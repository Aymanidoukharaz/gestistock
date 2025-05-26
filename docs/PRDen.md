# Product Requirements Document: GESTISTOCK (Revamped)

**Author:** Ayman Idoukharaz (End of Studies Project - Bachelor's Degree in IT), with AI Assistant
**Version:** 1.0
**Date:** 2024-07-29

## 1. Introduction

### 1.1. Project Vision
GESTISTOCK aims to be a comprehensive and user-friendly inventory management system designed to help businesses efficiently track and manage their stock, streamline operations, and make informed decisions regarding their inventory.

### 1.2. Purpose of this Document
This document outlines the product requirements for the GESTISTOCK application. It details the features, functionalities, and user experience of the system as it currently exists, based on a thorough analysis of the project repository. It serves as a central reference for understanding the application's capabilities and is intended to be suitable for an end-of-studies project presentation.

### 1.3. Document Conventions
*   **User Roles:**
    *   `admin`: Administrator with full system access.
    *   `magasinier`: Stock Manager/Warehouse Clerk with operational access.
*   References to detailed technical documentation (architecture, data models, API, frontend) are provided in the Appendix.

## 2. Goals and Objectives

### 2.1. Business Goals (for a hypothetical user company)
*   Improve inventory accuracy and reduce discrepancies.
*   Increase efficiency in stock handling processes (entries, exits, tracking).
*   Provide clear visibility into stock levels and movements to prevent stockouts or overstocking.
*   Enable better decision-making through accessible dashboard analytics.
*   Streamline supplier and product management.

### 2.2. User Goals
*   **Administrators:**
    *   To have full control over system configuration, user management, and core data (products, categories, suppliers).
    *   To monitor overall system activity and health.
    *   To ensure data integrity and security.
*   **Stock Managers (`magasinier`):**
    *   To easily record incoming and outgoing stock.
    *   To quickly find product information and current stock levels.
    *   To access operational data to understand stock flows.
    *   To efficiently manage daily stock operations.

### 2.3. Project Goals (for this version of GESTISTOCK)
*   Deliver a functional web application covering core stock management features.
*   Provide a clear and intuitive user interface for both admin and stock manager roles.
*   Implement robust backend API services to support all frontend functionalities.
*   Ensure secure authentication and role-based authorization.
*   Demonstrate proficiency in using modern web technologies (Laravel, Next.js) for an end-of-studies project.

## 3. Target Audience

### 3.1. Primary User Roles

#### 3.1.1. Administrator (`admin`)
*   **Description:** Responsible for overall system management, user accounts, master data setup, and has access to all system functionalities. Typically, this user would be an IT manager, a business owner, or a senior operations manager.
*   **Technical Proficiency:** Medium to High.

#### 3.1.2. Stock Manager / Warehouse Clerk (`magasinier`)
*   **Description:** Responsible for day-to-day stock operations, including recording goods receipts (entries), goods issues (exits), tracking stock movements, and viewing operational data.
*   **Technical Proficiency:** Low to Medium.

### 3.2. Needs and Pain Points Addressed
*   **Administrator:**
    *   **Need:** Centralized control and visibility. **Pain Point Addressed:** Manages users, configures categories/suppliers, and oversees all data without needing separate systems.
    *   **Need:** Data security and integrity. **Pain Point Addressed:** Role-based access ensures data is only modified by authorized personnel.
*   **Stock Manager (`magasinier`):**
    *   **Need:** Simple and efficient data entry for stock movements. **Pain Point Addressed:** Streamlined forms for entry and exit operations.
    *   **Need:** Quick access to accurate stock information. **Pain Point Addressed:** Easy lookup of product quantities, low stock alerts (via dashboard).
    *   **Need:** Traceability of stock. **Pain Point Addressed:** History of stock movements and form processing.

## 4. Product Overview

### 4.1. System Architecture Summary
GESTISTOCK is a web-based application composed of:
*   A **Backend API** built with Laravel, handling business logic, database interactions, and API endpoints.
*   A **Frontend Single Page Application (SPA)** built with Next.js and TypeScript, providing the user interface.
(For a detailed technical breakdown, refer to `docs/architecture.md`.)

### 4.2. Core Functionality at a Glance
The system provides functionalities for:
*   User Authentication and Role-Based Access
*   Dashboard with Key Metrics
*   Management of Products, Categories, and Suppliers
*   Tracking Stock Entries (Entry Forms) and Exits (Exit Forms)
*   Viewing Stock Levels and Movement History
*   User Management (for Administrators)
*   Generating various Inventory and Operational Data

## 5. Key Features (Detailed)

### 5.1. Authentication & User Session Management
*   **5.1.1. User Login:** Users can log in using their email and password. Successful login grants a JWT token for session management.
*   **5.1.2. User Logout:** Authenticated users can log out, invalidating their session.
*   **5.1.3. View Current User Information:** Authenticated users can see their basic information (name, role).
*   **5.1.4. JWT Token Handling:** Tokens are automatically attached to API requests and managed for session persistence (via `localStorage`). Automatic logout occurs on 401 errors.

### 5.2. Dashboard
*   **5.2.1. Key Performance Indicators (KPIs) Summary:** Displays an overview of important metrics (e.g., total products, low stock alerts, total stock value - specific KPIs depend on `DashboardController@summary`).
*   **5.2.2. Recent Stock Movements Display:** Shows a list of recent entry and exit activities.
*   **5.2.3. Product Category Analysis:** Visual representation (e.g., chart) of product distribution by category.
*   **5.2.4. Stock Movement Trends:** Visual representation (e.g., chart) of stock entries and exits over a period.

### 5.3. Product Management
*   **5.3.1. View Products:** Users can view a paginated list of all products. Functionality includes searching and filtering (details of filters depend on `ProductController@index` implementation).
*   **5.3.2. Create New Product (`admin` only):** Admins can add new products, specifying reference, name, description, category, price, initial quantity, and minimum stock level.
*   **5.3.3. Edit Existing Product (`admin` only):** Admins can modify the details of existing products.
*   **5.3.4. Delete Product (`admin` only):** Admins can remove products from the system.

### 5.4. Category Management
*   **5.4.1. View Categories:** Users can view a list of all product categories.
*   **5.4.2. Create New Category (`admin` only):** Admins can add new categories.
*   **5.4.3. Edit Existing Category (`admin` only):** Admins can modify existing categories.
*   **5.4.4. Delete Category (`admin` only):** Admins can remove categories.

### 5.5. Supplier Management
*   **5.5.1. View Suppliers:** Users can view a list of all suppliers.
*   **5.5.2. Create New Supplier (`admin` only):** Admins can add new suppliers with contact details and notes.
*   **5.5.3. Edit Existing Supplier (`admin` only):** Admins can modify supplier information.
*   **5.5.4. Delete Supplier (`admin` only):** Admins can remove suppliers.

### 5.6. Inventory & Stock Management
*   **5.6.1. View Product Stock Levels:** Users can view the current quantity and minimum stock level for each product, typically on product lists or detail views.
*   **5.6.2. View Stock Movement History:** Users can view a log of all stock movements, filterable by product, date range, and type (entry/exit).

### 5.7. Entry Form (Goods Receipt) Management
*   **5.7.1. View Entry Forms:** Users can view a list of entry forms, filterable by status, date, supplier, etc.
*   **5.7.2. Create New Entry Form (`admin`, `magasinier`):** Users can create new entry forms, specifying reference, date, supplier (optional), notes, and adding multiple items (product, quantity, unit price).
*   **5.7.3. Add/Edit/Remove Items from Draft Entry Form:** Functionality to manage items before validation.
*   **5.7.4. Validate Entry Form (`admin`, `magasinier`):** Validating an entry form updates the stock levels of the associated products and records stock movements.
*   **5.7.5. Cancel Entry Form (`admin` only):** Admins can cancel entry forms. If the form was already validated, stock levels are adjusted accordingly.
*   **5.7.6. View Entry Form Details & History:** Users can view the details of a specific entry form, including its items and a history of changes made to the form.
*   **5.7.7. Check for Duplicate Entry Forms:** System assists in identifying potential duplicate entry forms during creation.

### 5.8. Exit Form (Goods Issue) Management
*   **5.8.1. View Exit Forms:** Users can view a list of exit forms, filterable by status, date, destination, etc.
*   **5.8.2. Create New Exit Form (`admin`, `magasinier`):** Users can create new exit forms, specifying reference, date, destination, reason, notes, and adding multiple items (product, quantity).
*   **5.8.3. Add/Edit/Remove Items from Draft Exit Form:** Functionality to manage items before validation.
*   **5.8.4. Validate Exit Form (`admin`, `magasinier`):** Validating an exit form updates the stock levels of the associated products (checking for availability first) and records stock movements.
*   **5.8.5. Cancel Exit Form (`admin` only):** Admins can cancel exit forms. If already validated, stock levels are adjusted.
*   **5.8.6. View Exit Form Details & History:** Users can view the details of a specific exit form, its items, and a history of changes.
*   **5.8.7. Check for Duplicate Exit Forms:** System assists in identifying potential duplicate exit forms.

### 5.9. User Management (Administrator Only)
*   **5.9.1. View Users:** Admins can view a list of all users, their roles, and active status.
*   **5.9.2. Create New User:** Admins can register new users, assigning them a role (`admin` or `magasinier`).
*   **5.9.3. Edit User Details:** Admins can update a user's name, email, and role.
*   **5.9.4. Activate/Deactivate User Account:** Admins can toggle the active status of user accounts.


## 6. User Roles and Permissions Summary

*   **6.1. Administrator (`admin`):**
    *   Full CRUD access to all data entities: Products, Categories, Suppliers, Entry Forms, Exit Forms, Stock Movements, Users.
    *   Can validate and cancel both Entry and Exit Forms.
    *   Can manage user accounts and roles.
    *   Access to all dashboard features.
*   **6.2. Stock Manager (`magasinier`):**
    *   Read access to Products, Categories, Suppliers, Entry Forms, Exit Forms, Stock Movements.
    *   Can create Entry Forms and Exit Forms.
    *   Can validate Entry Forms and Exit Forms.
    *   Can create Stock Movements (typically as a result of form validation).
    *   Access to all dashboard features.
    *   No access to User Management. Cannot create, edit, or delete Categories or Suppliers. Cannot cancel validated forms.

## 7. Design and UX Considerations

*   **7.1. Responsive Design:** The application is designed to be usable across various screen sizes, including desktop and mobile devices.
*   **7.2. Intuitive Navigation:** A clear sidebar and header facilitate easy navigation between different modules.
*   **7.3. Clear Feedback Mechanisms:** User actions are acknowledged through toast notifications (for success or errors) and visual loading states.
*   **7.4. Data Visualization:** Charts are used in the Dashboard to provide quick insights into data.
*   **7.5. Light/Dark Theme Support:** Users can switch between light and dark themes for visual comfort.

## 8. Technical Requirements Overview

*   **8.1. Backend:** Developed using Laravel 10+ PHP framework.
*   **8.2. Frontend:** Developed as a Single Page Application (SPA) using Next.js (React) and TypeScript.
*   **8.3. Database:** Utilizes a relational database (MySQL compatible, as per Laravel standard).
*   **8.4. Authentication:** Secured using JSON Web Tokens (JWT).
(For more details, see `docs/architecture.md`, `docs/api-overview.md`, `docs/frontend-architecture.md`)

## 9. Success Metrics (Examples for GESTISTOCK)
*   **9.1. Feature Completeness:** All features listed in this PRD are implemented and functional.
*   **9.2. User Role Adherence:** Permissions for `admin` and `magasinier` roles are correctly enforced throughout the application.
*   **9.3. Data Integrity:** Stock levels are accurately updated upon validation of entry and exit forms. Form history is correctly logged.
*   **9.4. System Stability:** Application runs without frequent crashes or major bugs under normal usage patterns.
*   **9.5. Usability (Qualitative):** The application is perceived as easy to learn and use by target users (admin, magasinier). (For a student project, this could be demonstrated via user testing with a small group).

## 10. Future Considerations / Potential Roadmap (Examples)
*   **10.1. Advanced Data Analysis:** More complex filtering, custom data views, export to CSV/PDF.
*   **10.2. Barcode/QR Code Integration:** For faster product identification and stock operations.
*   **10.3. Multi-Language Support:** Internationalization beyond the current French interface.
*   **10.4. User Profile Management:** Allow authenticated users to change their own password or profile details.
*   **10.5. Enhanced Audit Logs:** More granular tracking of all system actions for security and traceability.
*   **10.6. Batch Operations:** Ability to update or manage multiple products, forms, etc., simultaneously.
*   **10.7. Purchase Order Integration:** Link entry forms to purchase orders.
*   **10.8. Low Stock Notifications:** Proactive email or in-app alerts for products nearing minimum stock levels.

## 11. Appendix

*   **11.1. System Architecture:** `docs/architecture.md`
*   **11.2. Data Models (Database Schema):** `docs/data-models.md`
*   **11.3. API Overview:** `docs/api-overview.md`
*   **11.4. Frontend Architecture:** `docs/frontend-architecture.md`

---
This document reflects the current understanding of the GESTISTOCK application based on repository analysis.
