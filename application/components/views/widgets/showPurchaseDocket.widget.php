<?php

    $purchaseDocket = $msg['docket'];
    

    if(count($purchaseDocket) > 0){

    $totalQty = $totalCost = 0;
    foreach($purchaseDocket as $docketItem){
        $totalQty += $docketItem->qty;
        $totalCost += $docketItem->price;
?>

        <tr id="row<?php echo $docketItem->id; ?>">
            <td><?php echo $docketItem->codeNo; ?></td>
            <td width="50%"><?php echo $docketItem->itemName; ?></td>
            <td><?php echo number_format($docketItem->qty); ?></td>
            <td><?php echo number_format($docketItem->price); ?></td>
            <td>

                <button class="btn btn-primary btn-raised" style="padding:5px 10px; margin-top:-3px" title="Delete Item" onclick="deleteDocketItem('<?php echo $docketItem->id; ?>')"><i class="fa fa-remove" style="color:white"></i></button>

            </td>

        </tr>

<?php } ?>


    <tr>
        <td colspan="2"><h4><strong>Total</strong></h4></td>

        <td><?php echo number_format($totalQty); ?></td>
        <td><?php echo number_format($totalCost); ?></td>
        <td>

            <button class="btn btn-warning btn-raised" style="padding:5px 10px; margin-top:-3px"  title="Clear Docket" id="clearDocketBtn" onclick="clearDocketAction()"><i class="fa fa-cogs" style="color:white"></i></button>

        </td>

    </tr>



<?php } ?>
