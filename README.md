# E-Commerce App

A Laravel-based E-Commerce REST API designed to manage users, categories, products, shopping carts, and orders through a structured API.

## Overview

This project provides the backend infrastructure for an e-commerce application, with authentication, role-based access, product and category management, cart operations, and order handling.

The API is designed to be consumed by a frontend application and returns data in JSON format.

## Core Features

- User registration and login
- Token-based authentication
- User profile
- Admin and regular user roles
- Category management
- Product management
- Product search and sorting
- Product pagination
- Product image upload and management
- Product soft deletion and restoration
- Admin dashboard statistics
- Shopping cart management
- Order creation
- Order listing
- Request validation
- Protected API routes

## Authentication

Authentication is implemented using Laravel Sanctum.

Users can register and log in through the API. After successful authentication, the API returns an authentication token that is used to access protected endpoints.

The authenticated user can also retrieve their profile information and log out.

### Authentication Flow

```text
Register / Login
       |
       v
Laravel Sanctum
       |
       v
Authentication Token
       |
       v
Protected API Requests
```

## User Roles

The application supports different access levels for users.

### Regular User

Regular users can:

- View categories
- View products
- Search and sort products
- View product details
- Manage their cart
- Create orders
- View their orders
- View their profile

### Admin

Administrators have additional access to:

- Manage categories
- Manage products
- Restore deleted products
- View deleted products
- Access dashboard statistics
- View relevant orders

Admin-protected endpoints are handled through authentication and admin middleware.

## Product Management

Products are managed through the `ProductController`.

Each product can contain:

- Title
- Description
- Price
- Budget Range
- Note
- Date
- Category
- Images
- User relationship

Products support:

- Create
- Read
- Update
- Delete
- Restore
- Search
- Sorting
- Pagination
- Image upload

### Product Search

Products can be searched by title.

```text
GET /products?search=keyword
```

### Product Sorting

Products can be sorted by:

- Price
- Date
- Latest/default order

```text
GET /products?sort=price
GET /products?sort=date
```

### Pagination

Product results are paginated, with 10 products returned per page.

## Product Images

The API supports uploading multiple product images.

Images are stored using Laravel's public storage disk and returned as accessible storage URLs through the API response.

When a product is updated with new images, the existing product images are removed before the new images are stored.

Image validation is applied to uploaded files with a maximum size of 2 MB per image.

## Product Soft Delete

Products use Laravel Soft Deletes.

Instead of permanently removing a product from the database, deleted products can be restored later.

The API provides separate functionality for:

- Viewing deleted products
- Restoring a deleted product

This allows administrators to manage deleted products without permanently losing their data.

## Categories

Categories are used to organize products.

The API supports category:

- Listing
- Viewing
- Creating
- Updating
- Deleting

Category creation and modification are restricted to administrators.

## Shopping Cart

Authenticated users have their own shopping cart.

The cart supports:

- Viewing the current cart
- Adding products
- Updating item quantities
- Removing items
- Calculating total items
- Calculating total price

When an item is added, the system checks whether the product already exists in the user's cart.

If it exists, its quantity is increased instead of creating a duplicate cart item.

### Cart Structure

```text
User
 |
 v
Cart
 |
 +---- Cart Item
 |       |
 |       +---- Product
 |
 +---- Cart Item
         |
         +---- Product
```

The cart response includes:

- Cart ID
- Total items
- Total price
- Cart items
- Product information

## Orders

Authenticated users can create orders from their cart items.

An order contains information such as:

- User
- Order number
- Address
- Phone
- Payment method
- Total price
- Items
- Order status

New orders are created with the initial status:

```text
requested
```

The API also supports retrieving orders.

Regular users receive their own orders, while administrators can retrieve orders with relevant statuses.

## Order Flow

```text
Products
   |
   v
Shopping Cart
   |
   v
Checkout / Order Request
   |
   v
Order Created
   |
   v
Order Status
```

When an order is created, the selected cart items can be removed from the user's cart.

## Admin Dashboard

The API provides dashboard statistics for administrators.

The dashboard includes:

- Total products
- Active products
- Deleted products
- Active product percentage

Example:

```text
Total Products
      |
      +---- Active Products
      |
      +---- Deleted Products
      |
      +---- Active Percentage
```

## API Structure

The main API endpoints are organized around the application's core resources.

### Authentication

```text
POST   /register
POST   /login
POST   /logout
GET    /profile
```

### Categories

```text
GET    /categories
GET    /categories/{id}

POST   /categories
PUT    /categories/{id}
DELETE /categories/{id}
```

### Products

```text
GET    /products
GET    /products/{id}

POST   /products
PUT    /products/{id}
DELETE /products/{id}

POST   /products/{id}/restore
GET    /deleted-products
```

### Cart

```text
GET    /cart
POST   /cart/add
PUT    /cart/items/{id}
DELETE /cart/items/{id}
```

### Orders

```text
GET    /orders
POST   /orders
```

### Dashboard

```text
GET    /dashboard
```

The API routes are protected using Laravel Sanctum, with additional admin middleware applied to administrative operations.

## Request Validation

The API validates incoming data before processing requests.

Examples include:

- Required fields
- Email validation
- Unique email addresses
- Numeric prices
- Valid categories
- Valid dates
- Valid budget ranges
- Image type and size validation
- Valid product IDs
- Valid cart quantities

This helps keep the API data consistent and prevents invalid requests from being processed.

## Database Relationships

The application uses Laravel Eloquent relationships to connect the main entities.

The main relationships include:

```text
User
 |
 +---- Products
 |
 +---- Cart
 |
 +---- Orders

Category
 |
 +---- Products

Product
 |
 +---- Category
 +---- User
 +---- Cart Items
```

## Backend Architecture

The project follows Laravel's standard application structure.

```text
app/
├── Http/
│   └── Controllers/
│       ├── AuthController
│       ├── CategoryController
│       ├── ProductController
│       ├── CartController
│       └── OrderController
│
├── Models/
│   ├── User
│   ├── Category
│   ├── Product
│   ├── Cart
│   ├── CartItem
│   └── Order
│
database/
├── migrations/
└── seeders/

routes/
└── api.php

tests/
```

## Technologies Used

| Technology | Purpose |
|---|---|
| Laravel 12 | Backend framework |
| PHP 8.2+ | Backend language |
| Laravel Sanctum | API authentication |
| MySQL | Database |
| Eloquent ORM | Database interaction |
| REST API | Client-server communication |
| Laravel Storage | Product image management |
| Composer | PHP dependency management |
| PHPUnit | Testing |

## Development Focus

This project provided practical experience with:

- Laravel REST API development
- Authentication using Laravel Sanctum
- Role-based authorization
- CRUD operations
- Eloquent relationships
- Database migrations
- Request validation
- File uploads
- Image storage
- Soft deletes
- Search and filtering
- Pagination
- Shopping cart logic
- Order management
- API response design
- Protected API routes

## Project Structure

```text
Ecommerce-App/
│
├── app/
│   ├── Http/
│   │   └── Controllers/
│   └── Models/
│
├── database/
│   ├── migrations/
│   └── seeders/
│
├── routes/
│   └── api.php
│
├── public/
├── resources/
├── storage/
├── tests/
│
├── composer.json
├── package.json
├── vite.config.js
└── README.md
```

## Author

Ahmed Ezzat
