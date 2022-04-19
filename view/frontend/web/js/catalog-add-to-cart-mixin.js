define([
    'underscore',
    'jquery',
    'mage/translate',
    'Magento_Customer/js/customer-data'
], function (_, $, $t, customerData) {
    'use strict';

    return function (widget) {

        $.widget('mage.catalogAddToCart', widget, {
            /**
             * @param {jQuery} form
             */
            ajaxSubmit: function (form) {
                let qty = 0,
                    serviceProduct = {},
                    serviceQty = 0;
                $.each(form.serializeArray(), function(k, item){
                    if(item.name.indexOf('qty') !== -1){
                        qty = parseInt(item.value);
                    }
                    if(item.name.indexOf('service_product') !== -1){
                        let index = item.name.replace('service_product[', '').replace(']', '');
                        if(typeof serviceProduct[index] === 'undefined'){
                            serviceProduct[index] = {qty: item.value};
                        }else{
                            serviceProduct[index]['qty'] = item.value;
                        }
                    }
                    if(item.name.indexOf('selected[') !== -1){
                        let index = item.name.replace('selected[', '').replace(']',''),
                            value = item.value === 'on';
                        if(typeof serviceProduct[index] === 'undefined'){
                            serviceProduct[index] = {'selected' : value};
                        }else{
                            serviceProduct[index]['selected'] = value;
                        }
                    }
                });
                $.each(serviceProduct, function (k, item){
                    if(item['selected']){
                        serviceQty += parseInt(item['qty']);
                    }    
                });
                if(serviceQty > qty){
                    customerData.set('messages', {
                        messages: [{
                            type: 'error',
                            text: $t('You are not allowed to purchase more service items than main products')
                        }],
                        'data_id': Math.floor(Date.now() / 1000)
                    });
                    setTimeout(function () {
                        customerData.set('messages', {});
                    }, 4000);
                    return false;
                }
                return this._super(form);
            }
        });

        return $.mage.catalogAddToCart;
    };
});
