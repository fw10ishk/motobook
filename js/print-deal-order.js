document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll(".print-button").forEach((button) => {
        button.addEventListener("click", function () {
            const carId = this.dataset.carId;
            printCarDetails(carId);
        });
    });
});

async function printCarDetails(carId) {
    try {
        const response = await fetch(`/api/new-cars/${carId}/print`);
        const carData = await response.json();

        const printContent = document.getElementById("print-content");

        // Get formatted date and time
        const now = new Date();
        const formattedDateTime = now.toLocaleString("en-IN", {
            year: "numeric",
            month: "2-digit",
            day: "2-digit",
            hour: "2-digit",
            minute: "2-digit",
            hour12: true,
        });
        const dateString = now.toISOString().split("T")[0];
        const filename = `Vehicle_Release_${carData.model_name}_${carData.customer_name}_${dateString}.pdf`;

        // Add print-specific styles
        const styleSheet = document.createElement("style");
        styleSheet.textContent = `
            @page {
                margin: 20mm;
                size: A4;
                
                @top-left {
                    content: "${formattedDateTime}";
                    font-size: 10pt;
                }
                
                @top-right {
                    content: "${filename}";
                    font-size: 10pt;
                }

                @bottom-left {
                    content: "https://motobook.com";
                    font-size: 10pt;
                }

                @bottom-right {
                    content: "Page " counter(page) " of " counter(pages);
                    font-size: 10pt;
                }
            }
        `;
        document.head.appendChild(styleSheet);

        // Enhanced template with proper styling classes
        printContent.innerHTML = `
            <div class="document-info">
                <div>Docket No: ${carData.docket_number}</div>
                <div>Booking Date: ${carData.booking_date}</div>
            </div>

            <div class="dealer-info">
                <p>To,</p>
                <p>${carData.dealer_name}</p>
            </div>

            <h6 class="subject"><strong>Subject:</strong> Please release the vehicle bearing the following</h6>

            <div class="vehicle-details car-details-card">
            <div class="detail">
                    <p><strong>Car:</strong></p>
                    <p>${carData.manufacturer_name} ${carData.model_name}</p>
                </div>
                <div class="detail">
                    <p><strong>Engine No:</strong></p>
                    <p>${carData.engine_no}</p>
                </div>
                <div class="detail">
                    <p><strong>Color:</strong></p>
                    <p>${carData.color}</p>
                </div>
                <div class="detail">
                    <p><strong>Chassis No:</strong></p>
                    <p>${carData.chassis_no}</p>
                </div>
            </div>

            <h6>The Invoice letter has to be proposed as follows:</h6>

            <div class="customer-details">
                <p><strong>Name:</strong> ${carData.customer_name}</p>
                <p><strong>Address-1:</strong> ${carData.customer_address1}</p>
                <p><strong>Address-2:</strong> ${carData.customer_address2}</p>
                <p><strong>Email:</strong> ${carData.email}</p>
                <p><strong>Mobile No:</strong> ${carData.customer_phone}</p>
                <p><strong>Date Of Birth:</strong> ${carData.customer_dob}</p>
                <p><strong>Nominee:</strong> ${carData.nominee_name}-${carData.nominee_relationship}</p>
                <p><strong>Date Of Birth:</strong> ${carData.nominee_dob}</p>
                <p><strong>H.P:</strong> ${carData.hp_name || "PENDING"}</p>
                <p><strong>Scheme:</strong> ${carData.scheme}</p>
                <p><strong>Delivery:</strong> ${carData.delivery_date}</p>
                <p><strong>Accessories:</strong> ${carData.accessories}</p>
                <p><strong>Extra Warranty:</strong> ${carData.ext_warranty}</p>
                <p><strong>Choice Number:</strong> ${carData.choice_number}</p>
            </div>

            <div class="footer">
                <p>For, MOTOBOOK</p>
                <div class="signature">Tahir Mansuri</div>
            </div>
        `;

        window.print();
    } catch (error) {
        console.error("Error printing car details:", error);
        alert("Error printing car details. Please try again.");
    }
}
