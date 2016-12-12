[< table of contents](../README.md)

Getting started is easy. You can follow these steps to setup a new project. If you just want to get it running, you might prefer the [quick and dirty instructions](basic.md).

1. The standard clone and change directory. Switch to the dev branch for the bleeding edge version.

    ```
    git clone git://github.com/cogentParadigm/Starbug.git myapp
    cd myapp
    git checkout dev
    ```

1. If you are setting up a new project, you will most likely want to point to your own repository.

    ```
    git remote rename origin sb
    git remote add origin git://path/to/repo.git
    git push -u origin
    ```

1. If you are setting up a new project, install and commit the libraries.

    ```
    bower install
    git add libraries
    git commit -am "libraries"
    git push
    ```

1. Run composer install and update the generated host configuration:

    ```
    composer install
    ```

    Once generated, update etc/db/default.json with your database credentials and var/etc/di.php with any environment specific configuration (eg. relative URL path if you are installing Starbug in a sub-directory).

1. Run setup:

    ```
    php sb setup
    composer dump-autoload
    ```
