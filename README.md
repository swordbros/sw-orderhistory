# sw-orderhistory

## Composer 

```
Add this line your web site composser.json 
    "require": {
        ...
        "swordbros/sw-orderhistory": "^1.0"
    },
```
### Or 

```
// This assumes that you have composer installed globally
composer require swordbros/sw-orderhistory
```

## change templates

### resources/views/app.blade.php append code 
<script type="text/javascript" src="/packages/swordbros/common/js/swordbros.js?_v=<?=time()?>"></script>

### resources/views/list-body-standard.php
<div class="history-list">
<?php  echo \Aimeos\MShop\Swordbros\Orderhistory\Helper::get_order_table($this->get( 'listsOrderItems', [] ) , $this); ?>
  
### resources/views/order-body-standard.php
<?php  echo \Aimeos\MShop\Swordbros\Orderhistory\Helper::get_cancelorder_button($this->orderItem , $this); ?>
