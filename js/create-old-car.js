document.addEventListener("DOMContentLoaded", function () {
    // Existing autocomplete functionality
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

    // Price breakdown functionality
    const currentPrice = document.getElementById("current_price");
    const currentPriceBreakdown = document.getElementById(
        "current_price_breakdown",
    );
    const extraPricesContainer = document.getElementById(
        "extraPricesContainer",
    );
    const extraChargesContainer = document.getElementById(
        "extra_charges_container",
    );
    const finalPriceContainer = document.getElementById(
        "final_price_container",
    );

    // Update price breakdown whenever current price changes
    currentPrice.addEventListener("input", updatePriceBreakdown);

    function updatePriceBreakdown() {
        const basePrice = parseFloat(currentPrice.value) || 0;
        currentPriceBreakdown.value = basePrice;

        // Clear existing extra charges display
        extraChargesContainer.innerHTML = "";

        // Calculate total of extra charges
        let totalExtraCharges = 0;
        const extraCharges =
            extraPricesContainer.querySelectorAll(".form-group.row");

        extraCharges.forEach((charge) => {
            const amountInput = charge.querySelector(
                'input[name="extra_charge_amount[]"]',
            );
            const nameInput = charge.querySelector(
                'input[name="extra_charge_name[]"]',
            );

            if (
                amountInput &&
                amountInput.value &&
                nameInput &&
                nameInput.value
            ) {
                const amount = parseFloat(amountInput.value) || 0;
                totalExtraCharges += amount;

                // Create display row for each extra charge
                const chargeDisplay = document.createElement("div");
                chargeDisplay.classList.add("form-group");
                chargeDisplay.innerHTML = `
                    <label>${nameInput.value}</label>
                    <input type="number" value="${amount}" readonly />
                `;
                extraChargesContainer.appendChild(chargeDisplay);
            }
        });

        // Update final price
        const finalPrice = basePrice + totalExtraCharges;
        finalPriceContainer.innerHTML = `
            <label for="final_price">Final Price</label>
            <input type="number" name="final_price" id="final_price" value="${finalPrice}" readonly />
        `;
    }

    // Extra charges management
    const addExtraPriceBtn = document.getElementById("addExtraPriceBtn");

    function createExtraChargeInput() {
        const extraPriceDiv = document.createElement("div");
        extraPriceDiv.classList.add("form-group", "row");
        extraPriceDiv.innerHTML = `
            <input type="text" name="extra_charge_name[]" placeholder="Enter Extra Charge Name" required />
            <input type="number" name="extra_charge_amount[]" placeholder="Enter Extra Charge Amount" required />
            <button type="button" class="icon-btn danger delete-extra-charge">
                <i data-lucide="trash-2"></i>
            </button>
        `;

        extraPricesContainer.appendChild(extraPriceDiv);
        lucide.createIcons();

        // Add event listeners to update price breakdown
        const amountInput = extraPriceDiv.querySelector(
            'input[name="extra_charge_amount[]"]',
        );
        const nameInput = extraPriceDiv.querySelector(
            'input[name="extra_charge_name[]"]',
        );

        amountInput.addEventListener("input", updatePriceBreakdown);
        nameInput.addEventListener("input", updatePriceBreakdown);

        // Add delete functionality
        const deleteBtn = extraPriceDiv.querySelector(".delete-extra-charge");
        deleteBtn.addEventListener("click", () => {
            extraPriceDiv.remove();
            updatePriceBreakdown();
        });
    }

    addExtraPriceBtn.addEventListener("click", function (e) {
        e.preventDefault();
        createExtraChargeInput();
    });

    // Existing image preview functionality
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

    mainImage.addEventListener("change", function () {
        if (this.files && this.files[0]) {
            createImagePreview(this.files[0], mainImagePreview, this, true);
        }
    });

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
        item.textContent = result.text;

        item.addEventListener("click", function () {
            field.value = result.value || result.text;
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
