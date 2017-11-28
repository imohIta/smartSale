<?php

    $receiptData = $msg['receiptData'];
?>

<style>

    #wrapper{
        width:400px;
        font-family: "Calibri";
        font-size:16pt;
        border:1px solid #fff;
        margin:4px;
        text-align:center;
    }

    table {
        border-collapse: collapse;
    }

    table, th, td {
        border: 1px solid black;
        width:100%; font-size:16pt;
        padding:4px; text-align: center;
    }

</style>

<div id="wrapper" >

    <div class="warper container-fluid" >

        <div>
            <h3 style="font-size:20pt"><strong>Prelizz </strong><small>Perfumery.</small></h3>
            <p style="font-size:18pt;line-height: 0.2em; text-align: center;"><strong>Calabar Discount Mall</strong><p>
            <p style="font-size:18pt;line-height: 0.2em; text-align: center;"><strong>Plot 74, Marian Road, Opp Glo office</strong><p>
            <p style="font-size:18pt;line-height: 0.6em; text-align: center; padding-bottom:20px; border-bottom:1px solid #000;"><strong>Cross River State</strong></p>

            <div style="float: left; width:50%; padding:0; margin:0">
                <p style="text-align:left;"><strong>Date :</strong> <?php echo $receiptData['date']; ?></p>
            </div>

            <div style="float: left; width:50%; padding:0; margin:0">
                <p style="text-align:right;"><strong>Time : </strong> <?php echo timeToString($receiptData['time']); ?></p>
            </div>

            <?php if($receiptData['customerName'] != ''){ ?>

                <div style="float: left; width:50%; padding:0; margin:0">
                    <p style="text-align:left;"><strong>Cust. Name :</strong> <?php echo ucwords($receiptData['customerName']); ?></p>
                </div>

                <div style="float: left; width:50%; padding:0; margin:0">
                    <p style="text-align:right;"><strong>Cust. Addr. : </strong> <?php echo $receiptData['customerAddr']; ?></p>
                </div>

            <?php } ?>

            <br style="clear: both;" />

            <table>
                <thead>
                    <tr>
                        <td colspan="5"><h3 style="text-align:center; font-size:18pt; margin-top:15px"><strong>INVIOCE ( <?php echo $receiptData['invoiceNo']; ?> )</strong></h3></td>
                    </tr>
                    <tr>
                        <td style="font-size:16pt; width:10%"><strong>QTY</strong></td>
                        <td style="font-size:16pt; width:50%"><strong>DESC</strong></td>
                        <td style="font-size:16pt; width:15%"><strong>RATE</strong></td>
                        <td style="font-size:16pt; width:10%"><strong>DISC</strong></td>
                        <td style="font-size:16pt; width:15%"><strong>AMT</strong></td>
                    </tr>
                </thead>

                <tbody>

                    <?php
                        $total = $totalQty =  0;
                        foreach($receiptData['docket'] as $itemSold){

                            $totalQty += $itemSold->qty;
                            $total += $itemSold->total;

                            $amt = $itemSold->price * $itemSold->qty;
                            $stockItem = new StockItem($registry->get('stockDb')->fetchStockByCodeNo($itemSold->codeNo));
                    ?>
                            <tr>
                                <td style="font-size:16pt; width:10%"><?php echo number_format($itemSold->qty); ?></td>
                                <td style="font-size:16pt; width:50%"><?php echo ucwords($stockItem->get('name')); ?></td>
                                <td style="font-size:16pt; width:15%"><?php echo number_format($itemSold->price); ?></td>
                                <td style="font-size:16pt; width:10%"><?php echo number_format($itemSold->discount); ?></td>
                                <td style="font-size:16pt; width:15%"><?php echo number_format($itemSold->total); ?></td>
                            </tr>
                    <?php  } ?>


                    <tr>
                        <td style="font-size:16pt" colspan="2"><strong>Total Qty : <?php echo number_format($totalQty); ?></strong></td>
                        <td colspan="3" style="font-size:16pt"><strong>Total Amt: <?php echo number_format($total); ?></strong></td>
                    </tr>

                </tbody>

            </table>


            <br style="clear:both" />



        </div>


        <p style="line-height:0.2em">No refunds or exchange of perfume after sale<p>

            <hr />

        <p style="line-height:0.2em">SmartSale<p>
        <p style="line-height:0.2em"><small>...Make sales with ease</small></p>
        <p style="line-height:0.2em"><small>www.oxygyn.xyz</small></p>

    </div>

</div>
