/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
define(
    [
    'Magento_Ui/js/grid/columns/column'
    ], function (Column) {
        'use strict';

        return Column.extend(
            {
                defaults: {
                    bodyTmpl: 'Walmart_BopisInventorySourceReservation/product/grid/cell/source-quantity-with-reservation.html',
                    itemsToDisplay: 5
                },

                /**
                 * Get source quantity with reservation data
                 *
                 * @param   {Object} record - Record object
                 * @returns {Array} Result array
                 */
                getSourceQuantityWithReservationData: function (record) {
                    return record[this.index] ? record[this.index] : [];
                },

                /**
                 * @param   {Object} record - Record object
                 * @returns {Array} Result array
                 */
                getSourceQuantityWithReservationDataCut: function (record) {
                    var sourceQuantityWithReservationData = this.getSourceQuantityWithReservationData(record).slice(0, this.itemsToDisplay);

                    for (var i = 0; i < sourceQuantityWithReservationData.length; i++) {
                        sourceQuantityWithReservationData[i]['sources'] = sourceQuantityWithReservationData[i]['sources'].slice(0, this.itemsToDisplay);
                    }

                    return sourceQuantityWithReservationData;
                }
            }
        );
    }
);
