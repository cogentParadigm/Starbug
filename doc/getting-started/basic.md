[< table of contents](../README.md)

This is the quickest path to get Starbug running directly after cloning from the official repository. The full instructions include instructions for moving the project to your own repository and committing the libraries installed through bower.

1. The standard clone and change directory. Switch to the dev branch for the bleeding edge version.

    ```
    git clone git://github.com/cogentParadigm/Starbug.git myapp
    cd myapp
    git checkout dev
    ```

1. Run composer install and update the generated host configuration:

    ```
    composer install
    ```

    Once generated, update etc/db/default.json with your database credentials and var/etc/di.php with any environment specific configuration (eg. relative URL path if you are installing Starbug in a sub-directory).

1. Install libraries.

    ```
    bower install
    ```

1. Run setup:

    ```
    php sb setup
    composer dump-autoload
    ```
