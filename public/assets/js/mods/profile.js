$(document).ready(function () {
    //modal title dinamis
    var n = Swal.mixin({
        customClass: {
            confirmButton: "btn btn-label-info btn-wide mx-1",
            denyButton: "btn btn-label-secondary btn-wide mx-1",
            cancelButton: "btn btn-label-danger btn-wide mx-1",
        },
        buttonsStyling: !1,
    });
});

function previewImage(input) {
    const preview = document.getElementById("imagePreview");
    if (input.files && input.files[0]) {
        const reader = new FileReader();

        reader.onload = function (e) {
            preview.src = e.target.result;
            preview.style.display = "block";
        };

        reader.readAsDataURL(input.files[0]);
    } else {
        // Don't reset the preview if no new file is selected
        if (!preview.src.includes("storage/images/user")) {
            preview.src = "#";
            preview.style.display = "none";
        }
    }
}

// Toggle password visibility
document
    .getElementById("togglePassword")
    .addEventListener("click", function () {
        const passwordField = document.getElementById("password");
        const type =
            passwordField.getAttribute("type") === "password"
                ? "text"
                : "password";
        passwordField.setAttribute("type", type);
        this.querySelector("i").classList.toggle("fa-eye");
        this.querySelector("i").classList.toggle("fa-eye-slash");
    });

document
    .getElementById("toggleConfirmPassword")
    .addEventListener("click", function () {
        const passwordField = document.getElementById("password_confirmation");
        const type =
            passwordField.getAttribute("type") === "password"
                ? "text"
                : "password";
        passwordField.setAttribute("type", type);
        this.querySelector("i").classList.toggle("fa-eye");
        this.querySelector("i").classList.toggle("fa-eye-slash");
    });
