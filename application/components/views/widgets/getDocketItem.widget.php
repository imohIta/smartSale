<?php
    $item = $msg['Item'];

    if($item !== false){

        # fetch last purchase Date
        $lastPurchaseDetails = StockItem::fetchLastPurchaseDetails($item->codeNo);
        $lastPurchaseDate = $lastPurchaseDetails === false ? '' : changeDateFormat($lastPurchaseDetails->date, 'Y-m-d', 'd M Y');
        $lastCostPrice = $lastPurchaseDetails === false ? 0 : $lastPurchaseDetails->rate;

        # fetch last sold details
        $lastSoldDetails = StockItem::fetchLastSoldDetails($item->codeNo);
        $lastSoldDate = $lastSoldDetails === false ? '' : changeDateFormat($lastSoldDetails->date, 'Y-m-d', 'd M Y');

        echo json_encode(array(
            'codeNo' => $item->codeNo,
            'name' => $item->name,
            'wholesalePrice'    => $item->wholesalePrice,
            'costPrice'      => $item->costPrice,
            'retailPrice' => $item->retailPrice,
            'qtyInStock' => number_format($item->qty),
            'tax' => $item->tax,
            'groupId' => $item->groupId,
            'lastSoldDate' => $lastSoldDate,
            'lastPurchaseDate' => $lastPurchaseDate,
            'lastCostPrice' => number_format($lastCostPrice)

        ));

    }else{
        echo '';
    }
