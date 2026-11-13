document.addEventListener("DOMContentLoaded", function () {
    const totalOnRoadPrice = document.getElementById("total_on_road");
    const netOnRoadPrice = document.getElementById("net_on_road");
    const netShortPaymentPrice = document.getElementById("net_short_payment");

    const onRoadCalculationFields = [
        "ex_showroom_price",
        "rto_tax",
        "insurance",
        "amc_gmc_tax",
        "ext_warranty",
        "accessories",
        "krunal_accessories",
        "fast_tag",
        "tcs",
        "loyalty_card",
        "driver_price",
    ];

    const netOnRoadCalculationFields = [
        "consumer_offer",
        "corporate_discount",
        "other_discount",
    ];

    const netShortPaymentCalculationFields = ["disbursement", "down_payment"];

    function calculateTotalOnRoadPrice() {
        let total = 0;
        onRoadCalculationFields.forEach((field) => {
            const element = document.getElementById(field);
            total += parseFloat(element.value) || 0;
        });

        totalOnRoadPrice.value = total;
    }

    function calculateNetOnRoadPrice() {
        let total = parseFloat(totalOnRoadPrice.value) || 0;
        netOnRoadCalculationFields.forEach((field) => {
            const element = document.getElementById(field);
            total -= parseFloat(element.value) || 0;
        });

        netOnRoadPrice.value = total;
    }

    function calculateNetShortPaymentPrice() {
        let total = parseFloat(netOnRoadPrice.value) || 0;
        netShortPaymentCalculationFields.forEach((field) => {
            const element = document.getElementById(field);
            total -= parseFloat(element.value) || 0;
        });

        netShortPaymentPrice.value = total;
    }

    onRoadCalculationFields.forEach((field) => {
        const element = document.getElementById(field);
        element.addEventListener("input", calculateTotalOnRoadPrice);
    });

    netOnRoadCalculationFields.forEach((field) => {
        const element = document.getElementById(field);
        element.addEventListener("input", calculateNetOnRoadPrice);
    });

    netShortPaymentCalculationFields.forEach((field) => {
        const element = document.getElementById(field);
        element.addEventListener("input", calculateNetShortPaymentPrice);
    });

    const dealerTotalOnRoadPrice = document.getElementById(
        "dealer_total_on_road",
    );
    const dealerNetOnRoadPrice = document.getElementById("dealer_net_on_road");
    const dealerNetShortPaymentPrice = document.getElementById(
        "dealer_net_short_payment",
    );

    const dealerOnRoadCalculationFields = [
        "dealer_ex_showroom_price",
        "dealer_rto_tax",
        "dealer_insurance",
        "dealer_amc_gmc_tax",
        "dealer_ext_warranty",
        "dealer_accessories",
        "dealer_krunal_accessories",
        "dealer_fast_tag",
        "dealer_tcs",
        "dealer_loyalty_card",
        "dealer_driver_price",
    ];

    const dealerNetOnRoadCalculationFields = [
        "dealer_consumer_offer",
        "dealer_corporate_discount",
        "dealer_other_discount",
    ];

    const dealerNetShortPaymentCalculationFields = [
        "dealer_disbursement",
        "dealer_down_payment",
    ];

    function dealerCalculateTotalOnRoadPrice() {
        let total = 0;
        dealerOnRoadCalculationFields.forEach((field) => {
            const element = document.getElementById(field);
            total += parseFloat(element.value) || 0;
        });

        dealerTotalOnRoadPrice.value = total;
    }

    function dealerCalculateNetOnRoadPrice() {
        let total = parseFloat(dealerTotalOnRoadPrice.value) || 0;
        dealerNetOnRoadCalculationFields.forEach((field) => {
            const element = document.getElementById(field);
            total -= parseFloat(element.value) || 0;
        });

        dealerNetOnRoadPrice.value = total;
    }

    function dealerCalculateNetShortPaymentPrice() {
        let total = parseFloat(dealerNetOnRoadPrice.value) || 0;
        dealerNetShortPaymentCalculationFields.forEach((field) => {
            const element = document.getElementById(field);
            total -= parseFloat(element.value) || 0;
        });

        dealerNetShortPaymentPrice.value = total;
    }

    dealerOnRoadCalculationFields.forEach((field) => {
        const element = document.getElementById(field);
        element.addEventListener("input", dealerCalculateTotalOnRoadPrice);
    });

    dealerNetOnRoadCalculationFields.forEach((field) => {
        const element = document.getElementById(field);
        element.addEventListener("input", dealerCalculateNetOnRoadPrice);
    });

    dealerNetShortPaymentCalculationFields.forEach((field) => {
        const element = document.getElementById(field);
        element.addEventListener("input", dealerCalculateNetShortPaymentPrice);
    });

    const autocompleteFields = document.querySelectorAll(".autocomplete");

    autocompleteFields.forEach((field) => {
        const autocompleteUrl = field.dataset.autocompleteUrl;

        field.addEventListener(
            "input",
            debounce(function () {
                const query = field.value || "";
                if (query.length < 2) return;
                console.log(field.value);

                if (!autocompleteUrl) {
                    console.error(
                        "Autocomplete URL not found for field:",
                        field,
                    );
                    return;
                }

                fetch(`${autocompleteUrl}?query=${encodeURIComponent(query)}`)
                    .then((response) => response.json())
                    .then((data) => {
                        showAutocompleteResults(field, data);
                    })
                    .catch((error) =>
                        console.error(
                            "Error fetching autocomplete data:",
                            error,
                        ),
                    );
            }, 300),
        );
    });

    // Image Preview Management
    const mainImage = document.getElementById("main_image");
    const mainImagePreview = document.getElementById("main-image-preview");
    const additionalImages = document.getElementById("additional_images");
    const additionalImagesPreview = document.getElementById(
        "additional-images-preview",
    );

    function createImagePreview(
        file,
        previewElement,
        inputElement,
        isMain = false,
    ) {
        const reader = new FileReader();
        reader.onload = function (e) {
            const img = document.createElement("img");
            img.src = e.target.result;

            const wrapper = document.createElement("div");
            wrapper.style.position = "relative";
            wrapper.appendChild(img);

            if (!isMain) {
                const removeBtn = document.createElement("button");
                removeBtn.innerHTML = '<i data-lucide="x"></i>';
                removeBtn.className = "icon-btn danger";
                removeBtn.style.position = "absolute";
                removeBtn.style.top = "5px";
                removeBtn.style.right = "5px";

                removeBtn.onclick = function () {
                    wrapper.remove();

                    // Remove the file from the input's FileList
                    const dt = new DataTransfer();
                    const files = inputElement.files;

                    for (let i = 0; i < files.length; i++) {
                        if (files[i] !== file) {
                            dt.items.add(files[i]);
                        }
                    }

                    inputElement.files = dt.files;
                };

                wrapper.appendChild(removeBtn);
            }

            if (isMain) {
                previewElement.innerHTML = "";
            }
            previewElement.appendChild(wrapper);
            lucide.createIcons();
        };
        reader.readAsDataURL(file);
    }

    if (mainImage) {
        mainImage.addEventListener("change", function () {
            if (this.files && this.files[0]) {
                createImagePreview(this.files[0], mainImagePreview, this, true);
            }
        });
    } else {
        console.error("Main image input field not found.");
    }

    if (additionalImages) {
        additionalImages.addEventListener("change", function () {
            if (this.files) {
                for (let i = 0; i < this.files.length; i++) {
                    createImagePreview(
                        this.files[i],
                        additionalImagesPreview,
                        this,
                    );
                }
            }
        });
    } else {
        console.error("Additional images input field not found.");
    }
});

function showAutocompleteResults(field, results) {
    let container = field.nextElementSibling;
    if (!container || !container.classList.contains("autocomplete-items")) {
        container = document.createElement("div");
        container.classList.add("autocomplete-items");
        field.parentNode.insertBefore(container, field.nextSibling);
    }

    container.innerHTML = "";

    results.forEach((result) => {
        const item = document.createElement("div");
        item.classList.add("autocomplete-item");

        // Assuming 'text' is the property to display
        item.textContent = result.text;

        item.addEventListener("click", function () {
            field.value = result.value || result.text; // Set the field value
            container.innerHTML = "";
        });
        container.appendChild(item);
    });
}

function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}
