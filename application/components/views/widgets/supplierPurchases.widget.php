<div class="form-group pull-right">
    <label for="filter" style="padding-top: 5px; font-size:14px"><strong>Search Table:</strong></label>
    <input id="filter"  style="font-size:14px; padding-left:7px" type="text" class="form-control rounded input-sm w-md mb-10 inline-block"/>
</div>
<table id="searchTextResults" data-filter="#filter" data-page-size="40" class="footable table table-custom">
    <thead>
        <tr>
            <th>Bar Code</th>
            <th width="40%">Details</th>
            <th>Qty</th>
            <th>Amount</th>
            <th>Total</th>
        </tr>
    </thead>
    <tbody>

        <?php
            $grandTotal = 0;
            foreach($msg['purchases'] as $groupedPurchase){

        ?>
                <tr style="background:#F9F9F9; margin-top:10px">
                    <td colspan="6">
                        <p style="color:#E56B6B">
                            <span>Purchase No : <?php echo $groupedPurchase->transId; ?></span>
                            <span style="margin-left:10px"><?php echo changeDateFormat($groupedPurchase->date); ?></span>
                    </td>
                </tr>

                <?php
                    # fetch all purchases with this purchase No
                    $allPurchasesWithThisId = $registry->get('db')->query('select * from purchases where transId = :transId', array('transId' => $groupedPurchase->transId), true);

                    $totalAmount = $totalQty = 0;
                    foreach ($allPurchasesWithThisId as $purchase) {

                        $grandTotal += ( $purchase->rate * $purchase->qty );
                        $totalAmount += ( $purchase->rate * $purchase->qty );
                        $totalQty += $purchase->qty;

                        $item = new StockItem(StockItem::fetchIdByCodeNo($purchase->codeNo));

                 ?>

                 <tr style="background:#fff">
                     <td><?php echo $item->get('codeNo'); ?></td>
                     <td><?php echo $item->get('name'); ?></td>
                     <td><?php echo number_format($purchase->qty); ?></td>
                     <td><?php echo number_format($purchase->rate); ?></td>
                     <td><?php echo number_format($purchase->qty * $purchase->rate); ?></td>
                 </tr>


        <?php } ?>

        <tr>
            <td colspan="2"><strong>Total</strong></td>
            <td><strong><?php echo number_format($totalQty); ?></strong></td>
            <td>&nbsp;</td>
            <td><strong><?php echo number_format($totalAmount); ?></strong></td>
        </tr>

        <?php } ?>

        <tr>
            <td colspan="4" class="text-left"><h4 style="padding-right:20px"><strong>Grand Total : </strong></h4></td>
            <td><h4><strong>=N= <?php echo number_format($grandTotal); ?></h4></td>
        </tr>



    </tbody>

    <tfoot class="hide-if-no-paging">
        <tr>

            <td colspan="6" class="text-right"><ul class="pagination">
                </ul></td>
        </tr>
    </tfoot>
</table>
