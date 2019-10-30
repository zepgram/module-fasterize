# Fasterize #

Manage Fasterize cache from Magento admin and auto-trigger cache flush when it's necessary by using the Fasterize API service.

## Installation
```
composer require zepgram/module-fasterize
bin/magento module:enable Zepgram_Fasterize
bin/magento setup:upgrade
```

## Configuration
Enter your Fasterize configuration under Stores > Configuration > Zepgram
![image](https://user-images.githubusercontent.com/16258478/67890243-a377be80-fb50-11e9-85f9-8b261d061030.png)

Then, at least one store must be enabled to access the Fasterize cache manager
![image](https://user-images.githubusercontent.com/16258478/67890231-9c50b080-fb50-11e9-82b9-89d3ce8938b2.png)

Flush action is explicitly rendered
![image](https://user-images.githubusercontent.com/16258478/67903759-bbf5d200-fb6c-11e9-9522-20a598c12e67.png)

Control access to the cache through Magento ACL
![image](https://user-images.githubusercontent.com/16258478/67896147-10dd1c80-fb5c-11e9-9dcf-04b033918bc9.png)

## Events

Fasterize flush will be automatically triggered on those native Magento events:

``clean_media_cache_after``<br>
``clean_catalog_images_cache_after``<br>
``assign_theme_to_stores_after``<br>
``adminhtml_cache_refresh_type``<br>
``adminhtml_cache_flush_all``<br>

## Logs
Every request are logged and can be found under /var/log/zepgram/fasterize.log