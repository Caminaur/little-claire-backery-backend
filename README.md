# Coffee Shop Backend API

## Overview

This project is a **backend API for a coffee shop**.
It manages products, categories, menus, promotions and contact requests.

The API is **admin-only**. There are no users, orders or payments.

---

## Tech Stack

- Laravel (API only)
- MySQL
- Eloquent ORM
- PHP Enums

Frontend is a **separate React SPA** that consumes this API.

---

## Main Ideas

### Catalog vs Menu

- **Catalog**: all categories and products
- **Menu**: a selected and ordered set of categories and products

A product belongs to one category, but can appear in multiple menus.

---

## Domain Diagram

> Diagram created with **dbdiagram.io**

<img src='./docs/uml.png' width='700px' style='border-radius:8px;'/>

---

## Core Models

### Category

- Groups products in the catalog
- Can be visible or hidden

### Product

- Belongs to a category
- Has a base price
- Can appear in multiple menus

### ProductImage

- Stores product images
- Order is handled by `position`

### Menu

- Represents a PDF menu configuration
- Controls ordering of categories and products
- Only one menu should be active at a time

### Promotion

- Applies discounts to products
- Discount type is defined using enums

### ContactRequest

- Stores contact form submissions
- Used for general or catering requests

---

## Pivot Tables

Pivot tables store **relationship-specific data**:

- Menu ↔ Category (ordering)
- Menu ↔ Product (ordering and custom price)
- Promotion ↔ Product (relation only)

---

## Project Status

- Migrations completed
- Domain models defined
- Relationships in place

Next steps:

- Factories & seeders
- Services (menu logic, pricing)
- API controllers
- PDF generation

---

## Notes

This project focuses on a **clean domain model** and explicit relationships.
