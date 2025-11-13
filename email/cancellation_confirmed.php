<?php include "../email/_header.php"; ?>

<h4 style="margin:0px"><strong>Hotel Cancellation Confirmed</strong></h4>
<p style="margin:10px 0 0px">
    Your hotel booking has been successfully cancelled.
</p>
<p style="margin:0px 0 10px">
    The refund (if applicable) will be processed as per the cancellation policy.
</p>

<a href="<?=$content?>" style="text-decoration:none;background: #0079ff; color: #fff; padding: 14px 18px; margin-top: 20px; display: inline-block; border-radius: 4px; font-weight: bold;">
    View Invoice
</a>

<p style="margin-top:15px;">
    Thank you for using our service. We hope to assist you again in the future.
</p>

<?php include "../email/_footer.php"; ?>
