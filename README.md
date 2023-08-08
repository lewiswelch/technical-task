# Technical Task

Setup:
* Install the latest versions of WooCommerce & the Twenty-Twenty-Three theme
* Run through WooCommerce setup
* Install woocommerce-test-theme and woocommerce-test-plugin and activate both


This plugin has had the majority of the functionality stripped out, leaving the main plugin file `woocommerce-test.php` and some related includes, plus a template file at `templates/dummy-template.php` 

The theme contains two template files, `woocommerce/dummy-template.php` (originally copied from our plugin) and `woocommerce/single-product.php` (originally copied from woocommerce). Both are out of date. You will notice that WooCommerce provides a notification to the end user that the default WooCommerce template is out of date.

![Template notification screenshot](/Screenshot.png?raw=true)

The task is to add functionality to the test plugin to provide a similar notice to users regarding our own template file being out of date. Please fork this repository and submit your completed code via pull request. 

Any accompanying notes or video explanation/screencast explaining the solution can be added to this readme in the pull request.