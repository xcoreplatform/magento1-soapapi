# Magento1 Soap Api
This module extends the soap api of Magento1 and is needed for the usage of the xCore. For more information or support see http://www.dealer4dealer.nl.

## Payment fees
To add payment fees (for example surcharges) to the sales order api either use the backend interface
or add an observer follow the steps below:

### Backend Interface
You can add simple fee fields via the backend interface.
Navigate to `System > Configuration > Dealer4Dealer xCore > Payment > Payment Method Fees`

![SysConf tab example](https://raw.githubusercontent.com/dealer4dealer/magento1-soapapi/master/payment_fee_config_tab.png)

- `title`: The name given to the fee in the order data submitted
- `base amount`: The "sales_flat_order" table column to use for amounts in the installations base currency 
- `amount`: The "sales_flat_order" table column to use for amounts in the shops currency 
- `tax rate`: The product class tax rate applied to the fee

### Observer
1. Listen for the event dealer4dealer_xcore_sales_order_payment_fee.
2. Fetch the order object in your observer with $observer->getOrder();
3. Create a new dealer4dealer_xcore/payment_fee object
4. Add te payment object to the xcore_payment_fees field (array) on the order.


## License
The MIT License (MIT)

Copyright (c) Dealer4Dealer

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
