# Tiny Household Database Specification

Version: 1.0.0

---

# Design Principles

1. Inventory is never edited directly.
2. Every inventory change creates a Stock Movement.
3. Products are reusable.
4. Variants store brand and size.
5. Telegram contains no business logic.
6. All timestamps use Laravel timestamps.
7. Soft Delete only where appropriate.

---

# Table List

1. users
2. categories
3. products
4. variants
5. locations
6. inventory
7. stock_movements
8. shopping_lists
9. receipts
10. receipt_items
11. settings

## products

Purpose

Stores generic products.

Example

Milk

Wet Tissue

Diaper

Columns

| Column        | Type          | Nullable | Default | Index |
| ------------- | ------------- | -------- | ------- | ----- |
| id            | bigint        | No       |         | PK    |
| category_id   | bigint        | No       |         | FK    |
| name          | varchar(100)  | No       |         | INDEX |
| minimum_stock | decimal(10,2) | No       | 0       |       |
| default_unit  | varchar(20)   | No       | pcs     |       |
| status        | tinyint       | No       | Active  |       |
| created_by    | bigint        | Yes      |         |       |
| updated_by    | bigint        | Yes      |         |       |
| created_at    | timestamp     |          |         |       |
| updated_at    | timestamp     |          |         |       |
| deleted_at    | timestamp     |          |         |       |

Take this:

Dettol 5L
Dettol Floor Cleaner

Those are actually different products, not just variants.

Similarly:

Lotus Milk 1L
Lotus Milk 200ml

Those are variants.

So I propose we separate them like this:

Category
│
▼
Product
│
▼
Variant

Examples:

Cleaning

↓

Product
Floor Cleaner

↓

Variants
Dettol Floor Cleaner 900ml
Dettol Floor Cleaner 2L
Dettol Floor Cleaner 5L

Another example:

Laundry

↓

Product
Laundry Detergent

↓

Variants
Top Powder 4kg
Top Powder 8kg
Dynamo Liquid 2.8L
Sunlight Liquid 3.6kg

This makes OCR and reporting much more consistent.

I want every table to have a clear responsibility.

For example:

Table Responsibility
products Defines what the item is.
variants Defines the purchasable version (brand, size, packaging).
inventory Current quantity only.
stock_movements Permanent audit log of all changes.
receipts Receipt header.
receipt_items Individual items purchased.

| Component | Choice                |
| --------- | --------------------- |
| Backend   | Laravel 13            |
| PHP       | 8.3                   |
| Database  | MySQL                 |
| Bot       | Telegram Bot API      |
| Hosting   | cPanel                |
| Queue     | Laravel Queue (later) |
| OCR       | Later                 |
| Dashboard | Later                 |
| Mobile    | Flutter (future)      |
