# Konecta Symfony Project

Handcrafted by Augusto R. M. as demonstration for Konecta.

## Requirements

Before you begin, ensure you have met the following requirements:

- PHP 8.1 or higher with php8.1-pgsql
- Composer
- PostgreSQL database server v 14.9

## Installation

To install and run this project, follow these steps:

1. Clone the repository:

   ```bash
   git clone https://github.com/yourusername/konecta.git

## Queries

Here are some example queries you can run with this project:

### Get the Best Selling Product

To retrieve the best-selling product:

```sql
SELECT p.name AS name, p.reference AS reference, SUM(s.units) AS total_sales
FROM product p
LEFT JOIN sales s ON p.id = s.product_id
GROUP BY p.id, p.name, p.reference
ORDER BY total_sales DESC
LIMIT 1;
```

### Get the Product with the Largest Stock

To retrieve the product with the largest stock:

```sql
SELECT p.name AS name, p.reference AS reference, MAX(p.stock) AS max_stock
FROM product p
GROUP BY p.id, p.name, p.reference
ORDER BY max_stock DESC
LIMIT 1;
```