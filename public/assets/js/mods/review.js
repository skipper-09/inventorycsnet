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

document.addEventListener("DOMContentLoaded", function () {
    var reviewModal = document.getElementById('reviewTaskModal');

    reviewModal.addEventListener('shown.bs.modal', function() {
        // If you're using Select2, initialize it after the modal is shown
        if ($.fn.select2) {
            $('#status').select2({
                dropdownParent: $('#reviewTaskModal .modal-body'),
                width: '100%'
            });
        }
    });

    // Reset form when modal is hidden
    reviewModal.addEventListener('hidden.bs.modal', function() {
        document.getElementById('status').value = '';
        document.getElementById('log').value = '';

        // If using Select2, destroy it before hiding
        if ($.fn.select2) {
            try {
                $('#status').select2('destroy');
            } catch (e) {}
        }
    });

    // Configure lightbox
    lightbox.option({
        resizeDuration: 200,
        wrapAround: true,
        albumLabel: "Gambar %1 dari %2",
        fadeDuration: 300,
    });

    // Print functionality
    document
        .querySelector('button[onclick="window.print()"]')
        .addEventListener("click", function (e) {
            e.preventDefault();
            const originalTitle = document.title;
            document.title =
                "{{ $title }} - " + new Date().toLocaleDateString();

            setTimeout(() => {
                window.print();
                document.title = originalTitle;
            }, 100);
        });

    // Animate elements on scroll
    function animateOnScroll() {
        const elements = document.querySelectorAll(".fade-in");
        elements.forEach((element) => {
            const rect = element.getBoundingClientRect();
            if (rect.top < window.innerHeight - 50) {
                element.style.opacity = "1";
                element.style.transform = "translateY(0)";
            }
        });
    }

    animateOnScroll();
    window.addEventListener("scroll", animateOnScroll);
});