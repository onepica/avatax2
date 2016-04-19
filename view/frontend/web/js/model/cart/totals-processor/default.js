/**
 * This file is copy-paste from:
 * module-checkout\view\frontend\web\js\model\cart\totals-processor\default.js
 * With changed third parameter in call storage.post method.
 * After this changes, the message box will be updated on checkout cart after totals-estimation request
 *
 */
define(
    [
        'underscore',
        'Magento_Checkout/js/model/resource-url-manager',
        'Magento_Checkout/js/model/quote',
        'mage/storage',
        'Magento_Checkout/js/model/totals',
        'Magento_Checkout/js/model/error-processor'
    ],
    function (_, resourceUrlManager, quote, storage, totalsService, errorProcessor) {
        'use strict';

        return {
            requiredFields: ['countryId', 'region', 'regionId', 'postcode'],

            /**
             * Get shipping rates for specified address.
             */
            estimateTotals: function (address) {
                var serviceUrl, payload;
                totalsService.isLoading(true);
                serviceUrl = resourceUrlManager.getUrlForTotalsEstimationForNewAddress(quote),
                    payload = {
                        addressInformation: {
                            address: _.pick(address, this.requiredFields)
                        }
                    };

                if (quote.shippingMethod() && quote.shippingMethod()['method_code']) {
                    payload.addressInformation['shipping_method_code'] = quote.shippingMethod()['method_code'];
                    payload.addressInformation['shipping_carrier_code'] = quote.shippingMethod()['carrier_code'];
                }

                storage.post(
                    serviceUrl, JSON.stringify(payload), true
                ).done(
                    function (result) {
                        quote.setTotals(result);
                    }
                ).fail(
                    function (response) {
                        errorProcessor.process(response);
                    }
                ).always(
                    function () {
                        totalsService.isLoading(false);
                    }
                );
            }
        };
    }
);
