document.addEventListener("DOMContentLoaded", () => {
    lucide.createIcons();

    // Flash Messages
    const flashMessagesContainer = document.querySelector("#flash-messages");
    const flashMessages =
        flashMessagesContainer.querySelectorAll(".flash-message");

    // Use Notyf for flash messages
    const notyf = new Notyf({
        duration: 3000,
        position: {
            x: "right",
            y: "top",
        },
        dismissible: true,
        types: [
            {
                type: "success",
                className: "notyf__toast--success",
                backgroundColor: "#28a745",
                icon: false,
            },
            {
                type: "error",
                className: "notyf__toast--error",
                backgroundColor: "#dc3545",
                icon: false,
            },
        ],
    });

    flashMessages.forEach((flashMessage) => {
        const type = flashMessage.dataset.type;
        const message = flashMessage.dataset.message;

        // Map the flash message type to Notyf types
        const notyfType = type === "error" ? "error" : "success";

        notyf.open({
            type: notyfType,
            message: message,
        });

        flashMessage.remove();
    });

    // Optionally hide the container once messages have been processed
    flashMessagesContainer.style.display = "none";

    const sidebar = document.getElementById("sidebar");
    const sidebarLogo = sidebar.querySelector(".sidebar-logo");

    // Toggle sidebar on logo click
    sidebarLogo.addEventListener("click", () => {
        sidebar.classList.toggle("collapsed");

        // Close all dropdowns when sidebar is collapsed
        if (sidebar.classList.contains("collapsed")) {
            const openDropdowns = sidebar.querySelectorAll(".dropdown.open");
            openDropdowns.forEach((dropdown) => {
                dropdown.classList.remove("open");
            });
        }
    });

    // Dropdown toggle with improved handling
    const dropdownToggles = sidebar.querySelectorAll(".dropdown-toggle");
    dropdownToggles.forEach((toggle) => {
        toggle.addEventListener("click", (e) => {
            e.preventDefault();
            const dropdown = e.currentTarget.closest(".dropdown");

            // If sidebar is collapsed, toggle only the clicked dropdown
            if (sidebar.classList.contains("collapsed")) {
                // Close all other dropdowns first
                const openDropdowns =
                    sidebar.querySelectorAll(".dropdown.open");
                openDropdowns.forEach((openDropdown) => {
                    if (openDropdown !== dropdown) {
                        openDropdown.classList.remove("open");
                    }
                });

                // Toggle the clicked dropdown
                dropdown.classList.toggle("open");
            } else {
                // Default desktop behavior
                const openDropdowns =
                    sidebar.querySelectorAll(".dropdown.open");
                openDropdowns.forEach((openDropdown) => {
                    if (openDropdown !== dropdown) {
                        openDropdown.classList.remove("open");
                    }
                });

                dropdown.classList.toggle("open");
            }
        });
    });

    // Close dropdowns when clicking outside
    document.addEventListener("click", (e) => {
        if (sidebar.classList.contains("collapsed")) {
            const dropdowns = sidebar.querySelectorAll(".dropdown");
            dropdowns.forEach((dropdown) => {
                if (!dropdown.contains(e.target)) {
                    dropdown.classList.remove("open");
                }
            });
        }
    });

    // Responsive sidebar toggle (same as before)
    function handleResponsiveSidebar() {
        const screenWidth = window.innerWidth;

        if (screenWidth <= 768) {
            // Mobile view
            sidebar.classList.add("collapsed");

            // Create mobile toggle button if it doesn't exist
            if (!document.querySelector(".mobile-menu-toggle")) {
                const mobileToggle = document.createElement("button");
                mobileToggle.classList.add("mobile-menu-toggle");
                mobileToggle.innerHTML = '<i data-lucide="menu"></i>';
                mobileToggle.style.position = "fixed";
                mobileToggle.style.top = "10px";
                mobileToggle.style.left = "10px";
                mobileToggle.style.zIndex = "1001";
                mobileToggle.addEventListener("click", () => {
                    sidebar.classList.toggle("open");
                });
                document.body.appendChild(mobileToggle);
            }
        } else {
            // Desktop view
            sidebar.classList.remove("open");
            sidebar.classList.remove("collapsed");

            // Remove mobile toggle button
            const mobileToggle = document.querySelector(".mobile-menu-toggle");
            if (mobileToggle) {
                mobileToggle.remove();
            }
        }
    }

    // Initial responsive setup
    handleResponsiveSidebar();

    // Recheck on window resize
    window.addEventListener("resize", handleResponsiveSidebar);
});
