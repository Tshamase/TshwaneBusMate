<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <form action="https://sandbox.payfast.co.za/eng/process" method="post">
  <input type="hidden" name="merchant_id" value="YOUR_MERCHANT_ID">
  <input type="hidden" name="merchant_key" value="YOUR_MERCHANT_KEY">
  <input type="hidden" name="return_url" value="https://yourdomain.com/payfast/success">
  <input type="hidden" name="cancel_url" value="https://yourdomain.com/payfast/cancel">
  <input type="hidden" name="notify_url" value="https://yourdomain.com/payfast/notify">
  <input type="hidden" name="m_payment_id" value="ORDER123">
  <input type="hidden" name="amount" value="123.45">
  <input type="hidden" name="item_name" value="My Product">
  <input type="submit" value="Pay with PayFast">
</form>

</body>
</html>