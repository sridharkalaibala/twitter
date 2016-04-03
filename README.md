# Twitter API

Introduction
---

REST api to get hourly tweet counts for given user. 
So that, we can identify what hour of the day they are most active. 

Installation
---

- Download as zip and extract into your web directory Ex. /opt/lamp/htdocs/twitter
- Edit index.php and enter your twitter api credentials. Lines 8 - 11. Ex. $['TWITTER_CONSUMER_KEY'] , secret and etc. 
  Note:- If your app credentials is not valid, page will through 500 error. 
- Installation completed. Now you can access your API Endpoints. 
- You can install through composer also. Just copy only index.php, composer.json and .htaccess files. 
  composer update and configure app credentials. That's all. 

Demo
---

- Endpoint 1. http://sridharb.com/twitter/
- Endpoint 2. http://sridharb.com/twitter/hello/bigcommerce
- Endpoint 3. http://sridharb.com/twitter/histogram/narendramodi


Unit Test
---

- There is no unitest written for this.

Known issues / limits
---

- OpenSSL, CURL extensions are required for twitter connection.

