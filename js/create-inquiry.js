document.addEventListener("DOMContentLoaded", function () {
    const inquirySource = document.getElementById("inquiry_source");
    const referralSourceContainer = document.getElementById(
        "referral_person_container",
    );

    inquirySource.addEventListener("change", function () {
        if (inquirySource.value === "referral") {
            referralSourceContainer.classList.remove("d-none");
        } else {
            referralSourceContainer.classList.add("d-none");
        }
    });
});
