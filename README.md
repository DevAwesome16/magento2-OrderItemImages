[![Latest Stable Version](http://poser.pugx.org/avenyra/module-order-item-images/v)](https://packagist.org/packages/avenyra/module-order-item-images)
[![Total Downloads](http://poser.pugx.org/avenyra/module-order-item-images/downloads)](https://packagist.org/packages/avenyra/module-order-item-images)
[![License](http://poser.pugx.org/avenyra/module-order-item-images/license)](https://packagist.org/packages/avenyra/module-order-item-images)

# Avenyra Order Item Images

A Magento 2 module that captures and stores product thumbnail images for order items, making them available in the admin panel on order view page and via GraphQL for headless storefronts.

## Features

- **Automatic Image Capture**: Automatically captures product thumbnail URLs when orders are placed
- **Admin Panel Display**: Shows product thumbnails in the order view page for better visual identification
- **GraphQL Support**: Exposes thumbnail URLs via GraphQL for headless commerce implementations
- **Configurable Product Support**: Intelligently selects child product images for configurable products
- **Database Storage**: Stores thumbnail URLs in the database to preserve historical product images
- **Placeholder Fallback**: Automatically uses placeholder images when product images are unavailable

## Requirements

- PHP 8.1+
- Magento 2.4.5+

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

## How It Works

### Order Placement

When a customer completes checkout, the module:

1. Captures the product thumbnail URL for each order item
2. For configurable products, uses the selected variant's image
3. Stores the thumbnail URL in the database table
4. Falls back to placeholder images if product images are unavailable

### Admin Panel

The module enhances the order view page by:

- Adding a "Thumbnail" column to the order items grid
- Displaying product images (90px height) for visual identification

### ScreenShots
#### Configurable Products
<img width="1920" height="919" alt="av_orderitemimages_1" src="https://github.com/user-attachments/assets/dd9aa390-86a8-4db8-bf00-1d5f9e467452" />

#### Simple & Bundle Products
<img width="1920" height="919" alt="av_orderitemimages_2" src="https://github.com/user-attachments/assets/6b597b67-7272-4246-a3bc-e000ab88b311" /><br/>

#### Placeholder Images
<img width="1920" height="919" alt="av_orderitemimages_3" src="https://github.com/user-attachments/assets/17201ff5-4d0d-4ff6-90a4-8920e566aec2" /><br/>


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

## Support

Found a bug or issue? Please <a href="https://github.com/Avenyra/magento2-OrderItemImages/issues">open an issue</a> on GitHub.

## Author

**Avenyra Solutions**
