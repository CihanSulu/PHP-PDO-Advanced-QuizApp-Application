<footer class="footer text-center text-sm-left">
    &copy; 2024 Ortaokulingilizce <a href="https://cihansulu.com" target="_blank"><span class="text-muted d-none d-sm-inline-block float-right">Crafted with <i
    class="mdi mdi-heart text-danger"></i> by CSulu</span></a>
</footer>
</div>
<!-- end page content -->
</div>
</div>
<!-- end page-wrapper -->

<!-- jQuery  -->
<script src="assets/js/jquery.min.js"></script>
<script src="assets/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/metisMenu.min.js"></script>
<script src="assets/js/waves.min.js"></script>
<script src="assets/js/jquery.slimscroll.min.js"></script>

<script src="assets/plugins/jvectormap/jquery-jvectormap-2.0.2.min.js"></script>
<script src="assets/plugins/jvectormap/jquery-jvectormap-world-mill-en.js"></script>
<script src="assets/plugins/moment/moment.js"></script>
<script src="assets/plugins/timepicker/bootstrap-material-datetimepicker.js"></script>
<script src="assets/plugins/clockpicker/jquery-clockpicker.min.js"></script>
<script src="assets/plugins/colorpicker/jquery-asColorPicker.min.js"></script>
<script src="assets/plugins/select2/select2.min.js"></script>
<script src="assets/plugins/bootstrap-colorpicker/js/bootstrap-colorpicker.min.js"></script>
<script src="assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js"></script>
<script src="assets/plugins/bootstrap-maxlength/bootstrap-maxlength.min.js"></script>
<script src="assets/plugins/bootstrap-touchspin/js/jquery.bootstrap-touchspin.min.js"></script>
<script src="assets/pages/jquery.forms-advanced.js"></script>

<script src="assets/plugins/moment/moment.js"></script>
<script src="assets/plugins/apexcharts/apexcharts.min.js"></script>
<script src="https://apexcharts.com/samples/assets/irregular-data-series.js"></script>
<script src="https://apexcharts.com/samples/assets/series1000.js"></script>
<script src="https://apexcharts.com/samples/assets/ohlc.js"></script>
<script src="assets/pages/jquery.dashboard.init.js"></script>
<script src="assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js"></script>

<!-- App js -->
<script src="assets/js/app.js"></script>
<script src="assets/js/custom.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/izitoast/1.4.0/js/iziToast.min.js" integrity="sha512-Zq9o+E00xhhR/7vJ49mxFNJ0KQw1E1TMWkPTxrWcnpfEFDEXgUiwJHIKit93EW/XxE31HSI5GEOW06G6BF1AtA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<?php if (isset($_SESSION["messages"]) && count($_SESSION["messages"]) > 0) {
    foreach ($_SESSION["messages"] as $item) { ?>
        <script>
            iziToast.<?= $item["type"] ?>({
                title: '<?= $item["title"] ?>',
                message: '<?= $item["message"] ?>',
                position: "topRight"
            });
        </script>
    <?php } ?>
    <?php unset($_SESSION["messages"]);
} ?>
<?php 
if(isset($_SESSION["old"])){
    unset($_SESSION["old"]);
}
?>

<!-- Required datatable js -->
<script src="assets/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="assets/plugins/datatables/dataTables.bootstrap4.min.js"></script>
<script src="assets/pages/jquery.modal-animation.js"></script>
<script src="assets/plugins/custombox/custombox.min.js"></script>
<script src="assets/plugins/custombox/custombox.legacy.min.js"></script>
<script>
    $('#datatable').DataTable({
        ordering: false,
        paging: true,
        pageLength: 10, 
        language: {
            url: 'https://cdn.datatables.net/plug-ins/2.2.2/i18n/tr.json',
        },
    });
    function replaceUrl(id){
        var thisElement = document.getElementById("actionBtn");
        var baseUrl = thisElement.href.split("?")[0];
        var newHref = baseUrl + "?method=del&id=" + id;
        thisElement.href = newHref;
    }
</script>

</body>
</html>