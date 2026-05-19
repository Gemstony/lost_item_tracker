        </div> <!-- /.page-content -->

        <!-- Footer -->
        <footer>
            <p>&copy; <?= date('Y') ?> National Institute of Transport - Digital Tracking & Reporting System for Lost Items and Incidents. All rights reserved.</p>
        </footer>
    </div> <!-- /#content -->
</div> <!-- /.wrapper -->

<!-- Bootstrap JS Bundle + jQuery (for sidebar toggle) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Sidebar toggle functionality
    $(document).ready(function () {
        $('#sidebarCollapse').on('click', function () {
            $('#sidebar').toggleClass('active');
        });
    });
</script>
<!-- Custom JS (GPS, maps, etc.) -->
<script src="<?= BASE_URL ?? '' ?>assets/js/main.js"></script>
</body>
</html>