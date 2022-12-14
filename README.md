# glob gallery plugin
A plugin for listing and displaying images in a specific directory using PHP's glob.

## Usage
After installing and activating the plugin in WordPress, write the shortcode [image_lists] on the page you want to display.
The attribute values and default values that can be specified are as follows.

* outfile -> The number of images to display on one page.
  * default : 4
* expiration -> Time to cache in transient.
  * default : 60 * 60 * 24
* directory -> Specifies the directory where WordPress installed images are uploaded.
  * default : wp_upload_dir()['basedir'] . '/*/*/{*.jpg,*.jpeg,*.png,*.webp,*.gif}'
* img_size -> Please specify the image size specified in the img tag as an attribute.  
Specify 'origin' to use the original size.  
However, if the directory specification is a relative address, the image size is not obtained and the attribute is not specified.
  * default : width="150"

