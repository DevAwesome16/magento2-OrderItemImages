<?php
/**
 * Copyright © Avenyra. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Avenyra\OrderItemImages\Plugin\Block\Adminhtml\Order\View\Items\Renderer;

use Avenyra\OrderItemImages\Helper\Image as ImageHelper;
use Magento\Framework\DataObject;
use Magento\Sales\Block\Adminhtml\Order\View\Items\Renderer\DefaultRenderer as Subject;

/**
 * Plugin to render image column in order items grid
 */
class DefaultRenderer
{
    /**
     * @param ImageHelper $imageHelper
     */
    public function __construct(
        private readonly ImageHelper $imageHelper
    ) {
    }

    /**
     * Generate image column html
     *
     * @param Subject $_
     * @param string $result
     * @param DataObject $item
     * @param string $column
     * @return string
     */
    public function afterGetColumnHtml(Subject $_, string $result, DataObject $item, string $column): string
    {
        if ($column === "product-thumbnail") {
            $imageUrl = !empty($item->getData('product_thumbnail_url')) ? $item->getData('product_thumbnail_url') : $this->imageHelper->getPlaceholderUrl();
            $result = '<img src="' . $imageUrl . '" height="90" alt="" align="left" style="padding-right:2px;">';
        }
        return $result;
    }

    /**
     * Add image column class
     *
     * @param Subject $_
     * @param array $result
     * @return array
     */
    public function afterGetColumns(Subject $_, array $result): array
    {
        return ['product-thumbnail' => 'col-product-thumbnail'] + $result;
    }
}

