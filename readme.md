# Projet TodoCo

Dernière analyse Codacy:
[![Codacy Badge](https://app.codacy.com/project/badge/Grade/c719b13284874e0abc9d693fe8a93ac1)](https://app.codacy.com/gh/Djordy59630/todoCo/dashboard?utm_source=gh&utm_medium=referral&utm_content=&utm_campaign=Badge_grade)

## Instructions

### Configuration requise

- Version de PHP : 8.01
- MariaDB : 10.6.5-MariaDB

### Initialisation du projet

1. Cloner ce dépôt.
2. Exécuter la commande `composer install`.
3. Exécuter la commande `yarn install`.
4. Exécuter la commande `yarn build`.

### Configuration de la base de données

1. Modifier le fichier `.env` avec les informations de votre base de données.
2. Exécuter la commande `php bin/console doctrine:database:create`.
3. Exécuter la commande `php bin/console doctrine:migrations:migrate`.

### Ajout de données de test

1. Exécuter la commande `php bin/console doctrine:fixtures:load`.

### Configuration de l'environnement local

1. Configurer le DNS du mailer en fonction de votre environnement local.

Identifiants de connexion pour les compte admin :
- email : admin[0 - 9]@example.com
- Mot de passe : password123

Identifiants de connexion pour les compte admin :
- email : user[0 - 9]@example.com
- Mot de passe : password123

### Tests Unitaire

1. Configurer la base de donnée de test dans le fichier `.env.test`
`
KERNEL_CLASS='App\Kernel'
APP_SECRET='$ecretf0rt3st'
SYMFONY_DEPRECATIONS_HELPER=999999
PANTHER_APP_ENV=panther
PANTHER_ERROR_SCREENSHOT_DIR=./var/error-screenshots

DATABASE_URL="mysql://root:@127.0.0.1:3307/todoco?serverVersion=10.6.5-MariaDB"
`
2. Creer la base de donnée test avec `symfony console doctrine:database:create --env=test`
3. Exécuter la commande `php bin/console doctrine:migrations:migrate --env=test`.
4. lancer les test avec `symfony php bin/phpunit`


Profitez bien ! :smile:
