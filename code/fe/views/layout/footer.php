</div> <!-- End container-fluid -->

<footer class="footer mt-auto py-3 bg-light no-print">
    <div class="container text-center">
        <span class="text-muted">© <?php echo date('Y'); ?> Hệ thống Quản lý Thu Phí Học Sinh.</span>
    </div>
</footer>

<!-- Bootstrap 5 JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- jQuery (Optional if needed) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Custom Scripts -->
<script>
    // Auto hide alerts after 5 seconds
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(function() {
            var alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                var bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
        
        // Confirm delete
        var deleteLinks = document.querySelectorAll('.btn-delete');
        deleteLinks.forEach(function(link) {
            link.addEventListener('click', function(e) {
                if (!confirm('Bạn có chắc chắn muốn xóa mục này? Hành động này không thể hoàn tác!')) {
                    e.preventDefault();
                }
            });
        });
    });
</script>

</body>
</html>
