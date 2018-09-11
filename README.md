# Stats API Plugin

The **Stats API** Plugin is for [Grav CMS](http://github.com/getgrav/grav), and allows you to get Admin-plugin Statistics via a simple REST API.

## Installation

To install this plugin, just download the zip version of this repository and unzip it under `/your/site/grav/user/plugins`. Then, rename the folder to `stats-api`. You can find these files on [GitHub](https://github.com/OleVik/grav-plugin-stats-api) or via [GetGrav.org](http://getgrav.org/downloads/plugins#extras).

You should now have all the plugin files under

    /your/site/grav/user/plugins/stats-api
	
> NOTE: This plugin is a modular component for Grav which requires [Grav](http://github.com/getgrav/grav) and the [Error](https://github.com/getgrav/grav-plugin-error) and [Problems](https://github.com/getgrav/grav-plugin-problems) to operate.

### Admin Plugin

If you use the admin plugin, you can install directly through the admin plugin by browsing the `Plugins` tab and clicking on the `Add` button.

## Configuration

Before configuring this plugin, you should copy the `user/plugins/stats-api/stats-api.yaml` to `user/config/plugins/stats-api.yaml` and only edit that copy.

Here is the default configuration and an explanation of available options:

```yaml
enabled: true
route: /stats-api
token: NVrzcU3h2hXuhZCJYZ6KUP29
```

Note that if you use the admin plugin, a file with your configuration, and named stats-api.yaml will be saved in the `user/config/plugins/` folder once the configuration is saved in the admin.

## Usage

If you need the API to be accessible at a different route, change it in the plugin's options. Be sure to prepend a `/` to it. For any site you use the plugin on, use a [unique 24-character, alphanumerical token](https://www.random.org/passwords/?num=5&len=24&format=html&rnd=new), set in the plugin's options.

To get the [statistics](https://learn.getgrav.org/admin-panel/dashboard#maintenance-and-statistics) from a site, visit it's URL and append the route followed by `/daily`, `/monthly`, `/totals`, or `/visitors`, as well as `?AUTH_TOKEN=NVrzcU3h2hXuhZCJYZ6KUP29`. Replace the get-parameters value with the token for the site, a full example URL looks like this: `http://example.com/stats-api/daily?AUTH_TOKEN=NVrzcU3h2hXuhZCJYZ6KUP29`.

### Going further

If you want to get the statistics of several sites at once, this should be easy for any task runner to accomplish. For an example, using PHP (7.1) with Symfony's libraries, see [Advanced usage](ADVANCED.md).