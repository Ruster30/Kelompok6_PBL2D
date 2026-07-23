@props(['storageKey' => 'sidebarScrollPosition'])

<script>
// Sidebar scroll position persistence
document.addEventListener('DOMContentLoaded', function() {
    var sidebarNav = document.getElementById('sidebarNav');
    var KEY = '{{ $storageKey }}';
    
    if (sidebarNav) {
        var restoreScroll = function() {
            var saved = sessionStorage.getItem(KEY);
            if (saved !== null) {
                sidebarNav.scrollTop = parseInt(saved, 10);
            }
        };
        restoreScroll();
        if (window.requestAnimationFrame) {
            window.requestAnimationFrame(restoreScroll);
        }
        sidebarNav.addEventListener('scroll', function() {
            sessionStorage.setItem(KEY, sidebarNav.scrollTop);
        });
        window.addEventListener('beforeunload', function() {
            sessionStorage.setItem(KEY, sidebarNav.scrollTop);
        });
        document.addEventListener('click', function(e) {
            var target = e.target.closest('a, button[type="submit"]');
            if (target && sidebarNav) {
                sessionStorage.setItem(KEY, sidebarNav.scrollTop);
            }
        }, true);
    }
});

// Sidebar responsive toggle
document.addEventListener('DOMContentLoaded', function() {
    var sidebar = document.getElementById('sidebar');
    var overlay = document.getElementById('sidebarOverlay');
    var toggleBtn = document.getElementById('sidebarToggle');
    
    if (toggleBtn && sidebar && overlay) {
        function openSidebar() {
            sidebar.classList.add('open');
            overlay.classList.add('show');
            document.body.style.overflow = 'hidden';
        }
        function closeSidebar() {
            sidebar.classList.remove('open');
            overlay.classList.remove('show');
            document.body.style.overflow = '';
        }
        toggleBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            if (sidebar.classList.contains('open')) {
                closeSidebar();
            } else {
                openSidebar();
            }
        });
        overlay.addEventListener('click', closeSidebar);
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && sidebar.classList.contains('open')) {
                closeSidebar();
            }
        });
        var navLinks = sidebar.querySelectorAll('.nav-item');
        navLinks.forEach(function(link) {
            link.addEventListener('click', function() {
                if (window.innerWidth < 768) {
                    closeSidebar();
                }
            });
        });
        window.addEventListener('resize', function() {
            if (window.innerWidth > 768) {
                closeSidebar();
            }
        });
    }
});
</script>