# Amazon Product Advertising Service Plugin for CakePHP #

The AmazonPAS Plugin enables the usage of the [Amazon Product Advertising Service API](http://docs.amazonwebservices.com/AWSECommerceService/2010-11-01/DG/) as a Cake PHP plugin.

## Amazon Requirements ##

You will require the following 3 keys from Amazon to be able to use the plugin effectively.

### Amazon Associate ID ###
This is issued by Amazon when you sign up as an associate. Being an associate allows you to earn some money from any sales Amazon make which are referred to them by you.

### Amazon Web Service Key and Secret Key ###
These are issued by Amazon when you [sign up for AWS](http://aws.amazon.com)

## Installation ##

The plugin is pretty easy to set up, all you need to do is to copy the Amazon to your application as the app/Plugin/Amazon folder.

You will need to set the 3 Amazon values detailed above in the file Plugin/Amazon/Config/aws.php and copy that file to your app/Config directory and you are ready to go.

## How to use it ##
The AmazonPAS interface is a controller component, so all you have to do is include it in your controller. 

    class MyController extends AppController {
        public $components = array( 'Amazon.Pas' );
        ...
    }

    to access the methods simply reference $this->Pas->method() in the controller.

## Methods ##
The methods within the component are well documented and mirror the services offered by AmazonPAS and will therefore not be documented again here. It MUST be noted however that this component DOES NOT MAKE REQUESTS TO AMAZON. 

Each query method returns a string which contains the properly constructed URL of the request. It is then up to you to send that request. In this way, you may use Curl, HttpRequest or my own HttpRequest extension classes to make the request itself and process the response. 

## License ##

Copyright 2012, [Stephen Found](http://www.dnsmedia.co.uk)

Licensed under [The MIT License](http://www.opensource.org/licenses/mit-license.php)<br/>
Redistributions of files must retain the above copyright notice.

## Copyright ###

Copyright 2012<br/>
[Stephen Found](http://www.dnsmedia.co.uk)<br/>
http://dnsmedia.co.uk<br/>

