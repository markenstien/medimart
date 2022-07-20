<?php build('content') ?>
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">Add Stocks</h4>
            <?php Flash::show()?>
        </div>

        <div class="card-body">
            <?php echo $stock_form->start()?>
            <?php csrf()?>
            <?php echo $stock_form->getFormItems()?>
            <?php echo $stock_form->end()?>
        </div>
    </div>
<?php endbuild()?>
<?php loadTo()?>