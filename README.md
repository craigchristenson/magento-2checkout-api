### _[Signup free with 2Checkout and start selling!](https://www.2checkout.com/referral?r=git2co)_

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
11. Select **Complete** under **Order Status**.
12. Select **Yes** under **Enabled** for 2Checkout API and 2Checkout PayPal Direct.
13. Save your changes.


#### 2Checkout Settings

1. Sign in to your 2Checkout account.
2. Click the **Account** tab and **Site Management** subcategory.
3. Under **Direct Return** select **Header Redirect** or **Given links back to my website**.
4. Enter your **Secret Word**._(Must be the same value entered in your Magento admin.)_
5. Set the **Approved URL** to https://www.yourdomain.com/index.php/tco/redirect/success _(Replace https://www.yourstore.com with the actual URL to your store.)_
6. Click **Save Changes**.

Please feel free to contact 2Checkout directly with any integration questions.
