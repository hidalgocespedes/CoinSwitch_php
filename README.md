# CoinSwitchphp


If you are using ubuntu, you need php-curl or php7.0-curl.

In other flavours of Linux the equivalent is needed.

If you have doubts, or you think that something is dramatically wrong, contact me at
hidalgocespedes@gmail.com


# Tips using CoinSwitch API

## Concepts used

### Invoice

  - It must be used the section "PAYMENT GATEWAY API"
  - An invoice is a virtual bill for the amount of a service. It is a fixed amount or a price. The customer can use some currencies (not the full list) to pay. After the customer send the money, we receive it in our preferred coin (BTC ?).
  -  The payout (what we receives) can be done only in the following currencies: ['btc', 'eth', 'ltc', 'trx', 'doge', 'bchabc']
  - The source list contains currently 119 active coins.
  - Previous to receive a payment, an invoice MUST to be created. The key concept is “payment with a fixed price”.
  

### Orders

- The proper section is "CoinSwitch Dynamic API".
- An order is used to trade between currencies. Setting an order is the establishment of  the source and the destination currencies and an amount of money expressed in the first currency that will be received converted in the second currency.
- CoinSwitch estimates the amount in the destination currency that we will receive in our private address. The exchange rates could also be queried previously from CoinSwitch but it is not require at all. It doesn’t impact at all.
- The source list contains currently 356 active coins for orders.
- Not all pairs of currencies can be traded. It must be queried every time, every pair.

### Common concepts

- We have an API key and we have to fix the source IP for each operation of  creation and checking of each invoice/order. It can be the same for all of them but it cannot be changed before complete the op.
- We can check the status of an individual order/invoice using polling at anytime. To do that, we have to save/cache their Id’s. PULL approach.
- It is possible to use a CALLBACK url to be notified (PUSH approach). Specifying a callback is optional. Anyway the id has to be needed to match with the user’s wallet.
- There is a different list of coins allowed for each kind of operation. It is not mandatory to download the coin list every time, but it must be updated enough if cached. Some currencies might be temporarily disabled for various reasons and we have to filter out those when isActive: false.
- The customer is offered a different address each time.

## Creating objects

### Invoice
- There is a list of coins allowed to be used by the customer to pay invoices. It is the list CS provide us. Currently 119 coins.
- We fix the price. The customer select the currency B (curB).
- Once create the invoice the only paymentMode is the same than the currency we want to receive.
- The price must to be converted from curA (our tarifs) to curB before create the invoice. To do this properly we have to POST the customer’s currency after create the invoice (POST /v2/payment/invoice/:invoice_id/paymentmode/:currency)
- Finally we have to query the invoice status to get the address in curA and curB and the amounts in both currencies. Using them we can build a fancy form with a QR to easy the transfer.


There are limts we need to know:

- The list of coins in which we can receive the payout: ['btc', 'eth', 'ltc', 'trx', 'doge', 'bchabc']  (what we get)
- It seems that 0.4374217 BTC is the limit when curA is other than curB. Perhaps there are other limits converting or it depends on the verification level of our CoinSwitch account.


### Order

It is the same flow than invoices, but:

- https://api.coinswitch.co/v2/order to create the order
- It is designed to be used by the same person in both roles or by different persons sending and receiving.
- This operation let us give the customer the option to chose the amount to be transfer and the source currency.
- The rate doesn’t need to be consulted because the conversion is automatic.



