document.addEventListener('DOMContentLoaded', (event) => {
    // Open Modal
    document.getElementById('openModalBtn').addEventListener('click', () => {
        $('#propertyModal').modal('show');
    });

    // Close Modal
    document.querySelectorAll('[data-dismiss="modal"]').forEach(button => {
        button.addEventListener('click', () => {
            $('#propertyModal').modal('hide');
        });
    });
});
