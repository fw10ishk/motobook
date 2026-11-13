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
});
