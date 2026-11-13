document.addEventListener("DOMContentLoaded", function () {
    // Define today's date at the top level of our main function
    const today = new Date().toISOString().split("T")[0];

    // Existing filter handlers
    const filterSelect = document.querySelector(".filter-select");
    if (filterSelect) {
        filterSelect.addEventListener("change", function () {
            submitFilterForm();
        });
    }

    // Handle search form submission
    const filterForm = document.getElementById("filterForm");
    if (filterForm) {
        filterForm.addEventListener("submit", function (e) {
            e.preventDefault();
            submitFilterForm();
        });
    }

    const searchInput = document.querySelector('[data-filter-type="search"]');
    if (searchInput) {
        searchInput.addEventListener("input", function () {
            if (!this.value.trim()) {
                submitFilterForm();
            }
        });
    }

    // Handle date inputs
    const dateFromInput = document.querySelector(
        '[data-filter-type="date-from"]',
    );
    const dateToInput = document.querySelector('[data-filter-type="date-to"]');

    if (dateFromInput && dateToInput) {
        // Set max date for both inputs to today
        dateFromInput.max = today;
        dateToInput.max = today;

        // Ensure "to" date is not before "from" date
        dateFromInput.addEventListener("change", function () {
            dateToInput.min = this.value;
            if (dateToInput.value && dateToInput.value < this.value) {
                dateToInput.value = this.value;
            }
            submitFilterForm();
        });

        dateToInput.addEventListener("change", function () {
            dateFromInput.max = this.value;
            if (dateFromInput.value && dateFromInput.value > this.value) {
                dateFromInput.value = this.value;
            }
            submitFilterForm();
        });
    }

    // Handle reset filters button
    const resetFiltersButton = document.getElementById("resetFilters");
    if (resetFiltersButton) {
        resetFiltersButton.addEventListener("click", function (e) {
            e.preventDefault();

            // Reset all filter inputs
            if (filterSelect) {
                filterSelect.value = "All";
            }

            if (searchInput) {
                searchInput.value = "";
            }

            if (dateFromInput) {
                dateFromInput.value = "";
                dateFromInput.max = today;
            }

            if (dateToInput) {
                dateToInput.value = "";
                dateToInput.min = "";
                dateToInput.max = today;
            }

            // Navigate to the base URL without any parameters
            window.location.href = window.location.pathname;
        });
    }

    // Handle pagination clicks
    const paginationLinks = document.querySelectorAll(".pagination-link");
    paginationLinks.forEach((link) => {
        link.addEventListener("click", function (e) {
            e.preventDefault();
            const page = this.getAttribute("data-page");
            navigateToPage(page);
        });
    });

    // Function to submit the filter form
    function submitFilterForm() {
        const status =
            document.querySelector('[data-filter-type="status"]')?.value ||
            "All";
        const search =
            document.querySelector('[data-filter-type="search"]')?.value || "";
        const dateFrom =
            document.querySelector('[data-filter-type="date-from"]')?.value ||
            "";
        const dateTo =
            document.querySelector('[data-filter-type="date-to"]')?.value || "";

        // Construct URL with current page and filters
        const urlParams = new URLSearchParams(window.location.search);
        urlParams.set("status", status);
        urlParams.set("search", search);
        urlParams.set("date_from", dateFrom);
        urlParams.set("date_to", dateTo);
        urlParams.set("page", "1"); // Reset to first page when filters change

        // Navigate to filtered results
        window.location.href = `${window.location.pathname}?${urlParams.toString()}`;
    }

    // Function to handle pagination navigation
    function navigateToPage(page) {
        const status =
            document.querySelector('[data-filter-type="status"]')?.value ||
            "All";
        const search =
            document.querySelector('[data-filter-type="search"]')?.value || "";
        const dateFrom =
            document.querySelector('[data-filter-type="date-from"]')?.value ||
            "";
        const dateTo =
            document.querySelector('[data-filter-type="date-to"]')?.value || "";

        const urlParams = new URLSearchParams(window.location.search);
        urlParams.set("page", page);
        urlParams.set("status", status);
        urlParams.set("search", search);
        urlParams.set("date_from", dateFrom);
        urlParams.set("date_to", dateTo);

        window.location.href = `${window.location.pathname}?${urlParams.toString()}`;
    }
});
