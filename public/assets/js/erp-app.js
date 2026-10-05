/**
 * REAL ESTATE MANAGEMENT SOFTWARE / ERP / CRM
 * Modern Application Interactions - Phase 1 Foundation
 */

document.addEventListener('DOMContentLoaded', () => {
    // 1. Sidebar Toggle & Mobile Off-Canvas Drawer
    const sidebar = document.querySelector('.app-sidebar');
    const sidebarToggleBtn = document.getElementById('sidebarToggleBtn');
    const sidebarCloseBtn = document.getElementById('sidebarCloseBtn');
    const sidebarBackdrop = document.getElementById('sidebarBackdrop');

    const openMobileSidebar = () => {
        if (!sidebar) return;
        sidebar.classList.add('mobile-open');
        if (sidebarBackdrop) sidebarBackdrop.classList.add('active');
        document.body.classList.add('sidebar-locked');
    };

    const closeMobileSidebar = () => {
        if (!sidebar) return;
        sidebar.classList.remove('mobile-open');
        if (sidebarBackdrop) sidebarBackdrop.classList.remove('active');
        document.body.classList.remove('sidebar-locked');
    };

    if (sidebarToggleBtn && sidebar) {
        sidebarToggleBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            if (window.innerWidth < 1024) {
                if (sidebar.classList.contains('mobile-open')) {
                    closeMobileSidebar();
                } else {
                    openMobileSidebar();
                }
            } else {
                sidebar.classList.toggle('collapsed');
                const main = document.querySelector('.app-main');
                if (main) {
                    const isCollapsed = sidebar.classList.contains('collapsed');
                    main.style.marginLeft = isCollapsed ? '78px' : '270px';
                    main.style.width = isCollapsed ? 'calc(100% - 78px)' : 'calc(100% - 270px)';
                }
            }
        });
    }

    if (sidebarCloseBtn) {
        sidebarCloseBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            closeMobileSidebar();
        });
    }

    if (sidebarBackdrop) {
        sidebarBackdrop.addEventListener('click', closeMobileSidebar);
    }

    // Close mobile drawer when clicking menu links on mobile
    if (sidebar) {
        sidebar.querySelectorAll('.menu-link').forEach(link => {
            link.addEventListener('click', () => {
                if (window.innerWidth < 1024) {
                    closeMobileSidebar();
                }
            });
        });
    }

    // Close mobile sidebar when clicking outside
    document.addEventListener('click', (e) => {
        if (window.innerWidth < 1024 && sidebar && sidebar.classList.contains('mobile-open')) {
            if (!sidebar.contains(e.target) && sidebarToggleBtn && !sidebarToggleBtn.contains(e.target)) {
                closeMobileSidebar();
            }
        }
    });

    // Window resize handler: normalize sidebar layout between mobile and desktop
    window.addEventListener('resize', () => {
        const main = document.querySelector('.app-main');
        if (window.innerWidth >= 1024) {
            closeMobileSidebar();
            if (main && sidebar) {
                const isCollapsed = sidebar.classList.contains('collapsed');
                main.style.marginLeft = isCollapsed ? '78px' : '270px';
                main.style.width = isCollapsed ? 'calc(100% - 78px)' : 'calc(100% - 270px)';
            }
        } else {
            if (main) {
                main.style.marginLeft = '0';
                main.style.width = '100%';
            }
        }
    });

    // 1.1 Category Accordion Toggling
    const categoryToggles = document.querySelectorAll('.category-header');
    categoryToggles.forEach(toggleBtn => {
        toggleBtn.addEventListener('click', (e) => {
            e.preventDefault();
            // If sidebar is collapsed on desktop, clicking a category icon expands the sidebar
            if (sidebar && sidebar.classList.contains('collapsed') && window.innerWidth >= 1024) {
                if (sidebarToggleBtn) {
                    sidebarToggleBtn.click();
                    const parentCat = toggleBtn.closest('.nav-category');
                    if (parentCat) {
                        parentCat.classList.add('open');
                        toggleBtn.setAttribute('aria-expanded', 'true');
                    }
                    return;
                }
            }

            const parentCat = toggleBtn.closest('.nav-category');
            if (!parentCat) return;

            const isOpen = parentCat.classList.contains('open');
            if (isOpen) {
                parentCat.classList.remove('open');
                toggleBtn.setAttribute('aria-expanded', 'false');
            } else {
                parentCat.classList.add('open');
                toggleBtn.setAttribute('aria-expanded', 'true');
            }
        });
    });

    // 1.2 In-Sidebar Live Module Search
    const searchInput = document.getElementById('sidebarSearchInput');
    const searchClearBtn = document.getElementById('sidebarSearchClear');
    const noResultsEl = document.getElementById('sidebarNoResults');
    const categories = document.querySelectorAll('.nav-category');

    if (searchInput) {
        // Save initial open state map for restoring after search is cleared
        const initialOpenMap = new Map();
        categories.forEach(cat => {
            initialOpenMap.set(cat, cat.classList.contains('open'));
        });

        const performSearch = () => {
            const query = searchInput.value.trim().toLowerCase();

            if (searchClearBtn) {
                searchClearBtn.style.display = query ? 'flex' : 'none';
            }

            if (!query) {
                // Restore original accordion state
                categories.forEach(cat => {
                    cat.style.display = '';
                    const wasOpen = initialOpenMap.get(cat) || false;
                    cat.classList.toggle('open', wasOpen);
                    const btn = cat.querySelector('.category-header');
                    if (btn) btn.setAttribute('aria-expanded', wasOpen ? 'true' : 'false');

                    cat.querySelectorAll('.menu-item').forEach(item => {
                        item.style.display = '';
                    });
                });
                if (noResultsEl) noResultsEl.style.display = 'none';
                return;
            }

            let totalMatches = 0;
            categories.forEach(cat => {
                let catMatches = 0;
                const items = cat.querySelectorAll('.menu-item');

                items.forEach(item => {
                    const link = item.querySelector('.menu-link');
                    const text = link ? (link.querySelector('span:not(.menu-icon)')?.textContent || link.textContent).trim().toLowerCase() : '';
                    if (text.includes(query)) {
                        item.style.display = '';
                        catMatches++;
                        totalMatches++;
                    } else {
                        item.style.display = 'none';
                    }
                });

                if (catMatches > 0) {
                    cat.style.display = '';
                    cat.classList.add('open');
                    const btn = cat.querySelector('.category-header');
                    if (btn) btn.setAttribute('aria-expanded', 'true');
                } else {
                    cat.style.display = 'none';
                }
            });

            if (noResultsEl) {
                noResultsEl.style.display = totalMatches === 0 ? 'flex' : 'none';
            }
        };

        searchInput.addEventListener('input', performSearch);

        if (searchClearBtn) {
            searchClearBtn.addEventListener('click', () => {
                searchInput.value = '';
                searchInput.focus();
                performSearch();
            });
        }

        searchInput.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                searchInput.value = '';
                performSearch();
                searchInput.blur();
            }
        });
    }

    // 2. User Dropdown Toggle
    const userDropdown = document.getElementById('userDropdown');
    const userDropdownBtn = document.getElementById('userDropdownBtn');

    if (userDropdownBtn && userDropdown) {
        userDropdownBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            userDropdown.classList.toggle('open');
        });

        document.addEventListener('click', (e) => {
            if (!userDropdown.contains(e.target)) {
                userDropdown.classList.remove('open');
            }
        });
    }

    // 3. Live Clock in Header
    const liveClock = document.getElementById('liveClock');
    if (liveClock) {
        const updateClock = () => {
            const now = new Date();
            const options = { 
                weekday: 'short', 
                month: 'short', 
                day: 'numeric', 
                hour: '2-digit', 
                minute: '2-digit', 
                second: '2-digit',
                hour12: true 
            };
            liveClock.textContent = now.toLocaleDateString('en-US', options);
        };
        updateClock();
        setInterval(updateClock, 1000);
    }

    // 4. Alert Dismissal
    document.querySelectorAll('.alert-close').forEach(btn => {
        btn.addEventListener('click', () => {
            const alert = btn.closest('.alert');
            if (alert) {
                alert.style.opacity = '0';
                alert.style.transform = 'translateY(-10px)';
                alert.style.transition = 'all 0.2s ease';
                setTimeout(() => alert.remove(), 200);
            }
        });
    });

    // 5. Global Confirmation Modal
    const confirmModal = document.getElementById('globalConfirmModal');
    const confirmModalForm = document.getElementById('confirmModalForm');
    const confirmModalTitle = document.getElementById('confirmModalTitle');
    const confirmModalMessage = document.getElementById('confirmModalMessage');
    const confirmModalSubmitBtn = document.getElementById('confirmModalSubmitBtn');

    window.openConfirmModal = (actionUrl, title, message, btnText = 'Confirm Delete', btnClass = 'btn-danger') => {
        if (!confirmModal) return;

        if (confirmModalForm) confirmModalForm.action = actionUrl;
        if (confirmModalTitle) confirmModalTitle.textContent = title || 'Confirm Action';
        if (confirmModalMessage) confirmModalMessage.textContent = message || 'Are you sure you want to proceed?';
        
        if (confirmModalSubmitBtn) {
            confirmModalSubmitBtn.textContent = btnText;
            confirmModalSubmitBtn.className = 'btn ' + btnClass;
        }

        confirmModal.classList.add('open');
    };

    window.closeConfirmModal = () => {
        if (confirmModal) confirmModal.classList.remove('open');
    };

    // Close modal and drawer on escape key
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            closeConfirmModal();
            closeMobileSidebar();
            document.querySelectorAll('.modal-backdrop').forEach(m => {
                if (m.style.display === 'flex' || m.style.display === 'block') {
                    m.style.display = 'none';
                }
            });
        }
    });

    // 6. Permission Matrix Group Checkboxes
    document.querySelectorAll('.select-all-group').forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            const groupCard = btn.closest('.perm-group-card');
            if (groupCard) {
                const checkboxes = groupCard.querySelectorAll('input[type="checkbox"]');
                const allChecked = Array.from(checkboxes).every(cb => cb.checked);
                checkboxes.forEach(cb => cb.checked = !allChecked);
                btn.textContent = allChecked ? 'Select All' : 'Deselect All';
            }
        });
    });

    // 7. Prevent Double Submit
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', function() {
            const submitBtn = this.querySelector('button[type="submit"]');
            if (submitBtn && !submitBtn.disabled) {
                submitBtn.dataset.originalText = submitBtn.innerHTML;
                submitBtn.disabled = true;
                submitBtn.innerHTML = `
                    <svg class="animate-spin" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="animation: spin 1s linear infinite; display: inline-block; vertical-align: middle;">
                        <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" style="opacity: 0.25;"></circle>
                        <path fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" style="opacity: 0.75;"></path>
                    </svg>
                    Processing...
                `;
                // Allow timeout re-enable in case form didn't navigate
                setTimeout(() => {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = submitBtn.dataset.originalText;
                }, 8000);
            }
        });
    });
});
