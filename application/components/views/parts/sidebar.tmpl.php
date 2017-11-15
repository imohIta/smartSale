<?php
    $thisUser = unserialize($registry->get('session')->read('thisUser'));
    $baseUri = $registry->get('config')->get('baseUri');
?>

<script>
    function allowNosOnly(value, div){
        if(isNaN(value)){
            document.getElementById(div).value = value.substring(0, value.length - 1);
            return false;
        }

    }
</script>

<!--  CONTROLS Content -->
<div id="controls">
    <!--SIDEBAR Content -->
    <aside id="leftmenu">
        <div id="leftmenu-wrap">
            <div class="panel-group slim-scroll" role="tablist">
                <div class="panel panel-default">
                    <div id="leftmenuNav" class="panel-collapse collapse in" role="tabpanel">
                        <div class="panel-body">
                            <!--  NAVIGATION Content -->
                            <ul id="navigation">
                                <li class="active open"><a href="<?php echo $baseUri; ?>"><i class="fa fa-dashboard"></i> <span>Dashboard</span></a></li>

                                <?php

                                    switch($thisUser->get('activeAcct')) {

                                    case 2: # Admin
                                ?>

                                <li> <a role="button" tabindex="0"><i class="fa fa-bar-chart-o"></i> <span>Reports</span> </a>
                                    <ul>
                                        <li><a href="<?php echo $baseUri; ?>/report/sales">Sales Report </a></li>
                                        <li><a href="<?php echo $baseUri; ?>/sales/sort"> Staff Sales Report </a></li>
                                        <li><a href="<?php echo $baseUri; ?>/report/stockGroup"> Stock Group Report </a></li>
                                        <li><a href="<?php echo $baseUri; ?>/report/outOfStock"> Out of Stock Report </a></li>
                                    </ul>
                                </li>

                                <li> <a role="button" tabindex="0"><i class="fa fa-envelope"></i> <span>Sales</span> </a>
                                    <ul>
                                        <li><a href="<?php echo $baseUri; ?>/sales/summary"> Sales & Purchases Summary </a></li>
                                        <li><a href="<?php echo $baseUri; ?>/sales/reverse"> Transaction Reversals </a></li>

                                    </ul>
                                </li>

                                <li> <a role="button" tabindex="0"><i class="fa fa-exchange"></i> <span>Purchasing</span> </a>
                                    <ul>
                                        <li><a href="<?php echo $baseUri; ?>/purchasing/addNew">Add New </a></li>
                                        <li><a href="<?php echo $baseUri; ?>/purchasing/summary"> Summary </a></li>
                                    </ul>
                                </li>

                                <li> <a role="button" tabindex="0"><i class="fa fa-list"></i> <span>Stock</span></a>
                                    <ul>
                                        <li><a href="<?php echo $baseUri; ?>/stock/stockCard">Stock Card</a></li>
                                        <li><a href="<?php echo $baseUri; ?>/stock/viewAll">View Current Stock</a></li>
                                        <li><a href="<?php echo $baseUri; ?>/stock/removeBadItem">Remove Bad Item</a></li>
                                        <li><a href="<?php echo $baseUri; ?>/stock/viewBadItems">View Bad Item</a></li>
                                        <li><a href="<?php echo $baseUri; ?>/stock/createCategory">Add Category</a></li>
                                        <li><a href="<?php echo $baseUri; ?>/stock/viewCategories">View Categories</a></li>
                                    </ul>
                                </li>



                                <li> <a role="button" tabindex="0"><i class="fa fa-credit-card"></i> <span>Expenses</span></a>
                                    <ul>
                                        <li><a href="<?php echo $baseUri; ?>/expenses/summary">Summary</a></li>
                                        <li><a href="<?php echo $baseUri; ?>/expenses/addNew">Add New</a></li>
                                        <li><a href="<?php echo $baseUri; ?>/expenses/viewAll">View All</a></li>
                                        <li><a href="<?php echo $baseUri; ?>/expenses/addNewCategory">Add New Category</a></li>
                                    </ul>
                                </li>

                                <li> <a role="button" tabindex="0"><i class="fa fa-users"></i> <span>Customers</span></a>
                                    <ul>
                                        <li><a href="<?php echo $baseUri; ?>/customer/viewAll">View All</a></li>
                                        <li><a href="<?php echo $baseUri; ?>/customer/sendBulkMail">Send Bulk Mail</a></li>
                                        <li><a href="<?php echo $baseUri; ?>/customer/sendBulkSMS">Send Bulk SMS</a></li>
                                    </ul>
                                </li>

                                <li> <a role="button" tabindex="0"><i class="fa fa-envelope"></i> <span>Supplier</span> </a>
                                    <ul>
                                        <li><a href="<?php echo $baseUri; ?>/supplier/addNew">Add New </a></li>
                                        <li><a href="<?php echo $baseUri; ?>/supplier/viewAll"> View All </a></li>
                                        <li><a href="<?php echo $baseUri; ?>/supplier/editInfo">Edit Info </a></li>
                                        <li><a href="<?php echo $baseUri; ?>/supplier/purchaseHistory"> Purchase History </a></li>
                                    </ul>
                                </li>


                                <li> <a role="button" tabindex="0"><i class="fa fa-desktop"></i> <span>Accounting</span></a>
                                    <ul>
                                        <li><a href="<?php echo $baseUri; ?>/accounting/incomingCash">View Incoming Cash</a></li>
                                        <li><a href="<?php echo $baseUri; ?>/accounting/addIncomingCash">Add Incoming Cash</a></li>
                                        <li><a href="<?php echo $baseUri; ?>/accounting/profitNLoss">Profit & Loss</a></li>
                                    </ul>
                                </li>


                                <li> <a role="button" tabindex="0"><i class="fa fa-user"></i> <span>User Accounts</span> </a>
                                    <ul>
                                        <li><a href="<?php echo $baseUri; ?>/account/addNew">Create New</a></li>
                                        <li><a href="<?php echo $baseUri; ?>/account/viewAll">View </a></li>
                                    </ul>
                                </li>


                        <?php

                            break;

                            case 3: # Account
                        ?>

                        <li> <a role="button" tabindex="0"><i class="fa fa-bar-chart-o"></i> <span>Reports</span> </a>
                            <ul>
                                <li><a href="<?php echo $baseUri; ?>/report/sales">Sales Report </a></li>
                                <li><a href="<?php echo $baseUri; ?>/sales/sort"> Staff Sales Report </a></li>
                                <li><a href="<?php echo $baseUri; ?>/report/stockGroup"> Stock Group Report </a></li>
                                <li><a href="<?php echo $baseUri; ?>/report/outOfStock"> Out of Stock Report </a></li>
                            </ul>
                        </li>

                        <li> <a role="button" tabindex="0"><i class="fa fa-envelope"></i> <span>Sales</span> </a>
                            <ul>
                                <li><a href="<?php echo $baseUri; ?>/sales/summary"> Sales & Purchases Summary </a></li>
                            </ul>
                        </li>
                        <li> <a role="button" tabindex="0"><i class="fa fa-exchange"></i> <span>Purchasing</span> </a>
                            <ul>
                                <li><a href="<?php echo $baseUri; ?>/purchasing/addNew">Add New </a></li>
                                <li><a href="<?php echo $baseUri; ?>/purchasing/summary"> Summary </a></li>
                            </ul>
                        </li>

                        <li> <a role="button" tabindex="0"><i class="fa fa-list"></i> <span>Stock</span></a>
                            <ul>
                                <li><a href="<?php echo $baseUri; ?>/stock/stockCard">Stock Card</a></li>
                                <li><a href="<?php echo $baseUri; ?>/stock/viewAll">View Current Stock</a></li>
                                <li><a href="<?php echo $baseUri; ?>/stock/removeBadItem">Remove Bad Item</a></li>
                                <li><a href="<?php echo $baseUri; ?>/stock/viewBadItems">View Bad Item</a></li>
                                <li><a href="<?php echo $baseUri; ?>/stock/createCategory">Add Category</a></li>
                                <li><a href="<?php echo $baseUri; ?>/stock/viewCategories">View Categories</a></li>
                            </ul>
                        </li>
                        <li> <a role="button" tabindex="0"><i class="fa fa-credit-card"></i> <span>Expenses</span></a>
                            <ul>
                                <li><a href="<?php echo $baseUri; ?>/expenses/summary">Summary</a></li>
                                <li><a href="<?php echo $baseUri; ?>/expenses/addNew">Add New</a></li>
                                <li><a href="<?php echo $baseUri; ?>/expenses/viewAll">View All</a></li>
                                <li><a href="<?php echo $baseUri; ?>/expenses/addNewCategory">Add New Category</a></li>
                            </ul>
                        </li>
                            <li> <a role="button" tabindex="0"><i class="fa fa-users"></i> <span>Customers</span></a>
                                <ul>
                                    <li><a href="<?php echo $baseUri; ?>/customer/addInfo">Add Customer Info</a></li>
                                    <li><a href="<?php echo $baseUri; ?>/customer/viewAll">View All</a></li>
                                    <li><a href="<?php echo $baseUri; ?>/customer/sendBulkMail">Send Bulk Mail</a></li>
                                    <li><a href="<?php echo $baseUri; ?>/customer/sendBulkSMS">Send Bulk SMS</a></li>
                                </ul>
                            </li>
                            <li> <a role="button" tabindex="0"><i class="fa fa-user"></i> <span>Manage Staff</span></a>
                                <ul>
                                    <li><a href="<?php echo $baseUri; ?>/staff/addProfile">Add Staff Profile</a></li>
                                    <li><a href="<?php echo $baseUri; ?>/staff/editProfile">Edit Staff Profile</a></li>
                                    <li> <a role="button" tabindex="0"><i class="fa fa-angle-right"></i> Subcharges</a>
                                        <ul>
                                            <li><a href="<?php echo $baseUri; ?>/staff/viewSubcharges" role="button" tabindex="0"><i class="fa fa-angle-right"></i> View All</a></li>
                                            <li><a href="<?php echo $baseUri; ?>/staff/subchargeStaff" role="button" tabindex="0"><i class="fa fa-angle-right"></i> Subcharge Staff</a></li>
                                        </ul>
                                    </li>
                                    <li><a href="<?php echo $baseUri; ?>/staff/generatePaySlip">Generate Pay Slip</a></li>
                                </ul>
                            </li>

                            <li> <a role="button" tabindex="0"><i class="fa fa-desktop"></i> <span>Accounting</span></a>
                                <ul>
                                    <li><a href="<?php echo $baseUri; ?>/accounting/incomingCash">View Incoming Cash</a></li>
                                    <li><a href="<?php echo $baseUri; ?>/accounting/addIncomingCash">Add Incoming Cash</a></li>
                                    <li><a href="<?php echo $baseUri; ?>/accounting/profitNLoss">Profit & Loss</a></li>
                                </ul>
                            </li>



                        <?php
                            break;

                            case 4: # Stock

                         ?>

                             <li> <a role="button" tabindex="0"><i class="fa fa-envelope"></i> <span>Sales</span> </a>
                                 <ul>
                                     <li><a href="<?php echo $baseUri; ?>/sales/viewAll"><i class="fa fa-angle-right"></i> View All <span class="label label-success">new</span></a></li>
                                     <!-- <li><a href="<?php echo $baseUri; ?>/sales/sort"><i class="fa fa-angle-right"></i> Sort</a></li> -->

                                 </ul>
                             </li>

                             <li> <a role="button" tabindex="0"><i class="fa fa-list"></i> <span>Stock</span> </a>
                                 <ul>
                                     <li><a href="<?php echo $baseUri; ?>/stock/addNew">Add New</a></li>
                                     <li><a href="<?php echo $baseUri; ?>/stock/viewAll">View</a></li>
                                     <li><a href="<?php echo $baseUri; ?>/stock/additionsList">Additions List</a></li>
                                     <li><a href="<?php echo $baseUri; ?>/stock/changeItemPrice">Change Item Price</a></li>
                                     <li><a href="<?php echo $baseUri; ?>/stock/removeBadItem">Remove Bad Item</a></li>
                                     <li><a href="<?php echo $baseUri; ?>/stock/viewBadItems">View Bad Item</a></li>
                                 </ul>
                             </li>

                         <?php

                             break;

                             case 5: # Sales Rep

                          ?>

                          <li> <a role="button" tabindex="0"><i class="fa fa-envelope"></i> <span>Sales</span> </a>
                              <ul>
                                  <li><a href="<?php echo $baseUri; ?>/sales/addNew">New</a></li>
                                  <li><a href="<?php echo $baseUri; ?>/sales/viewAll">View All </a></li>
                                  <!-- <li><a href="<?php echo $baseUri; ?>/sales/sort"><i class="fa fa-angle-right"></i> Sort</a></li> -->

                              </ul>
                          </li>

                          <li> <a role="button" tabindex="0"><i class="fa fa-list"></i> <span>Stock</span></a>
                              <ul>
                                  <li><a href="<?php echo $baseUri; ?>/stock/viewAll">View</a></li>
                                  <!-- <li><a href="<?php echo $baseUri; ?>/stock/additionsList">Additions List</a></li> -->
                                  <!-- <li><a href="<?php echo $baseUri; ?>/stock/changeItemPrice">Change Item Price</a></li> -->
                                  <li><a href="<?php echo $baseUri; ?>/stock/removeBadItem">Remove Bad Item</a></li>
                                  <li><a href="<?php echo $baseUri; ?>/stock/viewBadItems">View Bad Item</a></li>
                              </ul>
                          </li>




                          <?php
                                break;
                             }
                           ?>

                          <li><a href="<?php echo $baseUri; ?>/logout"><i class="fa fa-power-off"></i> <span>Logout</span></a></li>


                    </ul>
                </div>
            </div>
        </div>
    </aside>
    <!--/ SIDEBAR Content -->


    <?php
        if(in_array($thisUser->get('activeAcct'), array(2,3))){
     ?>
            <!--RIGHTBAR Content -->
            <aside id="rightmenu">
                <div role="tabpanel">
                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs" role="tablist">
                        <li role="presentation" class="active"><a href="#settings" aria-controls="settings" role="tab" data-toggle="tab">Settings</a></li>
                    </ul>
                    <!-- Tab panes -->
                    <div class="tab-content">


                        <div role="tabpanel" class="tab-pane active" id="settings">
                            <!-- <h6>General Settings</h6> -->

                            <form method="post" action="<?php echo $baseUri; ?>/settings" >

                                <br /><br />

                                <p class="label label-default">Cash Alert Notification</p>
                                <ul class="settings">
                                    <li>
                                        <div class="form-group">
                                            <label class="col-xs-8 control-label">Email</label>
                                            <div class="col-xs-4 control-label text-right">
                                                <div class="togglebutton">
                                                    <label>
                                                        <input type="checkbox" checked="">
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </li>

                                    <li>
                                        <div class="form-group">
                                            <label class="col-xs-8 control-label">SMS</label>
                                            <div class="col-xs-4 control-label text-right">
                                                <div class="togglebutton">
                                                    <label>
                                                        <input type="checkbox" checked="">
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                </ul>

                                <br /><br />
                                <p class="label label-danger">Stock Reduction Notification</p>
                                <ul class="settings">
                                    <li>
                                        <div class="form-group">
                                            <label class="col-xs-8 control-label">Email</label>
                                            <div class="col-xs-4 control-label text-right">
                                                <div class="togglebutton">
                                                    <label>
                                                        <input type="checkbox" checked="">
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="form-group">
                                            <label class="col-xs-8 control-label">SMS</label>
                                            <div class="col-xs-4 control-label text-right">
                                                <div class="togglebutton">
                                                    <label>
                                                        <input type="checkbox" unchecked="">
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </li>

                                </ul>

                                <hr />

                                <ul class="settings">
                                    <li>
                                        <div class="form-group">
                                                <input type="email" class="form-control" name="phone" placeholder="Email Address" >

                                        </div>
                                    </li>
                                    <li>
                                        <div class="form-group">

                                                <input type="text" class="form-control" name="phone" id="phone" onKeyUp="allowNosOnly(this.value, 'phone')" placeholder="Phone Number">

                                        </div>
                                    </li>

                                </ul>



                                <input type="submit" name="submit" class="btn btn-raised btn-success" value="Update" />

                            </form>
                        </div>
                    </div>
                </div>
            </aside>
            <!--/ RIGHTBAR Content -->

    <?php } ?>

</div>
<!--/ CONTROLS Content -->
