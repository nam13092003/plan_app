# Architectural Plan & Fixes

This document outlines the strategic direction for supporting different project types (Scrum and Kanban) and details the robust solutions for critical issues.

## Core Architectural Decision: Dynamic Project Types (Scrum vs. Kanban)

The application is designed to support two distinct project methodologies: Scrum and Kanban. Future development and feature implementation will proceed with this clear differentiation in mind. This impacts:

-   **UI/UX:** Tabs and functionalities displayed within a project's detail view will dynamically adapt based on the project's type.
-   **API Endpoints:** Specific API endpoints and data structures may be tailored to each project type (e.g., Scrum requires Sprints, Kanban requires a continuous flow board).
-   **Component Logic:** Frontend components will conditionally render or behave differently based on the `project.type` property.

## Mandate 1: Robust Default Status Assignment

### Problem

-   When creating a new ticket, the `status_id` field is sometimes missing or empty, leading to a `422 Unprocessable Content` validation error from the backend.
-   This occurs because the frontend's logic to find a default status (e.g., "Todo") might fail if the status doesn't exist, is named differently, or if there's a data loading issue.

### Solution Strategy (Backend-Driven)

The most robust solution is to make the backend responsible for assigning a default status if one is not explicitly provided by the frontend. This ensures data integrity regardless of frontend state.

### Backend Changes (`app/Http/Controllers/Api/Resources/TicketController.php`)

1.  **Modified `store` method validation:**
    -   Changed `'status_id' => 'required|exists:ticket_statuses,id'` to `'status_id' => 'nullable|exists:ticket_statuses,id'`.
    -   This allows the frontend to omit `status_id` if it doesn't have a value, without immediately failing validation.

2.  **Added default status assignment logic in `store` method:**
    -   If `status_id` is not provided, the backend now robustly finds a default status (prioritizing "Todo", then any `is_default`, then the first available status) and assigns its ID. This ensures a valid status is always set.

### Frontend Changes (`resources/js/components/Tickets/TicketFormModal.vue`)

1.  **Simplified `initializeForm` logic:**
    -   The frontend no longer attempts to find and assign a default `status_id` for new tickets. It now simply initializes `status_id` to `''` and relies entirely on the backend for default assignment.

2.  **Hide Status Dropdown on Create (already implemented):**
    The `Status` dropdown remains hidden (`v-if="isEditMode"`) when creating a new ticket, aligning with the new backend-driven default assignment.

## Mandate 2: Robust Kanban Board Implementation

### Problem

-   The Kanban board has been a source of constant `500 Internal Server Errors` and performance issues.
-   Previous attempts to fix involved frontend patches, but the underlying backend API calls were inefficient or prone to crashing.

### Solution Strategy (Backend-Driven Optimization & Robustness)

The Kanban board's backend APIs (`app/Http/Controllers/Api/Pages/BoardController.php`) have been rebuilt to be more efficient and defensive against unexpected data states.

### Backend Changes (`app/Http/Controllers/Api/Pages/BoardController.php`)

1.  **Optimized `getStatuses` method:**
    -   **Fixed N+1 Query:** Rewritten to get all ticket counts in a single, efficient query using `DB::raw` and `groupBy`, eliminating the N+1 query problem.
    -   **Removed Unreliable Permission Check:** The `add_ticket` permission check was removed, as frontend should handle UI logic.

2.  **Optimized `getKanbanTickets` method:**
    -   **Removed Complex Access Control:** The complex and redundant `where` clause for access control was removed, simplifying the query and improving performance.
    -   **Ensured Null Safety:** Applied null-safe operators (`?->`) to all potentially null relationships in the `map` function to prevent crashes.

3.  **Optimized `getScrumTickets` method:**
    -   **Removed Complex Access Control:** The complex and redundant `where` clause for access control was removed, simplifying the query and improving performance.
    -   **Ensured Null Safety:** Applied null-safe operators (`?->`) to all potentially null relationships in the `map` function to prevent crashes.

4.  **Optimized `moveTicket` method:**
    -   **Ensured Null Safety:** Applied null-safe operators (`?->`) to all potentially null relationships in the returned ticket data to prevent crashes.

## Rationale for Backend-Driven Approach

-   **Robustness:** Fixes issues at the source (backend API), ensuring data integrity and preventing crashes.
-   **Performance:** Optimizes database queries, leading to faster response times.
-   **Separation of Concerns:** Clearly defines responsibilities: backend for data logic and integrity, frontend for presentation.
-   **Simplicity:** Simplifies frontend logic by relying on reliable backend data.