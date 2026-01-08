# Avenyra Order Item Images

A Magento 2 module that captures and stores product thumbnail images for order items, making them available in the admin panel and via GraphQL for headless storefronts.

## Features

- **Automatic Image Capture**: Automatically captures product thumbnail URLs when orders are placed
- **Admin Panel Display**: Shows product thumbnails in the order view page for better visual identification
- **GraphQL Support**: Exposes thumbnail URLs via GraphQL for headless commerce implementations
- **Configurable Product Support**: Intelligently selects child product images for configurable products
- **Database Storage**: Stores thumbnail URLs in the database to preserve historical product images
- **Placeholder Fallback**: Automatically uses placeholder images when product images are unavailable

## Requirements

- PHP 8.3+
- Magento 2.4.7+

## Installation

### Via Composer (Recommended)

```bash
composer require avenyra/module-order-item-images
php bin/magento module:enable Avenyra_OrderItemImages
php bin/magento setup:upgrade
php bin/magento setup:di:compile
php bin/magento setup:static-content:deploy
php bin/magento cache:flush
```

### Manual Installation

1. Create directory structure: `app/code/Avenyra/OrderItemImages`
2. Copy all module files to the directory
3. Run the following commands:

```bash
php bin/magento module:enable Avenyra_OrderItemImages
php bin/magento setup:upgrade
php bin/magento setup:di:compile
php bin/magento setup:static-content:deploy
php bin/magento cache:flush
```

## How It Works

### Order Placement

When a customer completes checkout, the module:
1. Captures the product thumbnail URL for each order item
2. For configurable products, uses the selected variant's image
3. Stores the thumbnail URL in the `sales_order_item` table
4. Falls back to placeholder images if product images are unavailable

### Admin Panel

The module enhances the order view page by:
- Adding a "Thumbnail" column to the order items grid
- Displaying product images (90px height) for visual identification
- Supporting bundle products with custom template rendering

### GraphQL API

The module extends the `OrderItemInterface` with:
- `item_thumbnail_url` field for retrieving product thumbnails
- Automatic placeholder fallback for missing images
- Full compatibility with headless storefronts

## GraphQL Usage

### Query Example

```graphql
{
  customer {
    orders {
      items {
        items {
          product_name
          product_sku
          item_thumbnail_url
          quantity_ordered
        }
      }
    }
  }
}
```

### Response Example

```json
{
  "data": {
    "customer": {
      "orders": {
        "items": [
          {
            "items": [
              {
                "product_name": "Sample Product",
                "product_sku": "SAMPLE-SKU",
                "item_thumbnail_url": "https://example.com/media/catalog/product/cache/.../image.jpg",
                "quantity_ordered": 2
              }
            ]
          }
        ]
      }
    }
  }
}
```

## Database Schema

The module adds the following column to the `sales_order_item` table:

| Column Name | Type | Length | Nullable | Description |
|------------|------|--------|----------|-------------|
| `product_thumbnail_url` | varchar | 255 | Yes | Product thumbnail image URL |

## Technical Details

### Observer

- **Event**: `checkout_onepage_controller_success_action`
- **Observer**: `Avenyra\OrderItemImages\Observer\SaveProductThumbnailUrl`
- **Purpose**: Captures and stores product thumbnail URLs when orders are placed

### Plugins

1. **Items Grid Plugin**
   - **Target**: `Magento\Sales\Block\Adminhtml\Order\View\Items`
   - **Purpose**: Adds thumbnail column to order items grid

2. **Renderer Plugin**
   - **Target**: `Magento\Sales\Block\Adminhtml\Order\View\Items\Renderer\DefaultRenderer`
   - **Purpose**: Renders thumbnail images in the grid

### GraphQL Resolver

- **Interface**: `OrderItemInterface`
- **Field**: `item_thumbnail_url`
- **Resolver**: `Avenyra\OrderItemImages\Model\Resolver\OrderItemThumbnailUrl`

## Support

For issues, questions, or contributions, please visit the GitHub repository.

## License

This module is licensed under the MIT License. See the LICENSE file for details.

## Author

**Avenyra**

