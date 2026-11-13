// Get references to key elements
const currentPriceInput = document.getElementById("current_price_breakdown");
const extraPricesContainer = document.getElementById("extraPricesContainer");
const extraChargesContainer = document.getElementById(
    "extra_charges_container",
);
const finalPriceContainer = document.getElementById("final_price_container");
const addExtraPriceBtn = document.getElementById("addExtraPriceBtn");

// Initialize existing extra charges from the template
function initializeExtraCharges() {
    const existingChargeRows =
        extraPricesContainer.querySelectorAll(".extra-price-row");

    existingChargeRows.forEach((row) => {
        const amountInput = row.querySelector(
            'input[name="extra_charge_amount[]"]',
        );
        const deleteBtn = row.querySelector(".delete-extra-charge");

        // Add input event listener for price updates
        amountInput.addEventListener("input", updatePriceBreakdown);

        // Add click event listener for deletion
        deleteBtn.addEventListener("click", () => {
            row.remove();
            updatePriceBreakdown();
        });
    });

    // Initial price breakdown calculation
    updatePriceBreakdown();
}

// Create new extra charge input row
function createExtraChargeInput() {
    const extraPriceDiv = document.createElement("div");
    extraPriceDiv.classList.add("form-group", "row", "extra-price-row");

    extraPriceDiv.innerHTML = `
        <input 
            type="text" 
            name="extra_charge_name[]" 
            placeholder="Enter Extra Charge Name" 
            required 
        />
        <input 
            type="number" 
            name="extra_charge_amount[]" 
            placeholder="Enter Extra Charge Amount" 
            required 
        />
        <button type="button" class="icon-btn danger delete-extra-charge">
            <i data-lucide="trash-2"></i>
        </button>
    `;

    extraPricesContainer.appendChild(extraPriceDiv);
    lucide.createIcons();

    // Add event listeners to new row
    const amountInput = extraPriceDiv.querySelector(
        'input[name="extra_charge_amount[]"]',
    );
    const nameInput = extraPriceDiv.querySelector(
        'input[name="extra_charge_name[]"]',
    );
    const deleteBtn = extraPriceDiv.querySelector(".delete-extra-charge");

    amountInput.addEventListener("input", updatePriceBreakdown);
    nameInput.addEventListener("input", updatePriceBreakdown);
    deleteBtn.addEventListener("click", () => {
        extraPriceDiv.remove();
        updatePriceBreakdown();
    });
}

// Format currency for display
function formatCurrency(amount) {
    return new Intl.NumberFormat("en-IN", {
        style: "currency",
        currency: "INR",
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
    }).format(amount);
}

// Update price breakdown calculations
function updatePriceBreakdown() {
    const basePrice = parseFloat(currentPriceInput.value) || 0;

    // Clear existing extra charges display
    extraChargesContainer.innerHTML = "";

    // Calculate and display base price
    const basePriceDisplay = document.createElement("div");
    basePriceDisplay.classList.add("form-group");
    basePriceDisplay.innerHTML = `
        <label>Base Price</label>
        <input type="number" value="${basePrice}" readonly />
    `;
    extraChargesContainer.appendChild(basePriceDisplay);

    // Calculate total extra charges
    let totalExtraCharges = 0;
    const extraCharges =
        extraPricesContainer.querySelectorAll(".extra-price-row");

    extraCharges.forEach((charge) => {
        const amountInput = charge.querySelector(
            'input[name="extra_charge_amount[]"]',
        );
        const nameInput = charge.querySelector(
            'input[name="extra_charge_name[]"]',
        );

        if (amountInput?.value && nameInput?.value) {
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

    // Display total extra charges
    if (totalExtraCharges > 0) {
        const totalChargesDisplay = document.createElement("div");
        totalChargesDisplay.classList.add("form-group");
        totalChargesDisplay.innerHTML = `
            <label>Total Extra Charges</label>
            <input type="number" value="${totalExtraCharges}" readonly />
        `;
        extraChargesContainer.appendChild(totalChargesDisplay);
    }

    // Update final price
    const finalPrice = basePrice + totalExtraCharges;
    finalPriceContainer.innerHTML = `
        <div class="form-group">
            <label for="final_price">Final Price</label>
            <input 
                type="number" 
                name="final_price" 
                id="final_price" 
                value="${finalPrice}" 
                readonly 
            />
        </div>
    `;
}

// Event Listeners
addExtraPriceBtn.addEventListener("click", (e) => {
    e.preventDefault();
    createExtraChargeInput();
});

// Initialize on page load
document.addEventListener("DOMContentLoaded", () => {
    initializeExtraCharges();
});
