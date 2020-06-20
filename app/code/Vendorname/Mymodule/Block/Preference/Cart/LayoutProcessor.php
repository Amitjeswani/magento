<?php



namespace Vendorname\Mymodule\Block\Preference\Cart;


class LayoutProcessor extends \Magento\Checkout\Block\Cart\LayoutProcessor {


    public function __construct(
        \Magento\Checkout\Block\Checkout\AttributeMerger $merger, 
        \Magento\Directory\Model\ResourceModel\Country\Collection $countryCollection, 
        \Magento\Directory\Model\ResourceModel\Region\Collection $regionCollection
    ) {
        $this->merger = $merger;
        $this->countryCollection = $countryCollection;
        $this->regionCollection = $regionCollection;
         parent::__construct($merger, $countryCollection, $regionCollection);
    }


    /**
     * Process js Layout of block
     *
     * @param array $jsLayout
     * @return array
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function process($jsLayout)
    {
       $elements = [
        'city' => [
            'visible' => $this->isCityActive(),
            'formElement' => 'input',
            'label' => __('City'),
            'value' =>  null
        ],
        'country_id' => [
            'visible' => false,  //Remove the country
            'formElement' => 'select',
            'label' => __('Country'),
            'options' => [],
            'value' => null
        ],
        'region_id' => [
            'visible' => false,  //Remove the state
            'formElement' => 'select',
            'label' => __('State/Province'),
            'options' => [],
            'value' => null
        ],
        'postcode' => [
            'visible' => true,
            'formElement' => 'input',
            'label' => __('Zip/Postal Code'),
            'value' => null
        ]
    ];

      if (!isset($jsLayout['components']['checkoutProvider']['dictionaries'])) {
            $jsLayout['components']['checkoutProvider']['dictionaries'] = [
                'country_id' => $this->countryCollection->loadByStore()->toOptionArray(),
                'region_id' => $this->regionCollection->addAllowedCountriesFilter()->toOptionArray(),
            ];
        }

        if (isset($jsLayout['components']['block-summary']['children']['block-shipping']['children']
            ['address-fieldsets']['children'])
        ) {
            $fieldSetPointer = &$jsLayout['components']['block-summary']['children']['block-shipping']
            ['children']['address-fieldsets']['children'];
            $fieldSetPointer = $this->merger->merge($elements, 'checkoutProvider', 'shippingAddress', $fieldSetPointer);
            $fieldSetPointer['region_id']['config']['skipValidation'] = true;
        }
        return $jsLayout;
    }
}