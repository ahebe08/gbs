// Sélection de tous les liens de la barre latérale
const sidebarLinks = document.querySelectorAll('.sidebar-menu a');

// Fonction pour charger le contenu de la page cible dans le main
function loadPageContent(url) {
    fetch(url)
        .then(response => response.text())
        .then(data => {
            // Mettre à jour le contenu de main avec la réponse de la requête
            document.querySelector('.content').innerHTML = data;
        })
        .catch(error => {
            console.error('Erreur lors du chargement de la page :', error);
        });
}

// Ajouter un écouteur d'événement à chaque lien de la barre latérale
sidebarLinks.forEach(link => {
    link.addEventListener('click', function(e) {
        e.preventDefault(); // Empêcher le comportement par défaut du lien

        const url = this.getAttribute('href'); // Obtenir l'URL du lien cliqué
        localStorage.setItem('lastPage', url); // Enregistrer l'URL dans le localStorage
        loadPageContent(url); // Charger le contenu de la page cible dans le main
    });
});

// Charger la dernière page visitée lors du chargement de la page
document.addEventListener('DOMContentLoaded', function() {
    const lastPage = localStorage.getItem('lastPage');
    if (lastPage) {
        loadPageContent(lastPage);
    }
});

async function searchkeyword() {
    const suggestions = document.querySelector("#suggestions");
    suggestions.innerHTML = "";
    let keyword = document.querySelector("#search-inpt").value;
    // Récupérer le filtre sélectionné
    let filter = document.querySelector('input[name="filter"]:checked').value;

    if (keyword.length > 0) {
        //const req = await fetch(`search.php?keyword=${encodeURIComponent(keyword)}`);
        const req = await fetch(`search.php?query=${encodeURIComponent(keyword)}&filter=${encodeURIComponent(filter)}`);
        const json = await req.json();
        console.log(json);

        if (json.length > 0) {
            if (filter=="product") {
                json.forEach((post) => {
                    suggestions.innerHTML += 
                        '<li><a href="details_prod.php?id=' + encodeURIComponent(post.id_prod) + '&from=s">' + post.desi_prod + '</a></li>';
                });
            }else if (filter=="category") {
                json.forEach((post) => {
                    suggestions.innerHTML += 
                        '<li><a href="details_categ.php?id=' + post.id_categ + '">' + post.lib_categ + '</a></li>';
                }); 
            }

            // Affiche la liste des suggestions
            suggestions.classList.remove('hidden');
        } else {
            // Cache la liste si aucun résultat n'est trouvé
            suggestions.classList.add('hidden');
        }
    } else {
        // Cache la liste si l'entrée de recherche est vide
        suggestions.classList.add('hidden');
    }
}
