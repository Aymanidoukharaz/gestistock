# Frontend Architecture

This document describes the architecture of the GESTISTOCK frontend application, located in the `frontend/` directory.

## 1. Overview

The frontend is a single-page application (SPA) built using Next.js (a React framework) and TypeScript. It serves as the user interface for the GESTISTOCK system, allowing users to interact with the backend API to manage products, stock, entries, exits, and other system entities.

## 2. Technology Stack

*   **Framework:** Next.js (App Router)
*   **Language:** TypeScript
*   **UI Library:** React
*   **HTTP Client:** Axios (for API communication)
*   **Styling:** Tailwind CSS (utility-first CSS framework)
*   **UI Components:** Likely Shadcn/ui or a similar library for pre-built components (based on `frontend/components/ui/` structure and `frontend/lib/utils.ts` for `cn` function).
*   **State Management:**
    *   **Global:** React Context API for authentication (`useAuth`) and application data (`useApiData`).
    *   **Local:** Standard React component state (`useState`, `useReducer`).
*   **Notifications:** Custom toast system (`useToast`) with UI rendered by `sonner`.
*   **Theming:** Light/Dark mode supported via `next-themes`.
*   **Linting/Formatting:** Likely ESLint and Prettier (standard for Next.js/TypeScript projects).

## 3. Project Structure (`frontend/`)

Key directories and their purposes:

*   **`app/`**: Contains all routes and pages using the Next.js App Router convention.
    *   Each subdirectory (e.g., `app/products/`) represents a route segment.
    *   `page.tsx` inside these directories defines the UI for that route.
    *   `layout.tsx` (e.g., `app/layout.tsx`) defines layouts for pages. The root layout initializes global providers.
*   **`components/`**: Reusable React components.
    *   `components/layout/`: Global layout components like `DashboardLayout`, `Header`, `Sidebar`.
    *   `components/ui/`: Generic UI primitive components (buttons, cards, dialogs, etc.), characteristic of libraries like Shadcn/ui.
    *   Feature-specific subdirectories (e.g., `components/products/`, `components/dashboard/`): Components tailored for specific features/pages.
*   **`hooks/`**: Custom React hooks for reusable logic and state management.
    *   `use-auth.tsx`: Manages user authentication state and logic (login, logout, current user).
    *   `use-api-data.tsx`: Manages fetching, storing, and refreshing application data from the backend API.
    *   `use-toast.ts`: Provides functions for triggering toast notifications.
    *   `use-mobile.tsx`: (Assumed) Hook for detecting mobile screen sizes.
*   **`lib/`**: Utility functions and core library configurations.
    *   `api.ts`: Configures the global Axios instance for API calls, including request (auth token) and response (error handling, 401 redirects) interceptors.
    *   `utils.ts`: Contains utility functions, notably `cn` for merging Tailwind CSS classes.
*   **`providers.tsx`**: A client component that groups and provides global contexts (Theme, Auth, ApiData) to the application. Used in the root `app/layout.tsx`.
*   **`services/`**: Contains service files that abstract API communication for different backend resources (e.g., `product-service.ts`, `auth-service.ts`). These services use the Axios instance from `lib/api.ts`.
*   **`public/`**: Static assets accessible directly via URL (images, fonts not handled by Next/Font).
*   **`styles/`**: Global styles, though most styling is likely handled by Tailwind CSS utility classes directly in components.
    *   `globals.css`: Main global stylesheet, typically for Tailwind base styles and custom global styles.
*   **`types/`**: TypeScript type definitions for data structures used across the application (e.g., `Product`, `User`).
*   **`tailwind.config.ts`**: Configuration file for Tailwind CSS.
*   **`next.config.mjs`**: Configuration file for Next.js.
*   **`tsconfig.json`**: TypeScript configuration.
*   **`package.json`**: Project dependencies and scripts.

## 4. Routing

*   Routing is handled by the **Next.js App Router**, based on the directory structure within `frontend/app/`.
*   The root page (`app/page.tsx`) redirects to `/login`, indicating the application is primarily for authenticated users.
*   Authenticated sections are generally wrapped in the `DashboardLayout` component, which includes an authentication check and redirects unauthenticated users to `/login`.

## 5. Component Architecture

*   Components are primarily React Server Components (RSC) by default in the App Router unless specified with `"use client"`.
*   Layouts, pages, and many display components are likely Server Components.
*   Interactive components, or those using React hooks like `useState`, `useEffect`, or custom hooks relying on client-side features (like `useAuth`, `useApiData`), are Client Components (marked with `"use client"`).
    *   Examples: `DashboardLayout.tsx`, `Header.tsx`, `Sidebar.tsx`, components using forms or handling user events.
*   **Layouts:**
    *   A root `app/layout.tsx` sets up global providers.
    *   `components/layout/dashboard-layout.tsx` provides the main authenticated application shell (sidebar, header, content area).
*   **Content Components:** Specific pages (e.g., `app/products/page.tsx`) typically use the `DashboardLayout` and then render a main content component (e.g., `components/products/products-content.tsx`) which encapsulates the feature-specific UI and logic.

## 6. State Management

*   **Global State:**
    *   **Authentication:** Managed by `AuthContext` (provided by `AuthProvider` via `hooks/use-auth.tsx`). This context provides user information, loading status, and login/logout functions. User data and JWT are persisted in `localStorage`.
    *   **Application Data:** Managed by `ApiDataContext` (provided by `ApiDataProvider` via `hooks/use-api-data.tsx`). This context centralizes data fetched from the backend (products, categories, etc.), along with their loading/error states and functions to perform CRUD operations and refresh data. It acts as a client-side cache/store.
    *   **Theme:** Managed by `ThemeProvider` from `next-themes`.
*   **Local Component State:** Standard React `useState` and `useReducer` are used for managing state within individual components.
*   **Data Fetching State:** Loading and error states for API calls are managed within `ApiDataContext` and exposed to components.

## 7. API Interaction

1.  **Axios Client (`lib/api.ts`):** A global Axios instance is configured with a base URL and interceptors.
    *   **Request Interceptor:** Automatically adds the JWT from `localStorage` to the `Authorization` header.
    *   **Response Interceptor:** Handles global API errors, displays toast notifications, and redirects to `/login` on 401 Unauthorized responses.
2.  **Services (`services/*.ts`):** Each service file groups API calls related to a specific backend resource (e.g., `productService.getAll()`, `authService.login()`). These services use the global Axios client.
3.  **Data Context (`hooks/use-api-data.tsx`):**
    *   The `ApiDataProvider` uses the services to fetch and mutate data.
    *   It provides functions (`refreshProducts`, `addProduct`, etc.) that components can call.
    *   After successful mutations (add, update, delete), it typically re-triggers the relevant refresh function to update the data store and reflect changes in the UI.
4.  **UI Components:** Components consume data and action functions from `useApiData()` and `useAuth()` hooks. They trigger actions (e.g., button click calls `addProduct`) and display data from the context.

## 8. Styling

*   **Tailwind CSS:** Primarily used for styling, configured via `tailwind.config.ts`. Utility classes are applied directly in component JSX.
*   **`cn` utility (`lib/utils.ts`):** Combines `clsx` and `twMerge` for conditional and conflict-free class name generation.
*   **Global Styles (`styles/globals.css`):** Contains Tailwind base styles and any custom global CSS.
*   **Theming:** Light and dark themes are supported using `next-themes` and Tailwind's dark mode variant (`dark:`).

## 9. Build and Deployment

*   **Build:** `npm run build` or `pnpm build` (as per `package.json`) which runs `next build`.
*   **Deployment:** As a Next.js application, it can be deployed to various platforms like Vercel (common for Next.js), Netlify, AWS Amplify, or self-hosted Node.js servers.

This architecture provides a modular, maintainable, and scalable frontend application, leveraging the strengths of Next.js and TypeScript for a modern web experience.
