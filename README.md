# azure-uploads-tab
[WIP] List azure blob storage files as a tab in the media selector 🗃

## Installation

Install the plugin via [Composer](https://getcomposer.org/): 

```sh
$ composer require regionhalland/azure-uploads-tab
```

Activate the plugin in the Wordpress admin panel. Go to **Settings → Azure Uploads Tab** and add your credentials.

You can find the tab by going to **Pages → [Any Page] → Add media → [Tab Name]**.

## Development

Clone a copy of the plugin into your `plugins` folder and install dependencies via [Composer](https://getcomposer.org/) and [Yarn](https://yarnpkg.com/):

```sh
$ git clone https://github.com/RegionHalland/azure-uploads-tab.git
$ cd azure-uploads-tab/
$ composer install && yarn
```

Watch for changes and compile Javascript and SCSS-files during development:

```sh
$ yarn start
```

If you make any changes to Javascript or SCSS-files, make sure to build minified files before commiting any changes:

```sh
$ yarn build
```

## Todo 🎈
- [ ] Add Azure search API to index our blobs
- [ ] Creating working UI for filtering and searching for blob files.