document.addEventListener('DOMContentLoaded', function() {
    const uploadBtn = document.getElementById('upload-btn');
    const backBtn = document.getElementById('back-btn');
    const modal = document.getElementById('upload-modal');
    const closeModalBtn = document.getElementById('close-modal');
    const submitUploadBtn = document.getElementById('submit-upload');
    const fileInput = document.getElementById('file-input');

    // Open the modal when clicking on the Upload button
    uploadBtn.addEventListener('click', function() {
        modal.style.display = 'block';
    });

    // Close the modal when clicking on the 'x' button
    closeModalBtn.addEventListener('click', function() {
        modal.style.display = 'none';
    });

    // Close the modal when clicking anywhere outside of the modal content
    window.addEventListener('click', function(event) {
        if (event.target == modal) {
            modal.style.display = 'none';
        }
    });

    // Handle the file upload logic
    submitUploadBtn.addEventListener('click', function() {
        if (fileInput.files.length > 0) {
            alert('File uploaded successfully!');
            modal.style.display = 'none';
            // Here you can implement the file upload functionality
            // e.g., send the file to the server using fetch or XMLHttpRequest
        } else {
            alert('Please select a file to upload.');
        }
    });

    // Go back to the main page or previous page
    backBtn.addEventListener('click', function() {
        window.location.href = 'index.html'; // Adjust the path as needed
    });
});