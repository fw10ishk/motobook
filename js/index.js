document.addEventListener("DOMContentLoaded", () => {
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
    });

    flashMessages.forEach((flashMessage) => {
        const type = flashMessage.dataset.type;
        const message = flashMessage.dataset.message;

        notyf.open({
            type: type,
            message: message,
        });

        flashMessage.remove();
    });
});
