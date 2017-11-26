<?php
    $baseUri = $registry->get('config')->get('baseUri');
    $docket = $msg['docket'];

    if($msg['responseType'] == 'json'){

        echo json_encode($msg['docket']);

    }else{


        if(count($docket) > 0){
    ?>

    <!-- Hold docket count to make sure sale is not completed if docket is empty -->
    <input type="hidden" id="docketCount" value="<?php echo count($docket); ?>" />

    <!-- hold count on transactions put on-hold -->
    <input type="hidden" id="onHoldTransactionsCount" value="<?php echo 1; ?>" />

    <?php


        $total = $totalQty = $totalPrice = $totalDiscount = $subTotal = 0;
        foreach($docket as $docketItem){
            $totalQty += $docketItem->qty;
            $totalPrice += $docketItem->price;
            $totalDiscount += $docketItem->discount;
            $subTotal += ( $docketItem->qty * $docketItem->price);
            $total += $docketItem->amount;

            $stockItem = new StockItem(StockItem::fetchIdByCodeNo($docketItem->codeNo));
    ?>

            <tr id="row<?php echo $docketItem->id; ?>">
                <td width="15%"><?php echo $docketItem->codeNo; ?></td>
                <td width="45%"><?php echo $stockItem->get('name'); ?></td>
                <td><?php echo number_format($docketItem->qty); ?></td>
                <td><?php echo number_format($docketItem->price); ?></td>
                <td><?php echo number_format($docketItem->discount); ?></td>
                <td><?php echo number_format($docketItem->amount); ?></td>
                <td>

                    <button class="btn btn-primary btn-raised" style="padding:5px 10px; margin-top:-3px" title="Delete Item" onclick="deleteDocketItem('<?php echo $docketItem->id; ?>')"><i class="fa fa-remove" style="color:white"></i></button>

                </td>

            </tr>

    <?php } ?>


        <tr>
            <td colspan="2"><h4><strong>Total</strong></h4></td>

            <td><strong><?php echo number_format($totalQty); ?></strong></td>
            <td><strong><?php echo number_format($totalPrice); ?></strong></td>
            <td><strong><?php echo number_format($totalDiscount); ?></strong></td>
            <td><strong><?php echo number_format($total); ?></strong></td>
            <td>

                <button class="btn btn-warning btn-raised" style="padding:5px 10px; margin-top:-3px"  title="Clear Docket" id="clearDocketBtn"><i class="fa fa-cogs" style="color:white"></i></button>

            </td>

        </tr>


    <?php
        }else{
            echo 'not found';
        }
    }
?>
