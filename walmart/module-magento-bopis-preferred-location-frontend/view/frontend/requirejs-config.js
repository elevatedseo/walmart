/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */

var config = {
    map: {
        '*': {
            bopisLocation: 'Walmart_BopisPreferredLocationFrontend/js/model/location',
            bopisBrowserLocation: 'Walmart_BopisPreferredLocationFrontend/js/model/browser-location',
            bopisBrowserLocationStorage: 'Walmart_BopisPreferredLocationFrontend/js/bopis-geo-location-local-storage',
            bopisUrlBuilder: 'Walmart_BopisPreferredLocationFrontend/js/model/url-builder',
            bopisGrouped: 'Walmart_BopisPreferredLocationFrontend/js/bopis-grouped'
        }
    },
    config: {
        mixins: {
            'Magento_Bundle/js/price-bundle': {
                'Walmart_BopisPreferredLocationFrontend/js/price-bundle-mixin': true
            }
        }
    }
};
