<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Tax\Model\App\Action;

use Magento\Customer\Model\Context;
use Magento\Customer\Model\GroupManagement;

/**
 * Class ContextPlugin
 */
class ContextPlugin
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Magento\Framework\App\Http\Context
     */
    protected $httpContext;

    /**
     * @var \Magento\Tax\Helper\Data
     */
    protected $taxHelper;

    /**
     * @var \Magento\Tax\Model\Calculation\Proxy
     */
    protected $taxCalculation;

    /**
     * Module manager
     *
     * @var \Magento\Framework\Module\Manager
     */
    private $moduleManager;

    /**
     * Cache config
     *
     * @var \Magento\PageCache\Model\Config
     */
    private $cacheConfig;

    /**
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param \Magento\Tax\Model\Calculation\Proxy $calculation
     * @param \Magento\Tax\Helper\Data $taxHelper
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param \Magento\PageCache\Model\Config $cacheConfig
     */
    public function __construct(
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\App\Http\Context $httpContext,
        \Magento\Tax\Model\Calculation\Proxy $calculation,
        \Magento\Tax\Helper\Data $taxHelper,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\PageCache\Model\Config $cacheConfig
    ) {
        $this->customerSession = $customerSession;
        $this->httpContext = $httpContext;
        $this->taxCalculation = $calculation;
        $this->taxHelper = $taxHelper;
        $this->moduleManager = $moduleManager;
        $this->cacheConfig = $cacheConfig;
    }

    /**
     * @param \Magento\Framework\App\ActionInterface $subject
     * @param callable $proceed
     * @param \Magento\Framework\App\RequestInterface $request
     * @return mixed
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundDispatch(
        \Magento\Framework\App\ActionInterface $subject,
        \Closure $proceed,
        \Magento\Framework\App\RequestInterface $request
    ) {
        if (!$this->customerSession->isLoggedIn() ||
            !$this->moduleManager->isEnabled('Magento_PageCache') ||
            !$this->cacheConfig->isEnabled() ||
            !$this->taxHelper->isCatalogPriceDisplayAffectedByTax()) {
            return $proceed($request);
        }

        $defaultBillingAddress = $this->customerSession->getDefaultTaxBillingAddress();
        $defaultShippingAddress = $this->customerSession->getDefaultTaxShippingAddress();
        $customerTaxClassId = $this->customerSession->getCustomerTaxClassId();

        if (!empty($defaultBillingAddress) || !empty($defaultShippingAddress)) {
            $taxRates = $this->taxCalculation->getTaxRates(
                $defaultBillingAddress,
                $defaultShippingAddress,
                $customerTaxClassId
            );
            $this->httpContext->setValue(
                'tax_rates',
                $taxRates,
                0
            );
        }
        return $proceed($request);
    }
}
