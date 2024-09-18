// scripts.js

function todescription(str) {
    return str.charAt(0).toUpperCase() + str.slice(1).toLowerCase();
}

function showTutorialModal() {
    document.getElementById("tutorialmodal").style.display = "flex";
}

function hideTutorialModal() {
    document.getElementById("tutorialmodal").style.display = "none";
}

function closeSuccessMessage() {
    var successMessage = document.querySelector('.success-message');
    successMessage.style.display = 'none';
}

function closeErrorMessage() {
    var successMessage = document.querySelector('.error-message');
    successMessage.style.display = 'none';
}

function validateGMapLink() {
    var gmapLink = document.getElementById('gmap_link').value.trim();
    var validPrefix = 'https://www.google.com/maps/embed?';

    if (gmapLink.startsWith(validPrefix)) {
        return true;
    } else {
        alert('Please provide a valid Google Maps embed link starting with ' + validPrefix);
        return false;
    }
}

function showBHEdit() {
    document.getElementById("bhedit").style.display = "flex";
}

function hideBHEdit() {
    document.getElementById("bhedit").style.display = "none";
}

function showBHImageModal() {
    document.getElementById("bhImageModal").style.display = "flex";
    const profileImage = document.getElementById("profile_image");
    const bhImageModal = document.getElementById("profile_image_modal");
    bhImageModal.src = profileImage.src;
}

function hideBHImageModal() {
    document.getElementById("bhImageModal").style.display = "none";
}

document.getElementById("bhImageForm").addEventListener("submit", function (e) {
    var bhImageInput = document.getElementById("bh_image_input_modal");
    if (bhImageInput.files.length === 0) {
        e.preventDefault();
        alert("Please select a new image before saving.");
    }
});

document.getElementById("bh_image_input_modal").addEventListener("change", function () {
    previewBHImageInModal("bh_image_input_modal");
});

function previewBHImageInModal(inputId) {
    var input = document.getElementById(inputId);
    var bhImageModal = document.getElementById("bh_image_modal");

    if (input.files && input.files[0]) {
        var reader = new FileReader();

        reader.onload = function (e) {
            bhImageModal.src = e.target.result;
        };

        reader.readAsDataURL(input.files[0]);
    }
}

// License Modal Functions
function showLicenseModal() {
    document.getElementById("licenseModal").style.display = "flex";
    const licenseImage = document.getElementById("license_image");
    const licenseImageModal = document.getElementById("license_image_modal");
    licenseImageModal.src = licenseImage.src;
}

function hideLicenseModal() {
    document.getElementById("licenseModal").style.display = "none";
}

document.getElementById("licenseImageForm").addEventListener("submit", function (e) {
    var licenseImageInput = document.getElementById("license_image_input_modal");
    if (licenseImageInput.files.length === 0) {
        e.preventDefault();
        alert("Please select a new image before saving.");
    }
});

document.getElementById("license_image_input_modal").addEventListener("change", function () {
    previewLicenseImageInModal("license_image_input_modal");
});

function previewLicenseImageInModal(inputId) {
    var input = document.getElementById(inputId);
    var licenseImageModal = document.getElementById("license_image_modal");

    if (input.files && input.files[0]) {
        var reader = new FileReader();

        reader.onload = function (e) {
            licenseImageModal.src = e.target.result;
        };

        reader.readAsDataURL(input.files[0]);
    }
}
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    sidebar.classList.toggle('show');
}