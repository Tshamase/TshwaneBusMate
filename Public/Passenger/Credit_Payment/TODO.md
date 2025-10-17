# Integration Plan for PayFast Payment Files

## Steps Completed

1. **Update payment_gateway.php** ✅
   - Store totalAmount in session
   - Redirect to checkout.php instead of simulating payment

2. **Update checkout.php** ✅
   - Retrieve amount from session
   - Set order_id to transact_id
   - Fix URLs to relative paths (success.php, cancel.php, payfast_notify.php)
   - Remove passphrase if not used

3. **Update success.php** ✅
   - Add PHP code to update balance in transactions table for userId=1, add the amount

4. **Update payfast_notify.php** ✅
   - On payment COMPLETE, update balance in transactions table

5. **Update credit_wallet.html** ✅
   - Define $historyUrl to "Credit history.php"

6. **Verify database consistency** ✅
   - Changed Credit Wallet.php and Credit history.php to use db_payment.php instead of Database.php
