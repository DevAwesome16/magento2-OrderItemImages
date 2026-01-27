<?php
/**
 * Copyright © Avenyra. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Avenyra\OrderItemImages\Model\Resolver;

use Avenyra\OrderItemImages\Helper\Image as ImageHelper;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\Resolver\ContextInterface;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Sales\Api\Data\OrderItemInterface;

/**
 * Resolver for item_image field in OrderItemInterface
 */
class OrderItemThumbnailUrl implements ResolverInterface
{
    /**
     * @param ImageHelper $imageHelper
     */
    public function __construct(
        private readonly ImageHelper $imageHelper
    ) {
    }

    /**
     * Resolve product thumbnail URL for order item
     *
     * @param Field $field
     * @param ContextInterface $context
     * @param ResolveInfo $info
     * @param array|null $value
     * @param array|null $args
     * @return string|null
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function resolve(
        Field $field,
        ContextInterface $context,
        ResolveInfo $info,
        ?array $value = null,
        ?array $args = null
    ): ?string {
        if (!isset($value['model'])) {
            return null;
        }

        /** @var OrderItemInterface $orderItem */
        $orderItem = $value['model'];
        $imageUrl = $orderItem->getData('product_thumbnail_url');
        return $this->imageHelper->getThumbnailOrPlaceholder($imageUrl);
    }
}
