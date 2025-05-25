# Architecture Overview

This document provides a high-level overview of the GESTISTOCK system architecture. GESTISTOCK is a stock management system designed to track inventory, stock movements, and product entries/exits.

## Core Components

The system is primarily composed of two main applications:

1.  **Backend API:** A RESTful API built with PHP and the Laravel framework. It handles all business logic, data storage, and authentication.
2.  **Frontend Application:** A single-page application (SPA) built with TypeScript and Next.js. It provides the user interface for interacting with the system.

## Technology Stack

### Backend (API)

*   **Framework:** Laravel 10
*   **Language:** PHP 8.1+
*   **Database:** MySQL or MariaDB (as indicated by Laravel's default configuration and common usage)
*   **API Type:** RESTful
*   **Authentication:** JSON Web Tokens (JWT)
*   **Key Directories:**
    *   `api/app/Http/Controllers/Api/`: Contains controllers that handle API requests.
    *   `api/app/Models/`: Defines the Eloquent ORM models for database interaction.
    *   `api/routes/api.php`: Defines all API endpoints.
    *   `api/database/migrations/`: Contains database schema migrations.

### Frontend

*   **Framework:** Next.js (React framework)
*   **Language:** TypeScript
*   **HTTP Client:** Axios (for making API requests)
*   **UI Components:** Custom components, likely leveraging a UI library for base elements (e.g., Shadcn/ui, judging by `frontend/components/ui/` structure).
*   **State Management:** React Context API and component state (common in Next.js, further investigation of `frontend/hooks` or `frontend/store` would be needed for more complex global state management if present).
*   **Key Directories:**
    *   `frontend/app/`: Contains page components (route-based).
    *   `frontend/components/`: Contains reusable UI components, organized by feature and a general `ui` sub-directory.
    *   `frontend/services/`: Contains services that encapsulate API call logic for different resources.
    *   `frontend/lib/api.ts`: Configures the Axios instance used for all API communication, including request/response interceptors.
    *   `frontend/hooks/`: Contains custom React hooks for reusable logic.

## Project Structure

The monorepo is organized as follows:

*   **`api/`**: Contains the Laravel backend application.
*   **`frontend/`**: Contains the Next.js frontend application.
*   **`docs/`**: Contains general project documentation, including this architecture overview.
    *   `api/docs/`: Contains more specific API documentation (may have some overlap or older files).
*   **`README.md`**: Root README providing project description and setup instructions.

## Data Flow

1.  **User Interaction:** The user interacts with the frontend application through their web browser.
2.  **Frontend Request:** When data is needed or an action is performed (e.g., submitting a form), the relevant frontend page/component calls a function in one of the `frontend/services/*` files.
3.  **API Call:** The service function uses the configured Axios instance (`frontend/lib/api.ts`) to make an HTTP request to the appropriate backend API endpoint.
    *   The Axios request interceptor automatically attaches the JWT token to the request headers if the user is authenticated.
4.  **Backend Processing:** The Laravel backend receives the request.
    *   Routes in `api/routes/api.php` direct the request to the relevant controller in `api/app/Http/Controllers/Api/`.
    *   The controller processes the request, interacting with models (`api/app/Models/`) for database operations (CRUD) and other services/business logic.
5.  **Backend Response:** The controller returns a JSON response.
6.  **Frontend Response Handling:** The frontend service receives the API response.
    *   The Axios response interceptor handles global error cases (like 401 for logout).
    *   The service then processes the successful response and returns data to the calling component.
7.  **UI Update:** The frontend component updates its state with the new data, re-rendering the UI to display the information to the user.

## Authentication and Authorization

*   **Authentication:**
    *   Managed by the backend API using JWT.
    *   Users log in via the `/api/auth/login` endpoint, receiving a JWT.
    *   This token is stored in the frontend's `localStorage` and sent with subsequent API requests in the `Authorization` header.
    *   The `frontend/lib/api.ts` handles token attachment and automatic logout on 401 errors.
*   **Authorization:**
    *   The backend API uses a role-based access control (RBAC) system.
    *   Key roles identified are `admin` and `magasinier`.
    *   API routes are protected using middleware (`role:admin`, `role:admin,magasinier`) defined in `api/routes/api.php`.
    *   This ensures that users can only access resources and perform actions appropriate for their roles.
    *   (Refer to `api/docs/authorization-system.md` and `api/app/Http/Middleware/CheckRole.php` for more details on the backend implementation).

## Database

*   The backend uses an SQL database, typically MySQL or MariaDB with Laravel.
*   Database schema is managed through Laravel migrations located in `api/database/migrations/`.
*   Eloquent ORM models in `api/app/Models/` are used for data access and defining relationships between tables.

---

This overview should provide a good starting point for understanding the GESTISTOCK system. For more detailed information on specific parts, refer to the respective directories and documentation files.
