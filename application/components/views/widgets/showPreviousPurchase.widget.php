<?php
    $baseUri = $registry->get('config')->get('baseUri');
    $docket = $msg['docket'];

    if($msg['responseType'] == 'json'){

        echo json_encode($msg['docket']);
        
    }else{


        if(count($docket) > 0){

        $totalQty = $totalCost = 0;
        foreach($docket as $docketItem){

            $stockItem = new StockItem($registry->get('stockDb')->fetchStockByCodeNo($docketItem->codeNo));
            $totalQty += $docketItem->qty;
            $totalCost += $docketItem->rate;
        ?>

            <tr>
                <td><?php echo $docketItem->codeNo; ?></td>
                <td width="50%"><?php echo $stockItem->get('name'); ?></td>
                <td><?php echo number_format($docketItem->qty); ?></td>
                <td><?php echo number_format($docketItem->rate); ?></td>
                <td>

                </td>

            </tr>

    <?php } ?>


        <tr>
            <td colspan="2"><h4><strong>Total</strong></h4></td>

            <td><?php echo number_format($totalQty); ?></td>
            <td><?php echo number_format($totalCost); ?></td>
            <td>

            </td>

        </tr>



<?php

        }else{
            echo 'not found';
        }
    }
?>
