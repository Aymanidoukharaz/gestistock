# API Overview

This document provides a general overview of the GESTISTOCK RESTful API. The API is the backbone of the GESTISTOCK system, handling all data operations and business logic.

## Base URL

The primary base URL for all API endpoints is typically:
`/api`

For example, to get a list of products, the endpoint would be `/api/products`.
The full URL depends on your server configuration (e.g., `http://localhost:8000/api`).

## Authentication

All API endpoints (except for `/auth/login`) require authentication using JSON Web Tokens (JWT).
The token must be included in the `Authorization` header of your requests as a Bearer token.

Example: `Authorization: Bearer <your_jwt_token>`

For more details, see:
*   `docs/architecture.md` (Authentication and Authorization section)
*   `api/docs/api-authentication.md` (May contain more specific details on login/token handling)

## General Response Structure

The API generally follows a standardized JSON response structure:

*   **Success:**
    ```json
    {
      "success": true,
      "message": "Descriptive success message",
      "data": { /* Requested data or result of operation */ }
    }
    ```
    For collections (lists of resources), the `data` field often contains an array of items, and there might be additional `meta` and `links` fields for pagination.

*   **Error:**
    ```json
    {
      "success": false,
      "message": "Descriptive error message",
      "errors": { /* Optional: Detailed validation errors */ }
    }
    ```

Refer to `api/docs/api-resources.md` for more examples of specific resource structures and `api/docs/api-test-plan.md` for expected responses.

## Main Resource Groups

The API is organized around several main resources:

### 1. Authentication (`/auth`)

*   Handles user login, registration (by authenticated users), token refresh, and fetching the current user's information.
*   Endpoints: `login`, `logout`, `refresh`, `user`, `register`.

### 2. Users (`/users`)

*   Management of user accounts.
*   Typically restricted to administrators.
*   Endpoints for CRUD operations on users and toggling user active status.

### 3. Products (`/products`)

*   Management of product inventory.
*   Endpoints for CRUD operations on products.
*   Includes an endpoint for fetching product categories (`products/categories`).

### 4. Categories (`/categories`)

*   Management of product categories.
*   Endpoints for CRUD operations on categories.

### 5. Suppliers (`/suppliers`)

*   Management of supplier information.
*   Endpoints for CRUD operations on suppliers.

### 6. Entry Forms (`/entry-forms`)

*   Management of forms for recording incoming stock.
*   Endpoints for CRUD operations, validation, cancellation, history, and duplicate checking.

### 7. Exit Forms (`/exit-forms`)

*   Management of forms for recording outgoing stock.
*   Endpoints for CRUD operations, validation, cancellation, history, and duplicate checking.

### 8. Stock Movements (`/stock-movements`)

*   Provides a log of all stock changes.
*   Endpoints for listing, showing, and creating (often tied to entry/exit form validation) stock movements.

### 9. Reports (`/reports`)

*   Provides aggregated data and analysis.
*   Key report endpoints:
    *   `/reports/inventory`: Current inventory status.
    *   `/reports/movements`: Historical stock movements.
    *   `/reports/valuation`: Financial valuation of stock.
    *   `/reports/turnover`: Product turnover rates.
    *   Entry form reports: `/reports/entries/by-period`, `/reports/entries/by-supplier`, `/reports/entries/by-product`.
    *   Exit form reports: `/reports/exits/by-period`, `/reports/exits/by-destination`, `/reports/exits/by-product`.
*   Detailed documentation:
    *   `docs/inventory-reports.md` (or `api/docs/inventory-reports.md`)

### 10. Dashboard (`/dashboard`)

*   Provides analytical data for the frontend dashboard.
*   Endpoints: `summary`, `recent-movements`, `category-analysis`, `stock-movement-chart`.
*   Detailed documentation: `docs/dashboard-api.md`

## Further Details

For more in-depth information on specific API resources, their request/response formats, and available parameters, please refer to:

*   The route definitions: `api/routes/api.php`
*   The API controllers: `api/app/Http/Controllers/Api/`
*   Existing detailed documentation in `docs/` and `api/docs/`.
    *   `api/docs/api-resources.md` (Details on resource structures)
    *   `api/docs/bons-entree.md` & `api/docs/bons-sortie.md` (Entry/Exit form specifics)
    *   `api/docs/stock-management.md` (Stock management logic)

This overview should help in navigating and understanding the capabilities of the GESTISTOCK API.
