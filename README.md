### _[Signup free with 2Checkout and start selling!](https://www.2checkout.com/signup)_

### Integrate Magento with 2Checkout Payment API (Supports PayPal Direct)
----------------------------------------

### 2Checkout Payment API/PayPal Direct Setup

#### Magento Settings

1. Download the 2Checkout payment module from https://github.com/craigchristenson/magento-2checkout-api
2. Upload the files to your Magento directory.
3. Sign in to your Magento admin.
4. Flush your Magento cache under **System**->**Cache Management** and reindex all templates under **System**->**Index Management**.
5. Navigate to Payment Methods under **System**->**Configuration**->**Payment
   Methods** and open **2Checkout API**.
6. Enter your **Seller ID**. _(2Checkout Account Number)_
7. Enter your **Publishable Key**. _(2Checkout Publishable Key)_
8. Enter your **Private Key**. _(2Checkout Private Key)_
9. Enter your **Secret Word** _(Must be the same value entered on your 2Checkout Site Management page.)_
10. Select **No** under **Sandbox Mode**. _(Unless you are tesing in the 2Checkout Sandbox)_
11. (Optional) Enter your 2Checkout Admin API username and password (this is only required if you use Live Refunds functionality) If you have not yet created an Admin API user, please login to your 2Checkout account, and do so. This username and password can then be entered in API username and password fields in Magento.
12. (Optional) Select whether or not your want to enable Live Refunds. (This uses the 2Checkout API to issue a live refund to the customer when you issue a credit memo in Magento.)
13. Select **Complete** under **Order Status**.
14. Select **Yes** under **Enabled** for 2Checkout API and 2Checkout PayPal Direct.
15. Save your changes.


#### 2Checkout Settings

1. Sign in to your 2Checkout account.
2. Click the **Account** tab and **Site Management** subcategory.
3. Under **Direct Return** select **Header Redirect** or **Given links back to my website**.
4. Enter your **Secret Word**._(Must be the same value entered in your Magento admin.)_
5. Set the **Approved URL** to https://www.yourdomain.com/index.php/tco/redirect/success _(Replace https://www.yourstore.com with the actual URL to your store.)_
6. Click **Save Changes**.
7. Click the Create Username link and create a new username with API Access and API Updating selected for the Access type. More information [here](http://help.2checkout.com/articles/FAQ/How-to-create-an-API-only-Username/)

Please feel free to contact 2Checkout directly with any integration questions.
