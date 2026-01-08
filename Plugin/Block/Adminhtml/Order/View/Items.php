<?php
/**
 * Copyright © Avenyra. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Avenyra\OrderItemImages\Plugin\Block\Adminhtml\Order\View;

use Magento\Sales\Block\Adminhtml\Order\View\Items as Subject;

/**
 * Plugin to add image column to order items grid
 */
class Items
{
    /**
     * Add image column
     *
     * @param Subject $_
     * @param array $result
     * @return array
     */
    public function afterGetColumns(Subject $_, array $result): array
    {
        return ['product-thumbnail' => 'Product Image'] + $result;
    }
}

