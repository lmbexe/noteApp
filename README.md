# Bienvenue sur Memoa

Memoa est une application de prise de notes développée en [Vue.js](https://vuejs.org/) et [PHP](https://www.php.net/) par Louis-Marie et Faustine.
Pour directement lancer le projet, rendez-vous à la section de [démarrage](#démarrer-le-projet).

### Fonctionnalités

- Création de compte utilisateur
- Création de notes privées
- Ajout de tags personnalisés
- Possibilité d'épingler/mettre aux favoris ses notes
- Tri par date de création
- Recherche par tag et par titre


## Détails techniques

Avant de nous lancer dans le code, nous avons créé un [Trello](https://trello.com/invite/b/67f67d092775ff1f76d571ed/ATTI6fa344a5056d490a5e5ab2b208839b3007691E05/notes-app) pour la gestion du projet, ainsi qu'une maquette (disponible sur le Trello) pour mettre en forme nos idées.

Le fichier *App.vue* est le fichier principal permettant la mise en lien de toutes les vues en insérant le composant routeur.

La librairie [Vue-Router](https://router.vuejs.org/), combinée à [Axios](https://axios-http.com/fr/), nous permet grâce aux fichiers *index.js* et *api.js* de créer une API interne afin de gérer différentes routes et types de requêtes envoyées, ce qui permet de récupérer des données ou, au contraire, d'en envoyer aussi bien à la base de données que sur l'interface visuelle utilisateur.

Pour se connecter, l'application utilise une vue *login.vue* ainsi qu'un fichier *login.php* pour lier la connexion à la base de données. Pour la création de compte, les fichiers *register.vue* et *register.php* suivent le même principe. Le fichier *lougout.php* gère quant à lui la déconnexion au clic du bouton "Déconnexion" sur l'interface principale.

Le fichier *me.php* permet la récupération des informations de l'utilisateur connecté.

Le fichier *notes.php* gère les différentes requêtes envoyées à l'API interne, utilisant un *switch case* selon la requête demandée par l'utilisateur : récupérer toutes les notes pour le menu, récupérer l'id d'une note pour la modifier, créer une note et lui assigner ses nouvelles valeurs, etc... 

Le fichier *noteCard.vue* sert à mettre en forme le visuel de la note unique (l'aperçu sur le menu). Chaque note est ensuite affichée sur le menu grâce au fichier *home.vue*, qui combine les différents composants (NoteCard, Sidebar) pour l'affichage et qui s'occupe également du tri, des fonctions pour les boutons, etc... Le fichier *note.vue* est là pour l'affichage de l'interface d'édition de note et l'envoi d'informations à *notes.php*.


## Démarrer le projet

Le script MySQL se trouve sur le [Trello](https://trello.com/invite/b/67f67d092775ff1f76d571ed/ATTI6fa344a5056d490a5e5ab2b208839b3007691E05/notes-app). La base doit se nommer "appnote". Pensez ensuite à lancer [Wamp Server](https://www.wampserver.com/) au préalable pour faire fonctionner la base de données.

Ouvrez le dossier du projet dans Visual Studio Code, puis ouvrez un nouveau terminal. Placez vous dans le dossier du projet et entrez `npm run dev`.

Vous pouvez maintenant cliquer sur le lien *localhost* (port 5173) pour vous rendre sur la page de l'application.

### Si vous rencontrez un problème au lancement

Revenez sur l'interface de Visual Studio Code, cliquez dans le terminal et stoppez le projet en appuyant simultanément sur les touches *Ctrl+C* de votre clavier.

Vérifiez que le dossier du projet se nomme bien "noteApp", sinon renommez-le ainsi. 

Si le problème n'est pas ce dernier, entrez dans votre terminal `npm install`, puis `npm install axios` et `npm install vue-router`. Relancez le projet.