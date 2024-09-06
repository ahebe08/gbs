document.addEventListener("DOMContentLoaded", function() {
    const sidebarToggle = document.getElementById("sidebarToggle");
    const sidebar = document.getElementById("sidebar");
    const searchIcon = document.getElementById("searchIcon");
    const searchInput = document.getElementById("searchInput");

    sidebarToggle.addEventListener("click", function(event) {
        event.stopPropagation(); // Empêche la propagation de l'événement de clic
        if (sidebar.style.left === "0px") {
            sidebar.style.left = "-250px";
        } else {
            sidebar.style.left = "0px";
        }
    });

    searchIcon.addEventListener("click", function(event) {
        event.stopPropagation(); // Empêche la propagation de l'événement de clic
        if (searchInput.style.display === "none" || searchInput.style.display === "") {
            searchInput.style.display = "block";
            searchInput.focus();
        } else {
            searchInput.style.display = "none";
        }
    });

    // Ajoute un écouteur d'événement au document pour détecter les clics en dehors de la zone de recherche et de la sidebar
    document.addEventListener("click", function(event) {
        if (searchInput.style.display === "block" && !searchInput.contains(event.target) && !searchIcon.contains(event.target)) {
            searchInput.style.display = "none";
        }

        if (sidebar.style.left === "0px" && !sidebar.contains(event.target) && !sidebarToggle.contains(event.target)) {
            sidebar.style.left = "-250px";
        }
    });

    // Empêche la propagation de l'événement de clic dans la zone de recherche
    searchInput.addEventListener("click", function(event) {
        event.stopPropagation();
    });

    // Empêche la propagation de l'événement de clic dans la sidebar
    sidebar.addEventListener("click", function(event) {
        event.stopPropagation();
    });
});
