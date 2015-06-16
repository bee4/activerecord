### v1.0.1

* 2015-06-11: Move ConnectionEvent in a new namespace
* 2015-06-11: Update event name on DELETE entity action
* 2015-06-11: Update event dispatching for the ElasticSearch connection

### v1.0.0

* 2015-06-03: Rename package to bee4/activerecord
* 2015-06-03: Update README

### v0.0.11

* 2015-06-01: Hotfix - Invalid MagicHandler use

### v0.0.10

* 2015-05-28: Remove standard dev deps to use Phar instead
* 2015-05-28: Use MagicHandler instead of simple Client here
* 2015-05-28: Update bee4/transport to 1.1
* 2015-05-28: Remove version from Readme
* 2015-01-12: Ajout de l'image de statut + lien

### v0.0.9

* 2015-01-12: call_user_func replace fail
* 2015-01-12: Meilleure gestion du type de requête
* 2015-01-12: Code Sniffing update
* 2015-01-12: Intégration de PHPCS
* 2015-01-12: Intégration des règles PHPCS & mise à jour config PHPCI
* 2014-11-07: Spacing
* 2014-11-07: call_user_func performance optimization
* 2014-10-17: SerializableEntity : Gestion du fait qu'un parent peut être nul

### v0.0.8

* 2014-10-06: Mise à jour de la construction par array_walk
* 2014-10-06: Passage au nouveau mode d'events avec bee4/events
* 2014-10-06: Modification addCurlOption par addOption

### v0.0.7

* 2014-10-03: Mise à jour du client HTTP -> Passage à bee4/transport
* 2014-09-15: Ajout d'un accesseur aux commentaires parsés
* 2014-09-15: Nettoyage

### v0.0.6

* 2014-09-17: Ajout d'un appel de ligne récursif pour être sur d'avoir la totalité des données sérializées

### v0.0.5

* 2014-09-04: Mise à jour des dépendances
* 2014-09-04: Oubli d'appel à la méthode send() ce qui bloquait la requête
* 2014-09-04: Ajout d'un refresh automatique
* 2014-09-04: Documentation et normalisation
* 2014-09-04: ignore projet PHPStorm

### v0.0.4

* 2014-08-23: Ajout de la version
* 2014-08-23: Correction d'un bug dans la serialization des objets Parent>Enfant
* 2014-08-23: Correction d'un bug bloquant sur l'utilisation de FileTransaction
* 2014-08-20: Mise à jour des extensions pour éviter les effets de bords de la factory de test

### v0.0.3

* 2014-08-20: Ajout de test unitaires pour valider la serialization des NestedEntity
* 2014-08-20: Ajout d'un condition sur la génération de l'UID dans le JSON

### v0.0.2

* 2014-08-19: Ajout de tests dans la suite pour pouvoir valider les comportements multiples
* 2014-08-19: Modification du comportement de la détection des "Behaviours"

### v0.0.1

* 2014-08-19: Mise à jour des dépendances
* 2014-08-18: Ajout de la configuration de build PHPCI + Mise à jour composer
* 2014-08-18: Ajout de tests sur EntityCollection
* 2014-08-18: Finalisation du test sur le serialize
* 2014-08-18: Ajout de tests sur Property
* 2014-08-18: Black list du répertoire "test" pour la couverture de code
* 2014-08-18: Ajustement et tests des isset/unset/set/get sur un ActiveRecord
* 2014-08-18: @idea
* 2014-08-18: Ajout d'une exception pour éviter de modifier une propriété uniquement "Readable" et lire une propriété uniquement "Writable"
* 2014-08-18: Amélioration de la gestion des "Behaviours"
* 2014-08-18: Ajout du test sur NestedEntity et simplification du test Entity
* 2014-08-18: Ajout de tests unitaires avec des objets Sample pour tous les Behaviours
* 2014-08-18: Check du comportement de l'objet entity par rapport à la connexion
* 2014-08-18: Intégration d'évenements pour la sauvegarde/suppression d'une entité
* 2014-08-14: Ajout d'une connexion type PDO pour sauvegarder les entités
* 2014-08-14: Nom de variable...
* 2014-08-14: Mise en place des premiers tests unitaires
* 2014-08-14: Suppression de la partie suggest du composer qui est finalement obligatoire pour le fonctionnement de la librairie
* 2014-08-14: Détection de dépendance: Passage en isset au lieu de in_array pour des questions de performances
* 2014-08-14: Utilisation des itérateurs pour accéder aux propriétés de l'objet Entity en cours
* 2014-08-14: Normalisation et documentation
* 2014-08-14: isJsonSerializable est une méthode statique
* 2014-08-14: Intégration du client http dans les dépendances directes
* 2014-08-11: "uid" est une propriété privée, elle doit être exportée en JSON
* 2014-08-11: Modification de la gestion du serialize/unserialize
* 2014-08-11: Nettoyage et documentation
* 2014-08-11: Intégration de la contrainte sur Serializable sur l'objet FileTransaction
* 2014-08-11: Ajout de la contrainte JSON sur la connection ElasticSearch
* 2014-08-11: Création des traitements par défaut pour le serialize en JSON et en chaîne de caractère
* 2014-08-11: Intégration des interface dans la logique des comportements d'entités
* 2014-08-11: La propriété parent est maintenant privée car elle ne fait pas partie des données standard, et ne doit pas sortir dans une serialisation par visibilité
* 2014-08-11: Entity UID est maintenant une propriété privé, elle ne sort jamais lors d'une sérialisation JSON standard
* 2014-08-06: Suppression de l'utilisation de JsonTransformer, dépendance intuile
* 2014-08-06: Mise à jour de Composer pour ajouter une suggestion vers bee4/httpclient
* 2014-08-06: Mise à jour des type d'exception utilisés
* 2014-08-06: Passage de _state en private et amélioration du serialize/unserialize
* 2014-08-02: Commentaires...
* 2014-08-02: Refactoring des nom de dépot, les projets spécifiques beebot sont dans beebot/XX
* 2014-08-02: Utilisation du nouveau composant beebot/events
* 2014-08-01: Documentation
* 2014-08-01: Documentation et mise en forme
* 2014-08-01: Suppression des objets remplacés par les transactions
* 2014-08-01: Ajout d'une méthode setState pour éviter de modifier direction _state
* 2014-07-30: Ajout de commentaires sur les méthodes de DocBlockParser
* 2014-07-24: Intégration de la configuration phpDocumentor
* 2014-07-24: Correction du soucis de nom de package + installation des dépendances
* 2014-07-24: Ajout des éléments de bases d'un module composer+README
* 2014-07-24: Transformation de la structure du dépôt
* 2014-07-11: Intégration d'un principe de transaction sur les entités
* 2014-07-11: Création d'une méthode pour éviter la duplication de code + Intégration du serialize d'une entité pour un stockage facile en texte
* 2014-07-07: Meilleure log sur l'erreur de fetchOnyBy
* 2014-07-07: Restructuration des entités (normes de codes...)
* 2014-07-04: Intégration de la méthode ActiveRecord::boot et gestion des Data Source Name
* 2014-06-30: On utilise un "Behaviour" donc on n'accède pas à la propriété directement
* 2014-06-30: Suppression de "use" inutiles
* 2014-06-30: Ajout d'une contrainte sur les relations parent > enfant. Un parent doit être persisted pour que l'enfant soit sauvegardé
* 2014-06-16: Correction d'un bug dans le cas d'une entité child avec parent vide
* 2014-06-16: Mise en forme et nettoyage
* 2014-06-16: Utilisation de l'évent Connection lorsque le client HTTP fait une requête ou une erreur
* 2014-06-16: Ajout d'un nouveau type d'évent spécifique à la partie Connection
* 2014-06-10: Intégration de la logique de Connection et création d'un connecteur ElasticSearch
* 2014-06-10: Mise en forme
* 2014-06-10: Correction d'un bug sur l'appel de in_array
* 2014-06-10: Passage de la méthode de génération de cache en static
* 2014-06-02: Ajout de méthodes magiques pour détecter les comportements d'entité initialisés sur l'objet en cours (Dated, Factory, Child)
* 2014-06-02: Les propriétés statiques ne sont pas traitées comme des propriétés d'entités
* 2014-05-28: Ajustement du comportement de lecteur des propriétés d'entité: Ajout d'un isset, unset et correction de faille de setter / getter
* 2014-05-27: Restructuration du nom des variables
* 2014-05-27: Mise en forme de code (brackets)
* 2014-05-27: Ajout d'une couche commune aux entités et sous entités
* 2014-05-27: FactoryEntity est passé en trait
* 2014-05-27: Mise en forme
* 2014-05-27: FactoryEntity devient un trait au lieu d'une abstract
* 2014-05-27: Mise en forme de code
* 2014-05-23: Intégration de trait à la place d'interface pour le champ parent et le champ date des entités
* 2014-05-23: Modification de la casse de la fonction getIP qui n'était pas camelCase