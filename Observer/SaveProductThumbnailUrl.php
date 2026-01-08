<?php
/**
 * Copyright © Avenyra. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Avenyra\OrderItemImages\Observer;

use Avenyra\OrderItemImages\Helper\Image as ImageHelper;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\OrderItemInterface;
use Magento\Sales\Api\OrderItemRepositoryInterface;
use Psr\Log\LoggerInterface;

/**
 * Observer to store product thumbnail URLs when order is placed
 */
class SaveProductThumbnailUrl implements ObserverInterface
{
    /**
     * @param ImageHelper $imageHelper
     * @param OrderItemRepositoryInterface $orderItemRepository
     * @param LoggerInterface $logger
     */
    public function __construct(
        private readonly ImageHelper $imageHelper,
        private readonly OrderItemRepositoryInterface $orderItemRepository,
        private readonly LoggerInterface $logger
    ) {
    }

    /**
     * Execute observer to capture product thumbnails
     *
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer): void
    {
        try {
            /** @var OrderInterface $order */
            $order = $observer->getEvent()->getOrder();

            if (!$order || !$order->getEntityId()) {
                return;
            }

            $this->processOrderItems($order);
        } catch (\Exception $e) {
            $this->logger->error(
                'Error storing product thumbnails for order',
                [
                    'order_id' => $order->getEntityId() ?? 'unknown',
                    'exception' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]
            );
        }
    }

    /**
     * Process all order items to capture thumbnails
     *
     * @param OrderInterface $order
     * @return void
     */
    private function processOrderItems(OrderInterface $order): void
    {
        $items = $order->getItems();

        if (!$items) {
            return;
        }

        foreach ($items as $item) {
            if ($item->getParentItemId()) {
                continue;
            }

            $this->captureItemThumbnail($item, (int)$order->getStoreId());
        }
    }

    /**
     * Capture thumbnail for a single order item
     *
     * @param OrderItemInterface $item
     * @param int $storeId
     * @return void
     */
    private function captureItemThumbnail(OrderItemInterface $item, int $storeId): void
    {
        try {
            $productId = $this->getProductIdForThumbnail($item);

            if (!$productId) {
                $this->logger->warning(
                    'No product ID found for order item',
                    ['item_id' => $item->getItemId()]
                );
                return;
            }

            $thumbnailUrl = $this->imageHelper->getProductThumbnailUrl($productId, $storeId);
            $thumbnailUrl = $this->imageHelper->getThumbnailOrPlaceholder($thumbnailUrl);

            if ($thumbnailUrl) {
                $item->setData('product_thumbnail_url', $thumbnailUrl);
                $this->orderItemRepository->save($item);
            }
        } catch (\Exception $e) {
            $this->logger->error(
                'Error capturing thumbnail for order item',
                [
                    'item_id' => $item->getItemId(),
                    'product_id' => $item->getProductId(),
                    'exception' => $e->getMessage()
                ]
            );
        }
    }

    /**
     * Get the appropriate product ID for thumbnail
     *
     * For configurable products, we want the child product's image
     * For other product types, use the main product
     *
     * @param OrderItemInterface $item
     * @return int|null
     */
    private function getProductIdForThumbnail(OrderItemInterface $item): ?int
    {
        // For configurable products, check if there's a child item
        if ($item->getProductType() === Configurable::TYPE_CODE) {
            $children = $item->getChildrenItems();
            if (!empty($children)) {
                $childItem = reset($children);
                if ($childItem && $childItem->getProductId()) {
                    return (int)$childItem->getProductId();
                }
            }
        }
        return $item->getProductId() ? (int)$item->getProductId() : null;
    }
}

