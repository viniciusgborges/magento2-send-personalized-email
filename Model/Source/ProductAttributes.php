<?php
declare(strict_types=1);

namespace Vbdev\PersonalizedEmail\Model\Source;

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory;
use Magento\Framework\Data\OptionSourceInterface;

class ProductAttributes extends Template implements OptionSourceInterface
{
    private CollectionFactory $collectionFactory;

    /**
     * @param Context $context
     * @param CollectionFactory $collectionFactory
     * @param array $data
     */
    public function __construct(
        Context           $context,
        CollectionFactory $collectionFactory,
        array             $data = []
    ) {
        $this->collectionFactory = $collectionFactory;
        parent::__construct($context, $data);
    }

    /**
     * @return array
     */
    public function toOptionArray(): array
    {
        $attribute_data = [];
        $attributeInfo = $this->collectionFactory->create();
        foreach ($attributeInfo as $items) {
            $attribute_data[$items->getData('attribute_id')] = $items->getData('frontend_label');
        }

        return $attribute_data;
    }
}
