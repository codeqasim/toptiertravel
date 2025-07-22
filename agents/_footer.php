 <!-- Bottom padding for nice scrolling experience -->
            <div class="h-20 sm:h-32"></div>
        </div>
    </div>

    <script>
        // Mobile sidebar functionality
        const mobileMenuBtn = document.getElementById('mobile-menu-btn');
        const sidebar = document.getElementById('sidebar');
        const mobileOverlay = document.getElementById('mobile-overlay');
        const menuIcon = document.getElementById('menu-icon');
        const closeIcon = document.getElementById('close-icon');

        let sidebarOpen = false;

        function toggleSidebar() {
            sidebarOpen = !sidebarOpen;

            if (sidebarOpen) {
                sidebar.classList.add('open');
                mobileOverlay.classList.remove('hidden');
                menuIcon.classList.add('hidden');
                closeIcon.classList.remove('hidden');
            } else {
                sidebar.classList.remove('open');
                mobileOverlay.classList.add('hidden');
                menuIcon.classList.remove('hidden');
                closeIcon.classList.add('hidden');
            }
        }

        mobileMenuBtn.addEventListener('click', toggleSidebar);
        mobileOverlay.addEventListener('click', toggleSidebar);

        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(event) {
            if (window.innerWidth < 768 && sidebarOpen) {
                if (!sidebar.contains(event.target) && !mobileMenuBtn.contains(event.target)) {
                    toggleSidebar();
                }
            }
        });

        // Handle window resize
        window.addEventListener('resize', function() {
            if (window.innerWidth >= 768 && sidebarOpen) {
                // Close mobile menu when resizing to desktop
                toggleSidebar();
            }
        });

        // User dropdown functionality
        const userDropdownBtn = document.getElementById('user-dropdown-btn');
        const userDropdown = document.getElementById('user-dropdown');

        if (userDropdownBtn && userDropdown) {
            userDropdownBtn.addEventListener('click', function(event) {
                event.stopPropagation();
                userDropdown.classList.toggle('show');
            });

            // Close dropdown when clicking outside
            document.addEventListener('click', function(event) {
                if (!userDropdown.contains(event.target) && !userDropdownBtn.contains(event.target)) {
                    userDropdown.classList.remove('show');
                }
            });
        }

        // Function to handle link sharing
        function shareLink(type) {
            const links = {
                travel: 'https://travel.example.com/ref/johndoe',
                partner: 'https://partner.example.com/ref/johndoe'
            };

            if (navigator.share) {
                navigator.share({
                    title: `Share ${type} link`,
                    text: `Join me on this amazing ${type} platform!`,
                    url: links[type]
                }).then(() => {
                    console.log('Successful share');
                }).catch((error) => {
                    console.log('Error sharing', error);
                });
            } else {
                // Fallback for browsers that don't support native sharing
                alert(`Share this ${type} link: ${links[type]}`);
            }
        }

        // Function to copy link to clipboard
        function copyLink(type, buttonElement) {
            const links = {
                travel: 'https://travel.example.com/ref/johndoe',
                partner: 'https://partner.example.com/ref/johndoe'
            };

            navigator.clipboard.writeText(links[type]).then(() => {
                // Show success message inline
                const originalText = buttonElement.textContent;
                const originalClasses = buttonElement.className;

                buttonElement.textContent = '✓ Copied!';
                buttonElement.className = 'bg-emerald-500 text-white rounded-xl shadow-sm transition-all duration-300 px-6 py-3 font-medium';

                setTimeout(() => {
                    buttonElement.textContent = originalText;
                    buttonElement.className = originalClasses;
                }, 2000);
            }).catch(err => {
                console.error('Failed to copy: ', err);
                // Fallback for older browsers
                const textArea = document.createElement('textarea');
                textArea.value = links[type];
                document.body.appendChild(textArea);
                textArea.select();
                document.execCommand('copy');
                document.body.removeChild(textArea);

                // Show copied message inline for fallback
                const originalText = buttonElement.textContent;
                const originalClasses = buttonElement.className;

                buttonElement.textContent = '✓ Copied!';
                buttonElement.className = 'bg-emerald-500 text-white rounded-xl shadow-sm transition-all duration-300 px-6 py-3 font-medium';

                setTimeout(() => {
                    buttonElement.textContent = originalText;
                    buttonElement.className = originalClasses;
                }, 2000);
            });
        }

        // Toggle dropdown functionality
        document.addEventListener('click', function(event) {
            const dropdowns = document.querySelectorAll('.dropdown');
            dropdowns.forEach(dropdown => {
                if (!dropdown.contains(event.target)) {
                    dropdown.classList.remove('active');
                }
            });
        });

        // Add click handler for all dropdown toggles
        document.querySelectorAll('.dropdown').forEach(dropdown => {
            dropdown.addEventListener('click', function(event) {
                event.stopPropagation();
                this.classList.toggle('active');
            });
        });

        // Search functionality
        function performSearch(query) {
            // Simple search simulation - you can enhance this
            console.log('Searching for:', query);
            // In a real app, this would trigger API calls or filter results
        }

        function searchBookings(query) {
            const tableRows = document.querySelectorAll('tbody tr');

            tableRows.forEach(row => {
                const textContent = row.textContent.toLowerCase();
                const searchQuery = query.toLowerCase();

                if (textContent.includes(searchQuery) || query === '') {
                    row.style.display = 'table-row';
                } else {
                    row.style.display = 'none';
                }
            });
        }

        // Filter functionality
        function toggleFilter() {
            const filterDropdown = document.querySelector('.filter-dropdown');
            filterDropdown.classList.toggle('active');
        }

        function applyFilters() {
            const statusFilter = document.getElementById('statusFilter').value.toLowerCase();
            const typeFilter = document.getElementById('typeFilter').value.toLowerCase();
            const guestFilter = document.getElementById('guestFilter').value.toLowerCase();
            const tableRows = document.querySelectorAll('tbody tr');

            tableRows.forEach(row => {
                const status = row.querySelector('td:nth-child(5) span').textContent.toLowerCase();
                const type = row.querySelector('td:nth-child(4) span').textContent.toLowerCase();
                const guestType = row.querySelector('td:nth-child(1) p:nth-child(2)').textContent.toLowerCase();

                const statusMatch = !statusFilter || status.includes(statusFilter);
                const typeMatch = !typeFilter || type.includes(typeFilter);
                const guestMatch = !guestFilter || guestType.includes(guestFilter);

                if (statusMatch && typeMatch && guestMatch) {
                    row.style.display = 'table-row';
                } else {
                    row.style.display = 'none';
                }
            });
        }

        function clearFilters() {
            document.getElementById('statusFilter').value = '';
            document.getElementById('typeFilter').value = '';
            document.getElementById('guestFilter').value = '';
            applyFilters();
        }

        // Table dropdown functionality
        function toggleTableDropdown(buttonElement) {
            const dropdown = buttonElement.closest('.table-dropdown');

            // Close all other table dropdowns
            document.querySelectorAll('.table-dropdown').forEach(otherDropdown => {
                if (otherDropdown !== dropdown) {
                    otherDropdown.classList.remove('active');
                }
            });

            // Toggle current dropdown
            dropdown.classList.toggle('active');
        }

        // Close table dropdowns when clicking outside
        document.addEventListener('click', function(event) {
            if (!event.target.closest('.table-dropdown')) {
                document.querySelectorAll('.table-dropdown').forEach(dropdown => {
                    dropdown.classList.remove('active');
                });
            }
        });

        // Prevent dropdown from closing when clicking inside it
        document.querySelectorAll('.table-dropdown-content').forEach(content => {
            content.addEventListener('click', function(event) {
                event.stopPropagation();
            });
        });

        // Add touch-friendly hover effects for mobile
        if ('ontouchstart' in window) {
            document.querySelectorAll('.hover-scale').forEach(element => {
                element.addEventListener('touchstart', function() {
                    this.style.transform = 'scale(1.05)';
                });
                element.addEventListener('touchend', function() {
                    this.style.transform = 'scale(1)';
                });
            });
        }
    </script>
</body>
</html>