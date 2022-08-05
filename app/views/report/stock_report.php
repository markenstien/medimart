<?php build('content') ?>
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">Sales Report</h4>
        </div>

        <div class="card-body">
            <div class="col-md-5">
                <?php
                    Form::open([
                        'method' => 'get',
                        'action' => ''
                    ])
                ?>
                    <?php echo $_formCommon->getRow('start_date')?>
                    <?php echo $_formCommon->getRow('end_date')?>
                    <?php echo $_formCommon->getRow('user_id')?>
                    <?php echo $_formCommon->get('submit')?>
                <?php Form::close()?>
            </div>
        </div>
        
        <div class="card-body">
            <div class="col-md-7">
                <div class="report_container">    
                    <section class="header">
                        <div class="text-center">
                            <h4>STOCK REPORT</h4>
                            <div><small>AS of 2022-07-31 : 10:19AM</small></div>
                            <div>Report By : <small><strong>Mark Angelo Gonzales(110010)</strong></small></div>
                        </div>
                    </section>
                    
                    <section class="summary">
                        <table class="table table-bordered">
                            <tr align="center">
                                <td colspan="5">HIGHEST STOCKS IN LEVEL</td>
                            </tr>
                            <tr>
                                <td>ITEM + SKU</td>
                                <td>MIN</td>
                                <td>MAX</td>
                                <td>STOCKS</td>
                                <td>LEVEL</td>
                            </tr>

                            <tr align="center">
                                <td colspan="5">HIGHEST STOCKS IN LEVEL</td>
                            </tr>
                            <tr>
                                <td>ITEM + SKU</td>
                                <td>MIN STOCK</td>
                                <td>MAX STOCK</td>
                                <td>STOCKS</td>
                                <td>LEVEL</td>
                            </tr>

                            <tr align="center">
                                <td colspan="5">HIGHEST STOCKS IN QUANTITY</td>
                            </tr>
                            <tr>
                                <td>ITEM + SKU</td>
                                <td>MIN STOCK</td>
                                <td>MAX STOCK</td>
                                <td>STOCKS</td>
                                <td>LEVEL</td>
                            </tr>

                            <tr align="center">
                                <td colspan="5">HIGHEST STOCKS IN QUANTITY</td>
                            </tr>
                            <tr>
                                <td>ITEM + SKU</td>
                                <td>MIN STOCK</td>
                                <td>MAX STOCK</td>
                                <td>STOCKS</td>
                                <td>LEVEL</td>
                            </tr>

                        </tbody>
                        </table>
                    </section>

                    <section class="particular">
                        <h4>Stocks</h4>
                        <small>ARRANGED BY HIHEST STOCK LEVEL AND STOCK</small>
                        <table class="table table-bordered">
                            <thead>
                                <td>ITEM + SKU</td>
                                <td>MIN</td>
                                <td>MAX</td>
                                <td>STOCK</td>
                                <td>LEVEL</td>
                            </thead>
                        </table>
                    </section>
                </div>
            </div>

        </div>
    </div>
<?php endbuild()?>
<?php loadTo()?>