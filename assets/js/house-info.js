document.addEventListener('DOMContentLoaded', function() {
    const backBtn = document.getElementById('back-btn');

    // Redirects to the previous page or main page
    backBtn.addEventListener('click', function() {
        window.location.href = 'index.html'; // Adjust the path as necessary
    });
});
