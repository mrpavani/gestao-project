</div><!-- End Content -->
</div><!-- End Wrapper -->

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Custom Scripts -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const sidebar = document.getElementById('sidebar');
        const sidebarCollapse = document.getElementById('sidebarCollapse');

        if (sidebarCollapse) {
            sidebarCollapse.addEventListener('click', function () {
                sidebar.classList.toggle('active');
            });
        }
    });
</script>
</body>

</html>