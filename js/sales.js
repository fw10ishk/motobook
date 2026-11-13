document.addEventListener("DOMContentLoaded", function () {
    // Existing loan calculation elements
    const saleType = document.getElementById("sale_type");
    const loanDetails = document.getElementById("loan-details");
    const loanAmountInput = document.getElementById("loan_amount");
    const interestRateInput = document.getElementById("interest_rate");
    const numberOfPartsInput = document.getElementById("number_of_parts");
    const installmentAmountInput =
        document.getElementById("installment_amount");

    // New car selection elements
    const carTypeSelect = document.getElementById("car_type");
    const carSelect = document.getElementById("car_id");
    const buyerNameInput = document.getElementById("buyer_name");
    const totalPriceInput = document.getElementById("total_price");

    // Car selection handlers
    carTypeSelect.addEventListener("change", async function (event) {
        const carType = event.target.value;

        if (!carType) {
            resetCarSelect();
            return;
        }

        try {
            const response = await fetch(`/api/sales/cars/${carType}`);
            if (!response.ok) throw new Error("Failed to fetch cars");

            const cars = await response.json();
            updateCarSelect(cars);
        } catch (error) {
            console.error("Error fetching cars:", error);
            resetCarSelect();
        }
    });

    carSelect.addEventListener("change", async function (event) {
        const carId = event.target.value;
        const carType = carTypeSelect.value;

        if (!carId || !carType) {
            resetCarDetails();
            return;
        }

        try {
            const response = await fetch(`/api/sales/cars/${carType}/${carId}`);
            if (!response.ok) throw new Error("Failed to fetch car details");

            const carDetails = await response.json();
            updateCarDetails(carDetails);
        } catch (error) {
            console.error("Error fetching car details:", error);
            resetCarDetails();
        }
    });

    // Existing loan calculation handlers
    saleType.addEventListener("change", function () {
        const saleTypeValue = saleType.value;
        if (saleTypeValue === "loan") {
            loanDetails.style.display = "flex";
        } else {
            loanDetails.style.display = "none";
            // Reset loan fields
            loanAmountInput.value = "";
            interestRateInput.value = "";
            numberOfPartsInput.value = "";
            installmentAmountInput.value = "";
        }
    });

    function calculateEMI() {
        const loanAmount = parseFloat(loanAmountInput.value);
        const annualInterestRate = parseFloat(interestRateInput.value);
        const numberOfInstallments = parseInt(numberOfPartsInput.value, 10);

        if (
            !loanAmount ||
            !annualInterestRate ||
            !numberOfInstallments ||
            numberOfInstallments <= 0
        ) {
            installmentAmountInput.value = "Invalid Input";
            return;
        }

        const monthlyInterestRate = annualInterestRate / (12 * 100);
        const EMI =
            (loanAmount *
                monthlyInterestRate *
                Math.pow(1 + monthlyInterestRate, numberOfInstallments)) /
            (Math.pow(1 + monthlyInterestRate, numberOfInstallments) - 1);

        installmentAmountInput.value = EMI.toFixed(2);
    }

    // Attach EMI calculation listeners
    [loanAmountInput, interestRateInput, numberOfPartsInput].forEach(
        (input) => {
            input.addEventListener("input", calculateEMI);
        },
    );

    // Car selection helper functions
    function updateCarSelect(cars) {
        carSelect.innerHTML =
            '<option value="" disabled selected>Select Vehicle</option>';

        cars.forEach((car) => {
            const option = document.createElement("option");
            option.value = car.id;
            option.textContent = `${car.manufacturer_name} ${car.model_name}`;
            carSelect.appendChild(option);
        });

        carSelect.disabled = false;
    }

    function updateCarDetails(car) {
        buyerNameInput.value = car.customer_name || "";

        console.log(car);
        let totalPrice =
            car.car_type === "old"
                ? calculateUsedCarPrice(car)
                : car.ex_showroom_price;

        totalPriceInput.value = totalPrice;
    }

    function calculateUsedCarPrice(car) {
        let totalPrice = car.current_price;
        if (car.extra_charges) {
            totalPrice += car.extra_charges.reduce(
                (sum, charge) => sum + charge.amount,
                0,
            );
        }
        return totalPrice;
    }

    function resetCarSelect() {
        carSelect.innerHTML =
            '<option value="" disabled selected>First Select Car Type</option>';
        carSelect.disabled = true;
        resetCarDetails();
    }

    function resetCarDetails() {
        buyerNameInput.value = "";
        totalPriceInput.value = "";
    }

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
