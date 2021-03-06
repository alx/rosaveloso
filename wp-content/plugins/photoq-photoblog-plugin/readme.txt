=== PhotoQ Photoblog Plugin ===
Contributors: whoismanu
Donate link: http://www.whoismanu.com/blog/
Tags: photoq, images, photos, photoblog, photo, upload, thumbnail, photo, picture, pictures, post, automatic, lightbox, admin, flash, photoq photoblog
Requires at least: 2.8.1
Tested up to: 2.8.2
Stable tag: 1.7

Turns WordPress into a photoblog. Allows queue based photo management, batch uploads and automatic 
posting of photos at regular intervals.

== Description ==

PhotoQ is a WordPress plugin that turns your blog into a photoblog. If you have a lot of pictures to post, 
PhotoQ is your ideal companion. With PhotoQ you can mass upload several photos at the same time thanks to 
its batch upload functionality. PhotoQ places uploaded photos in a queue which gives you a convenient way 
to manage photos to be posted. The plugin then gives you the possibility to have the top of the queue 
automatically posted at a given interval. PhotoQ was designed to automate and simplify your photo posting 
process as much as possible. It takes away the hassle of browsing uploaded image files and embedding them 
into posts: You simply upload your photo to the queue and enter desired information about the photo. PhotoQ 
then automatically generates the post based on this information.

To make a long story short, WordPress + PhotoQ = Photoblog. With the latest PhotoQ version this formula is more 
valid than ever: PhotoQ v1.5 brings tons of new features to your photoblog. It now includes EXIF support, 
watermarking, batch editing and multiple image sizes in addition to all the features you have come to love from 
earlier versions.   

**Feature list:**

* Convenient queue-based photo management
* Batch uploading of photos to your photoblog
* Hassle-free, fully automated posting of photo posts
* Support for EXIF metadata, automatic post tag creation from EXIF data
* Photo Watermarking to protect your photos
* Possibility to add custom metadata to photo posts
* Automatic generation of thumbnails and alternative image sizes
* Updating of all your posted photos with only a few clicks
* Automatic posting through cronjobs
* Integration with Lightbox, Shutter Reloaded and similar libraries/plugins


== Installation ==

** Upgrading **

Before upgrading please check [the PhotoQ Blog](http://www.whoismanu.com/blog/ "Whoismanu Blog") for specific upgrading instructions/warnings.
Also, whenever you upgrade to a new version it is advised that you backup your database and files just like you do for a WordPress upgrade.

** Fresh Installation **

1. Unzip the downloaded file, you should end up with a folder called "photoq-photoblog-plugin".
2. Upload the "photoq-photoblog-plugin" folder to your "plugin" directory (wp-content/plugins).
3. If you plan to use the automatic posting capability, move the file "photoq-photoblog-plugin/wimpq-cronpost.php" to the same directory as your wp-config.php file.
4. You now have to setup a directory on your web server where your image files will be stored (called 
"imgdir" directory from here on). By default this is the directory "wp-content". If you do not stick 
to the default one you have to create your directory now.
5. Make sure that the file permissions of the "imgdir" directory are such that the plugin is allowed 
to write to it (otherwise, uploaded photos cannot be stored).
6. PhotoQ also needs a "cache" directory to store temporary files. This is the directory 
"wp-content/photoQCache". If PhotoQ has the permissions to write to "wp-content" the "cache directory will be created automatically. Otherwise you have to create the "photoQCache" directory now and make sure that the file permissions are such that the plugin is allowed to write to it.
7. If your web hosting provider enabled the mod_security Apache module on your web server, you need 
to add the following directives to your .htaccess file in order for batch uploads to work:
&lt;IfModule mod_security.c&gt;
SecFilterEngine Off
SecFilterScanPOST Off
&lt;/IfModule&gt;
See the 
[Troubleshooting section of my homepage](http://www.whoismanu.com/photoq-wordpress-photoblog-plugin/#troubles "PhotoQ Troubleshooting") 
for more information.
8. You are almost done. Just go to the "Plugins" Wordpress admin panel and activate the Photoq 
plugin.

For the long version explaining all the features, for now please check 
[my homepage](http://www.whoismanu.com/photoq-wordpress-photoblog-plugin/ "Home of PhotoQ").

== Frequently Asked Questions ==

= Where can I get answers to my questions regarding PhotoQ? =

* Full documentation can be found on [the PhotoQ Homepage](http://www.whoismanu.com/photoq-wordpress-photoblog-plugin/ "PhotoQ WordPress Photoblog Plugin") 
* For support, please visit [the PhotoQ Support Forum](http://www.whoismanu.com/forum/ "PhotoQ Support Forum")
* Latest news are found on [the PhotoQ Blog](http://www.whoismanu.com/blog/ "Whoismanu Blog")

= I really like PhotoQ, what can I do to support it? =

* You could start by giving it a nice rating on the very [page you are looking at right now](http://wordpress.org/extend/plugins/photoq-photoblog-plugin/ "PhotoQ on WordPress.org)
* Please spread the word and tell other people how great it is
* Link back to [the PhotoQ Homepage](http://www.whoismanu.com/photoq-wordpress-photoblog-plugin/ "PhotoQ WordPress Photoblog Plugin") 
* More ideas can be found [here](http://www.whoismanu.com/photoq-wordpress-photoblog-plugin/#help "Support PhotoQ")


== Screenshots ==

1. Batch upload process
2. Entering information for uploaded photos.
3. The queue. Reordering can be done by drag and drop.
4. Tons of options.


