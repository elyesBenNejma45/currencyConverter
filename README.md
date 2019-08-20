# currencyConverter
to install the project please run this commands

php bin/console doctrine:database:create

php bin/console make:entity

php bin/console make:migration

php bin/console doctrine:migrations:migrate

and to update the quotes please run this command

php bin/console app:create-quote
