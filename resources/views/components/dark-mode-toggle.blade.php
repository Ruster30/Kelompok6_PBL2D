<button id="darkModeToggle" class="dark-mode-toggle" aria-label="Toggle tema gelap/terang">
    <i class="bi bi-moon-stars dark-icon"></i>
    <i class="bi bi-sun-fill light-icon" style="display:none;"></i>
</button>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    var toggle = document.getElementById('darkModeToggle');
    var html = document.documentElement;
    var darkIcon = toggle ? toggle.querySelector('.dark-icon') : null;
    var lightIcon = toggle ? toggle.querySelector('.light-icon') : null;
    
    // Load saved preference
    var saved = localStorage.getItem('theme');
    if (saved === 'dark') {
        html.setAttribute('data-theme', 'dark');
        if (darkIcon) darkIcon.style.display = 'none';
        if (lightIcon) lightIcon.style.display = 'inline';
    }
    
    // Toggle on click
    if (toggle) {
        toggle.addEventListener('click', function() {
            var isDark = html.getAttribute('data-theme') === 'dark';
            if (isDark) {
                html.removeAttribute('data-theme');
                localStorage.setItem('theme', 'light');
                if (darkIcon) darkIcon.style.display = 'inline';
                if (lightIcon) lightIcon.style.display = 'none';
            } else {
                html.setAttribute('data-theme', 'dark');
                localStorage.setItem('theme', 'dark');
                if (darkIcon) darkIcon.style.display = 'none';
                if (lightIcon) lightIcon.style.display = 'inline';
            }
        });
    }
});
</script>
@endpush