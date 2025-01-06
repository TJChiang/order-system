# Order System

## API

### New orders from official website

POST /order

### Update order

PUT /order/:id

### Delete order

DELETE /order/:id

### Get orders

GET /order


## Database schema relationship

```mermaid
---
title: Order System Database Schema Relationship
---
erDiagram
    user ||--o{ orders : "one to zero/many"
    orders ||--|{ order_items : "one to many"
    orders ||--|{ shipments : "one to many"
    shipments }|--|{ order_items : "many to many"
    order_items ||--|| products : "one to one"
```
