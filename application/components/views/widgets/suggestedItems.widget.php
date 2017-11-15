<?php

  $baseUri = $registry->get('config')->get('baseUri');

  if(count($msg['suggestions']) > 0) {

      # loop tru each suggestion
      foreach ( $msg[ 'suggestions' ] as $row ) {

          # fetch last purchase Date
          $lastPurchaseDetails = StockItem::fetchLastPurchaseDetails($row->codeNo);
          $lastPurchaseDate = $lastPurchaseDetails === false ? '' : changeDateFormat($lastPurchaseDetails->date, 'Y-m-d', 'd M Y');
          $lastCostPrice = $lastPurchaseDetails === false ? 0 : $lastPurchaseDetails->rate;

          # fetch last sold details
          $lastSoldDetails = StockItem::fetchLastSoldDetails($row->codeNo);
          $lastSoldDate = $lastSoldDetails === false ? '' : changeDateFormat($lastSoldDetails->date, 'Y-m-d', 'd M Y');

  ?>
              <a onclick="setItem({
                              'name' : '<?php echo $row->name; ?>', 'codeNo' : '<?php echo $row->codeNo; ?>', 'costPrice' : '<?php echo $row->costPrice; ?>', 'wholesalePrice' : '<?php echo $row->wholesalePrice; ?>', 'retailPrice' : '<?php echo $row->retailPrice; ?>', 'qtyInStock' : '<?php echo number_format($row->qty); ?>', 'tax' : '<?php echo $row->tax; ?>', 'groupId': '<?php echo $row->groupId; ?>', 'lastPurchaseDate' : '<?php echo $lastPurchaseDate; ?>', 'lastSoldDate' : '<?php echo $lastSoldDate; ?>', 'lastCostPrice' : '<?php echo number_format($lastCostPrice); ?>'
                            })">
                  <p class="suggestion"><?php echo ucwords($row->name); ?></p>
              </a>

   <?php

      }

  }else{

      if($msg['fetchEmpty'] == 'true'){

?>

    <p class="no-suggestion">No result Found for <span class="highlight"><?php echo $msg['searchQuery']; ?></span> </p>

<?php  }else{
    echo '';
} } ?>
