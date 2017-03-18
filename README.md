# WP Context Switch
> Work on multiple plugins/themes? Create/switch between multiple WordPress contexts (installations) from within the WP Dashboard.

## About WP Context Switch
I'm always creating new plugins for clients - sometimes more than one at a time. Having multiple in-progress plugins/themes in one WordPress install was a headache.

To switch from one plugin to another, I'd sometimes need to deactivate one or more plugins/themes, as well as set up the data again. 

One alternative was to create a new WP instance with Docker - but this meant multiple wp-content folders to deal with.

Another was to use wp multisite - but this comes with it's own set of headaches.

What I really needed was a simple way to switch between development contexts when needed, and the ability to create them on the fly.


WP Context Switch was the result

![](https://raw.githubusercontent.com/mzalewski/wpcontextswitch/master/docs/recording.gif)


To install, just download this repository [here](https://github.com/mzalewski/wpcontextswitch/archive/master.zip) and install as a normal WP plugin.

Once activated, it will make some changes to your WordPress installation:
1 - It will create an "env-config" folder in your WP root directory. This folder contains the PHP script that will load the Context config file and set the current context.
2 - It will modify your wp-config.php and inject a line that will include the env-config loader script. Your current wp-config.php file will be backed up. (Note: Highly recommended that make your own backup)
3 - Finally, it will install an mu-plugin to add the menu items to the WP Admin bar

### Warning: DO NOT USE ON PRODUCTION/LIVE SITES 

This is not intended to be used on live or public-facing sites. There is a chance it will break your current WordPress install so not recommended for people who are not comfortable modifying WordPress/PHP/config files.
