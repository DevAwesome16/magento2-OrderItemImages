<?php
/**
 * Copyright © Avenyra. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Avenyra\OrderItemImages\Helper;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Helper\Image as ImageHelper;
use Magento\Catalog\Model\Product;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Exception\NoSuchEntityException;
use Psr\Log\LoggerInterface;

/**
 * Helper class for product image URL operations
 */
class Image extends AbstractHelper
{
    /**
     * Product image type for thumbnail
     */
    private const IMAGE_TYPE_THUMBNAIL = 'product_thumbnail_image';

    /**
     * @param Context $context
     * @param ImageHelper $imageHelper
     * @param ProductRepositoryInterface $productRepository
     * @param LoggerInterface $logger
     */
    public function __construct(
        Context $context,
        private readonly ImageHelper $imageHelper,
        private readonly ProductRepositoryInterface $productRepository,
        private readonly LoggerInterface $logger
    ) {
        parent::__construct($context);
    }

    /**
     * Get product thumbnail URL
     *
     * @param int $productId
     * @param int|null $storeId
     * @return string|null
     */
    public function getProductThumbnailUrl(int $productId, ?int $storeId = null): ?string
    {
        try {
            $product = $this->productRepository->getById($productId, false, $storeId);
            return $this->getThumbnailUrlFromProduct($product);
        } catch (NoSuchEntityException $e) {
            $this->logger->warning(
                'Product not found when retrieving thumbnail URL',
                [
                    'product_id' => $productId,
                    'store_id' => $storeId,
                    'exception' => $e->getMessage()
                ]
            );
            return null;
        } catch (\Exception $e) {
            $this->logger->error(
                'Error retrieving product thumbnail URL',
                [
                    'product_id' => $productId,
                    'store_id' => $storeId,
                    'exception' => $e->getMessage()
                ]
            );
            return null;
        }
    }

    /**
     * Get thumbnail URL from product object
     *
     * @param ProductInterface|Product $product
     * @return string
     */
    public function getThumbnailUrlFromProduct(ProductInterface|Product $product): string
    {
        try {
            // Get thumbnail image URL
            return $this->imageHelper
                ->init($product, self::IMAGE_TYPE_THUMBNAIL)
                ->getUrl();
        } catch (\Exception $e) {
            $this->logger->error(
                'Error generating thumbnail URL from product',
                [
                    'product_id' => $product->getId(),
                    'exception' => $e->getMessage()
                ]
            );
            return $this->getPlaceholderUrl();
        }
    }

    /**
     * Get placeholder image URL
     *
     * @return string
     */
    public function getPlaceholderUrl(): string
    {
        try {
            return $this->imageHelper
                ->getDefaultPlaceholderUrl('thumbnail');
        } catch (\Exception $e) {
            $this->logger->error(
                'Error retrieving placeholder image URL',
                ['exception' => $e->getMessage()]
            );
            // Return empty string as fallback
            return '';
        }
    }

    /**
     * Get thumbnail URL or placeholder if URL is empty
     *
     * @param string|null $thumbnailUrl
     * @return string
     */
    public function getThumbnailOrPlaceholder(?string $thumbnailUrl): string
    {
        if (!empty($thumbnailUrl)) {
            return $thumbnailUrl;
        }

        return $this->getPlaceholderUrl();
    }
}

