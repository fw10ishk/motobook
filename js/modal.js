document.addEventListener("DOMContentLoaded", function () {
    // Open Modal Function (Generalized)
    function openModal(modalId) {
        const modal = document.getElementById(modalId);
        const pageContent = document.querySelector(".page-content");

        if (modal) {
            modal.style.display = "flex"; // Show modal
            pageContent.style.overflow = "hidden"; // Hide scroll bar
        }
    }

    // Close Modal Function (Generalized)
    function closeModal(modalId) {
        const modal = document.getElementById(modalId);
        const pageContent = document.querySelector(".page-content");

        if (modal) {
            modal.style.display = "none"; // Hide modal
            pageContent.style.overflow = "auto"; // Show scroll bar
        }
    }

    // Event listener for opening modals using a class or individual ID triggers
    document.querySelectorAll("[data-modal-open]").forEach((button) => {
        button.addEventListener("click", function () {
            const modalId = this.getAttribute("data-modal-open");
            openModal(modalId);
        });
    });

    // Event listener for closing modals using a class or individual ID triggers
    document.querySelectorAll("[data-modal-close]").forEach((button) => {
        button.addEventListener("click", function () {
            const modalId = this.getAttribute("data-modal-close");
            closeModal(modalId);
        });
    });
});
