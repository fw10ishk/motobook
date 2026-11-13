document.addEventListener("DOMContentLoaded", function () {
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
