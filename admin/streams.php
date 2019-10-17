<?php
include "functions.php";
if (!isset($_SESSION['user_id'])) { header("Location: ./login.php"); exit; }

if (isset($_GET["category"])) {
    if (!isset($rCategories[$_GET["category"]])) {
        exit;
    } else {
        $rCategory = $rCategories[$_GET["category"]];
    }
} else {
    $rCategory = null;
}

if ($rSettings["sidebar"]) {
    include "header_sidebar.php";
} else {
    include "header.php";
}
        if ($rSettings["sidebar"]) { ?>
        <div class="content-page"><div class="content<?php if ($rPermissions["is_reseller"]) { echo " boxed-layout"; } ?>"><div class="container-fluid">
        <?php } else { ?>
        <div class="wrapper<?php if ($rPermissions["is_reseller"]) { echo " boxed-layout"; } ?>"><div class="container-fluid">
        <?php } ?>
                <!-- start page title -->
                <div class="row">
                    <div class="col-12">
                        <div class="page-title-box">
                            <div class="page-title-right">
                                <ol class="breadcrumb m-0">
                                    <li>
                                        <?php if (!$detect->isMobile()) { ?>
                                        <a href="#" onClick="toggleAuto();">
                                            <button type="button" class="btn btn-dark waves-effect waves-light btn-sm">
                                                <i class="mdi mdi-refresh"></i> <span class="auto-text">Auto-Refresh</span>
                                            </button>
                                        </a>
                                        <?php } else { ?>
                                        <a href="javascript:location.reload();" onClick="toggleAuto();">
                                            <button type="button" class="btn btn-dark waves-effect waves-light btn-sm">
                                                <i class="mdi mdi-refresh"></i> Refresh
                                            </button>
                                        </a>
                                        <?php }
                                        if ($rPermissions["is_admin"]) { ?>
                                        <a href="stream.php<?php if($rCategory) { echo "?category=".$rCategory["id"]; }?>">
                                            <button type="button" class="btn btn-success waves-effect waves-light btn-sm">
                                                <i class="mdi mdi-plus"></i> Add Stream
                                            </button>
                                        </a>
                                        <?php } ?>
                                    </li>
                                </ol>
                            </div>
                            <h4 class="page-title">Streams<?php if ($rCategory) { echo " - ".$rCategory["category_name"]; } ?></h4>
                        </div>
                    </div>
                </div>     
                <!-- end page title --> 

                <div class="row">
                    <div class="col-12">
                        <?php if ($rPermissions["is_admin"]) { ?>
                        <div class="alert alert-warning" role="alert">
                            <i class="mdi mdi-alert-outline mr-2"></i> Search functionality is very limited in the <strong>BETA</strong>. This will be replaced and refined shortly. Also pagination speed will improve.
                        </div>
                        <?php } ?>
                        <div class="card">
                            <div class="card-body" style="overflow-x:auto;">
                                <div class="form-group row mb-4">
                                    <label class="col-md-2 col-form-label text-center" for="category_name">Category Name</label>
                                    <div class="col-md-2">
                                        <select id="category_id" class="form-control" data-toggle="select2">
                                            <option value="" selected>All Categories</option>
                                            <?php foreach ($rCategories as $rCategory) { ?>
                                            <option value="<?=$rCategory["id"]?>"><?=$rCategory["category_name"]?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <label class="col-md-2 col-form-label text-center" for="stream_search">Search Streams</label>
                                    <div class="col-md-2">
                                        <input type="text" class="form-control" id="stream_search" value="">
                                    </div>
                                    <label class="col-md-2 col-form-label text-center" for="show_entries">Entries to Show</label>
                                    <div class="col-md-2">
                                        <select id="show_entries" class="form-control" data-toggle="select2">
                                            <option value="10" selected>10</option>
                                            <option value="25">25</option>
                                            <option value="50">50</option>
                                            <option value="100">100</option>
                                        </select>
                                    </div>
                                </div>
                                <table id="datatable-streampage" style="font-family: tahoma;" class="table dt-responsive nowrap">
                                    <thead>
                                        <tr style="<?php echo $background ?>">
                                            <th class="text-center">ID</th>
                                            <th>Name</th>
                                            <th class="text-center">Current Source</th>
                                            <th class="text-center">Server</th>
                                            <th class="text-center">Clients</th>
                                            <th class="text-center">Uptime</th>
                                            <th class="text-center">Actions</th>
                                            <th class="text-center">Player</th>
                                            <th class="text-center">Stream Info</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div> <!-- end card body-->
                        </div> <!-- end card -->
                    </div><!-- end col-->
                </div>
                <!-- end row-->
            </div> <!-- end container -->
        </div>
        <!-- end wrapper -->
        <?php if ($rSettings["sidebar"]) { echo "</div>"; } ?>
        <!-- Footer Start -->
        <footer class="footer">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12 copyright text-center"><?=getFooter()?></div>
                </div>
            </div>
        </footer>
        <!-- end Footer -->

        <!-- Vendor js -->
        <script src="assets/js/vendor.min.js"></script>
        <script src="assets/libs/jquery-toast/jquery.toast.min.js"></script>

        <script>
        var autoRefresh = true;
        var rCategory = "";
        
        function toggleAuto() {
            if (autoRefresh == true) {
                autoRefresh = false;
                $(".auto-text").html("Manual Mode");
            } else {
                autoRefresh = true;
                $(".auto-text").html("Auto-Refresh");
            }
        }
        
        function api(rID, rServerID, rType) {
            if (rType == "delete") {
                if (confirm('Are you sure you want to delete this stream?') == false) {
                    return;
                }
            }
            $.getJSON("./api.php?action=stream&sub=" + rType + "&stream_id=" + rID + "&server_id=" + rServerID, function(data) {
                if (data.result == true) {
                    if (rType == "start") {
                        $.toast("Stream successfully started. It will take a minute or so before the stream becomes available.");
                    } else if (rType == "stop") {
                        $.toast("Stream successfully stopped.");
                    } else if (rType == "restart") {
                        $.toast("Stream successfully restarted. It will take a minute or so before the stream becomes available.");
                    } else if (rType == "delete") {
                        $.toast("Stream successfully deleted.");
                    }
                    $.each($('.tooltip'), function (index, element) {
                        $(this).remove();
                    });
                    $("#datatable-streampage").DataTable().ajax.reload( null, false );
                } else {
                    $.toast("An error occured while processing your request.");
                }
            }).fail(function() {
                $.toast("An error occured while processing your request.");
            });
        }
        function player(rID) {
            $.magnificPopup.open({
                items: {
                    src: "./player.php?type=live&id=" + rID,
                    type: 'iframe'
                }
            });
        }
        function reloadStreams() {
            if (autoRefresh == true) {
                $("#datatable-streampage").DataTable().ajax.reload( null, false );
            }
            setTimeout(reloadStreams, 5000);
        }
        function getCategory() {
            return window.rCategory;
        }
        $(document).ready(function() {
            $('select').select2({width: '100%'});
            $("#datatable-streampage").DataTable({
                language: {
                    paginate: {
                        previous: "<i class='mdi mdi-chevron-left'>",
                        next: "<i class='mdi mdi-chevron-right'>"
                    }
                },
                drawCallback: function() {
                    $(".dataTables_paginate > .pagination").addClass("pagination-rounded");
                    $('[data-toggle="tooltip"]').tooltip();
                },
                createdRow: function(row, data, index) {
                    $(row).addClass('stream-' + data[0]);
                },
                responsive: false,
                processing: true,
                serverSide: true,
                ajax: {
                    url: "./table.php",
                    "data": function(d) {
                        d.id = "streams",
                        d.category = getCategory();
                    }
                },
                columnDefs: [
                    {"className": "dt-center", "targets": [0,2,3,4,5,6,7]},
                    <?php if (!$rPermissions["is_admin"]) { ?>
                    {"targets": [2, 5, 6, 7], "visible": false, "searchable": false},
                    <?php } ?>
                ]
            });
            $('#stream_search').keyup(function(){
                $('#datatable-streampage').DataTable().search($(this).val()).draw();
            })
            $('#show_entries').change(function(){
                $('#datatable-streampage').DataTable().page.len($(this).val()).draw();
            })
            $('#category_id').change(function(){
                window.rCategory = $(this).val();
                $("#datatable-streampage").DataTable().ajax.reload( null, false );
            })
            <?php if (!$detect->isMobile()) { ?>
            setTimeout(reloadStreams, 5000);
            <?php } ?>
        });
        </script>

        <!-- third party js -->
        <script src="assets/libs/select2/select2.min.js"></script>
        <script src="assets/libs/datatables/jquery.dataTables.min.js"></script>
        <script src="assets/libs/datatables/dataTables.bootstrap4.js"></script>
        <script src="assets/libs/datatables/dataTables.responsive.min.js"></script>
        <script src="assets/libs/datatables/responsive.bootstrap4.min.js"></script>
        <script src="assets/libs/datatables/dataTables.buttons.min.js"></script>
        <script src="assets/libs/datatables/buttons.bootstrap4.min.js"></script>
        <script src="assets/libs/datatables/buttons.html5.min.js"></script>
        <script src="assets/libs/datatables/buttons.flash.min.js"></script>
        <script src="assets/libs/datatables/buttons.print.min.js"></script>
        <script src="assets/libs/datatables/dataTables.keyTable.min.js"></script>
        <script src="assets/libs/datatables/dataTables.select.min.js"></script>
        <script src="assets/libs/magnific-popup/jquery.magnific-popup.min.js"></script>
        <!-- third party js ends -->

        <!-- App js-->
        <script src="assets/js/app.min.js"></script>
        
    </body>
</html>