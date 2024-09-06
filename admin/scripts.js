document.addEventListener("DOMContentLoaded", function() {
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('sidebar');
    const content = document.querySelector('.content');

    // Fonction pour basculer l'état ouvert/fermé de la sidebar
    function toggleSidebar() {
        if (sidebar.style.left === '0px') {
            sidebar.style.left = '-250px';
        } else {
            sidebar.style.left = '0';
        }
    }

    // Écouteur d'événement pour le bouton de toggle
    sidebarToggle.addEventListener('click', function() {
        toggleSidebar();
    });

    // Fonction pour fermer la sidebar si elle est ouverte
    function closeSidebar() {
        if (sidebar.style.left === '0px') {
            sidebar.style.left = '-250px';
        }
    }

    // Écouteur d'événement pour fermer la sidebar lorsqu'on clique ailleurs sur la page
    content.addEventListener('click', function() {
        closeSidebar();
    });

    // Écouteur d'événement pour fermer la sidebar lorsqu'on sélectionne une option du sidebar
    sidebar.addEventListener('click', function(event) {
        if (event.target.tagName === 'A') {
            closeSidebar();
        }
    });

    // Écouteur d'événement pour fermer la sidebar lorsqu'on clique en dehors de celle-ci
    document.addEventListener('click', function(event) {
        if (!sidebar.contains(event.target) && !sidebarToggle.contains(event.target)) {
            closeSidebar();
        }
    });
});