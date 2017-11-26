<?php
    $baseUri = $registry->get('config')->get('baseUri');
    $docket = $msg['docket'];

    # check if error message exist
    if(isset($msg['errorMsg'])){
?>

        <div class="alert alert-icon alert-danger alert-dismissible fade in" role="alert">
            <button type="button" class="close" data-dismiss="alert"
                    aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <i class="mdi mdi-block-helper"></i>
            <strong>Error!</strong> <?php echo $msg['errorMsg']; ?>
        </div>

<?php

    }
?>

<input type="hidden" id="docketCountHidden" value="<?php echo count($docket); ?>" />
<input type="hidden" id="transId" value="<?php echo $msg['transId']; ?>" />

<?php

$total = $subTotal = $totalQty = $totalPrice = $totalDiscount = 0;

 if(count($docket) > 0){

    foreach($docket as $docketItem){
        $totalQty += $docketItem->qty;
        $totalPrice += $docketItem->price;
        $totalDiscount += $docketItem->discount;
        $total += $docketItem->total;
        $subTotal += ($docketItem->qty * $docketItem->price);

        $stockItem = new StockItem(StockItem::fetchIdByCodeNo($docketItem->codeNo));
?>

        <tr id="row<?php echo $docketItem->id; ?>">
            <td width="15%"><?php echo $docketItem->codeNo; ?></td>
            <td width="45%"><?php echo $stockItem->get('name'); ?></td>
            <td><?php echo number_format($docketItem->qty); ?></td>
            <td><?php echo number_format($docketItem->price); ?></td>
            <td><?php echo number_format($docketItem->discount); ?></td>
            <td><?php echo number_format($docketItem->total); ?></td>
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

        <button class="btn btn-warning btn-raised" style="padding:5px 10px; margin-top:-3px"  title="Clear Docket" id="clearDocketBtn" onclick="clearDocketAction()"><i class="fa fa-cogs" style="color:white"></i></button>

    </td>

</tr>

<?php } ?>


<input type="hidden" id="subTotalHidden" value="<?php echo $subTotal; ?>" />
<input type="hidden" id="totalDiscountHidden" value="<?php echo $totalDiscount; ?>" />
<input type="hidden" id="grandTotalHidden" value="<?php echo $total; ?>" />
