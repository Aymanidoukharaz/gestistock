# Data Models (Database Schema)

This document outlines the main data models used in the GESTISTOCK application, corresponding to the database schema. These models are defined as Eloquent ORM models in the `api/app/Models/` directory.

## Core Models

### 1. User (`User.php`)

Represents users of the application.

*   **Attributes:**
    *   `name` (string): Name of the user.
    *   `email` (string): Email address (unique).
    *   `password` (string, hashed): User's password.
    *   `role` (string): Role of the user (e.g., 'admin', 'magasinier').
    *   `active` (boolean): Status of the user account.
    *   `email_verified_at` (datetime, nullable): Timestamp of email verification.
    *   `remember_token` (string, nullable): Token for "remember me" functionality.
*   **Relationships:**
    *   `stockMovements()`: HasMany `StockMovement` (a user can perform many stock movements).
    *   `entryForms()`: HasMany `EntryForm` (a user can create many entry forms).
    *   `exitForms()`: HasMany `ExitForm` (a user can create many exit forms).
    *   `entryFormHistories()`: HasMany `EntryFormHistory` (changes made by a user to entry forms).
    *   `exitFormHistories()`: HasMany `ExitFormHistory` (changes made by a user to exit forms).

### 2. Product (`Product.php`)

Represents products in the inventory.

*   **Attributes:**
    *   `reference` (string): Unique reference for the product.
    *   `name` (string): Name of the product.
    *   `description` (text, nullable): Detailed description.
    *   `category_id` (foreignId): Foreign key referencing `categories.id`.
    *   `price` (decimal): Price of the product.
    *   `quantity` (integer): Current quantity in stock.
    *   `min_stock` (integer): Minimum desired stock level.
*   **Casts:**
    *   `price`: `decimal:2`
    *   `quantity`: `integer`
    *   `min_stock`: `integer`
*   **Relationships:**
    *   `category()`: BelongsTo `Category` (a product belongs to one category).
    *   `stockMovements()`: HasMany `StockMovement` (a product can have many stock movements).
    *   `entryItems()`: HasMany `EntryItem` (a product can be in many entry form items).
    *   `exitItems()`: HasMany `ExitItem` (a product can be in many exit form items).

### 3. Category (`Category.php`)

Represents categories for products.

*   **Attributes:**
    *   `name` (string): Name of the category.
    *   `description` (text, nullable): Description of the category. (*Self-correction: Based on `Category.php` provided, `description` is not a fillable attribute, so it might not be directly managed or has been removed. Will omit from attributes if not in `$fillable`.*)
    *   *Correction:* `description` field is not present in the `Category.php` model's `$fillable` array.
*   **Fillable Attributes:**
    *   `name` (string)
*   **Relationships:**
    *   `products()`: HasMany `Product` (a category can have many products).

### 4. Supplier (`Supplier.php`)

Represents suppliers of products.

*   **Attributes:**
    *   `name` (string): Name of the supplier.
    *   `email` (string, nullable): Contact email.
    *   `phone` (string, nullable): Contact phone number.
    *   `address` (text, nullable): Physical address.
    *   `notes` (text, nullable): Additional notes about the supplier.
*   **Relationships:**
    *   `entryForms()`: HasMany `EntryForm` (a supplier can be associated with many entry forms).

### 5. EntryForm (`EntryForm.php`)

Represents a form for recording incoming stock (e.g., from a supplier).

*   **Attributes:**
    *   `reference` (string): Unique reference for the entry form.
    *   `date` (date): Date of the entry.
    *   `supplier_id` (foreignId, nullable): Foreign key referencing `suppliers.id`.
    *   `user_id` (foreignId): Foreign key referencing `users.id` (who created/processed the form).
    *   `notes` (text, nullable): Additional notes.
    *   `status` (string): Status of the form (e.g., 'draft', 'pending', 'completed', 'cancelled').
    *   `total` (decimal, nullable): Total value of the entry form.
*   **Casts:**
    *   `date`: `date`
    *   `total`: `decimal:2`
*   **Relationships:**
    *   `supplier()`: BelongsTo `Supplier`.
    *   `user()`: BelongsTo `User`.
    *   `items()` (aliased as `entryItems()`): HasMany `EntryItem` (an entry form has many items).
    *   `histories()`: HasMany `EntryFormHistory` (tracks changes to the entry form).

### 6. EntryItem (`EntryItem.php`)

Represents an individual item within an `EntryForm`.

*   **Attributes:**
    *   `entry_form_id` (foreignId): Foreign key referencing `entry_forms.id`.
    *   `product_id` (foreignId): Foreign key referencing `products.id`.
    *   `quantity` (integer): Quantity of the product entered.
    *   `unit_price` (decimal, nullable): Price per unit at the time of entry.
    *   `total` (decimal, nullable): Total price for this item (quantity * unit_price).
*   **Casts:**
    *   `quantity`: `integer`
    *   `unit_price`: `decimal:2`
    *   `total`: `decimal:2`
*   **Relationships:**
    *   `entryForm()`: BelongsTo `EntryForm`.
    *   `product()`: BelongsTo `Product`.

### 7. EntryFormHistory (`EntryFormHistory.php`)

Records changes made to `EntryForm` records.

*   **Attributes:**
    *   `entry_form_id` (foreignId): Link to the `EntryForm`.
    *   `user_id` (foreignId): Link to the `User` who made the change.
    *   `field_name` (string): Name of the field that was changed.
    *   `old_value` (text, nullable): Previous value of the field.
    *   `new_value` (text, nullable): New value of the field.
    *   `change_reason` (string, nullable): Reason for the change.
*   **Relationships:**
    *   `entryForm()`: BelongsTo `EntryForm`.
    *   `user()`: BelongsTo `User`.

### 8. ExitForm (`ExitForm.php`)

Represents a form for recording outgoing stock.

*   **Attributes:**
    *   `reference` (string): Unique reference for the exit form.
    *   `date` (date): Date of the exit.
    *   `user_id` (foreignId): Foreign key referencing `users.id` (who created/processed the form).
    *   `destination` (string, nullable): Where the products are going.
    *   `reason` (string, nullable): Reason for the exit.
    *   `notes` (text, nullable): Additional notes.
    *   `status` (string): Status of the form (e.g., 'draft', 'pending', 'completed', 'cancelled').
*   **Casts:**
    *   `date`: `date`
*   **Relationships:**
    *   `user()`: BelongsTo `User`.
    *   `items()` (aliased as `exitItems()`): HasMany `ExitItem` (an exit form has many items).
    *   `histories()`: HasMany `ExitFormHistory` (tracks changes to the exit form).

### 9. ExitItem (`ExitItem.php`)

Represents an individual item within an `ExitForm`.

*   **Attributes:**
    *   `exit_form_id` (foreignId): Foreign key referencing `exit_forms.id`.
    *   `product_id` (foreignId): Foreign key referencing `products.id`.
    *   `quantity` (integer): Quantity of the product exited.
*   **Casts:**
    *   `quantity`: `integer`
*   **Relationships:**
    *   `exitForm()`: BelongsTo `ExitForm`.
    *   `product()`: BelongsTo `Product`.

### 10. ExitFormHistory (`ExitFormHistory.php`)

Records changes made to `ExitForm` records.

*   **Attributes:**
    *   `exit_form_id` (foreignId): Link to the `ExitForm`.
    *   `user_id` (foreignId): Link to the `User` who made the change.
    *   `field_name` (string): Name of the field that was changed.
    *   `old_value` (text, nullable): Previous value of the field.
    *   `new_value` (text, nullable): New value of the field.
    *   `change_reason` (string, nullable): Reason for the change.
*   **Relationships:**
    *   `exitForm()`: BelongsTo `ExitForm`.
    *   `user()`: BelongsTo `User`.

### 11. StockMovement (`StockMovement.php`)

Records every change in product quantity.

*   **Attributes:**
    *   `product_id` (foreignId): Foreign key referencing `products.id`.
    *   `user_id` (foreignId, nullable): Foreign key referencing `users.id` (who initiated the movement, if applicable).
    *   `type` (string): Type of movement (e.g., 'entry', 'exit', 'adjustment').
    *   `quantity` (integer): The amount by which stock changed (can be positive or negative depending on interpretation, or always positive with type dictating direction).
    *   `reason` (string, nullable): Reason for the stock movement (e.g., 'entry_form_validation', 'exit_form_validation', 'stock_correction').
    *   `date` (datetime): Timestamp of the movement.
*   **Casts:**
    *   `quantity`: `integer`
    *   `date`: `datetime`
*   **Relationships:**
    *   `product()`: BelongsTo `Product`.
    *   `user()`: BelongsTo `User`.

## Relationships Summary

*   **One-to-Many:**
    *   `User` has many `StockMovement`, `EntryForm`, `ExitForm`, `EntryFormHistory`, `ExitFormHistory`.
    *   `Category` has many `Product`.
    *   `Supplier` has many `EntryForm`.
    *   `Product` has many `StockMovement`, `EntryItem`, `ExitItem`.
    *   `EntryForm` has many `EntryItem`, `EntryFormHistory`.
    *   `ExitForm` has many `ExitItem`, `ExitFormHistory`.
*   **Many-to-One (BelongsTo):**
    *   `Product` belongs to `Category`.
    *   `EntryForm` belongs to `Supplier` and `User`.
    *   `EntryItem` belongs to `EntryForm` and `Product`.
    *   `EntryFormHistory` belongs to `EntryForm` and `User`.
    *   `ExitForm` belongs to `User`.
    *   `ExitItem` belongs to `ExitForm` and `Product`.
    *   `ExitFormHistory` belongs to `ExitForm` and `User`.
    *   `StockMovement` belongs to `Product` and `User`.

This structure provides a comprehensive way to manage inventory, track changes, and associate operations with users and suppliers.
