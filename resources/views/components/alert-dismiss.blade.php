@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    var successAlert = document.getElementById('success-alert');
    var errorAlert = document.getElementById('error-alert');
    
    if (successAlert) {
        setTimeout(function() {
            successAlert.style.transition = 'opacity .3s ease';
            successAlert.style.opacity = '0';
            setTimeout(function() { successAlert.remove(); }, 300);
        }, 2000);
    }
    
    if (errorAlert) {
        setTimeout(function() {
            errorAlert.style.transition = 'opacity .5s ease';
            errorAlert.style.opacity = '0';
            setTimeout(function() { errorAlert.remove(); }, 500);
        }, 4000);
    }
});
</script>
@endpush