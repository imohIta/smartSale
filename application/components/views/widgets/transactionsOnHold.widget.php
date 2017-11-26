<?php
    foreach ($msg['transactionsOnHold'] as $value) {
        # code...
 ?>

 <tr>
     <td><?php echo changeDateFormat($value->date); ?></td>
     <td><a href="javascript:viod(0)" title="Recall Transaction" onclick="recallTransaction('<?php echo $value->transId; ?>')"><?php echo $value->transId; ?></a></td>
 </tr>

 <?php } ?>
