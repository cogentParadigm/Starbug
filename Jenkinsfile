pipeline {
  agent {
    node {
      label ""
      customWorkspace "starbug/${params.branch}"
    }
  }

  parameters {
    string(name: "branch", defaultValue: "master")
  }

  environment {
    JOB_ID = "${env.JOB_NAME}_${params.branch.replaceAll(/[-\.]/, '_')}"
    UID = sh(script: "id -u ${env.USER}", returnStdout: true).trim()
    GID = sh(script: "id -g ${env.USER}", returnStdout: true).trim()
    PHP_UID = "${env.UID}"
    PHP_GID = "${env.GID}"
    PHP_USER = "${env.USER}"
    PHP_GROUP = sh(script: "id -gn ${env.USER}", returnStdout: true).trim()
  }

  stages {

    stage("Start services") {
      steps {
        sh """
          sed -i'' -e \"s/COMPOSE_PROJECT_NAME=.*/COMPOSE_PROJECT_NAME=${env.JOB_ID}/\" .env
          sed -i'' -e \"s/docker-compose.chrome.yml/docker-compose.chrome-headless.yml/\" .env
          docker volume ls | grep "${env.JOB_ID}_mariadb-data" && docker volume rm "${env.JOB_ID}_mariadb-data"
          docker-compose up -d
          sleep 0.3
          docker-compose exec -T mariadb mysql -e \"DROP DATABASE IF EXISTS starbug; CREATE DATABASE IF NOT EXISTS starbug\"
          docker-compose exec -T mariadb mysql -e \"DROP DATABASE IF EXISTS starbug_test; CREATE DATABASE IF NOT EXISTS starbug_test\"
        """
      }
    }

    stage("Install application") {
      steps {
        sh "docker-compose exec -u ${env.UID}: -T php composer install"

        // Configure default database.
        sh """
          sed -i'' -e 's/\"username\":.*/\"username\":\"root\",/' app/etc/db/default.json
          sed -i'' -e 's/\"password\":.*/\"password\":\"\",/' app/etc/db/default.json
          sed -i'' -e 's/\"db\":.*/\"db\":\"starbug\",/' app/etc/db/default.json
          cat app/etc/db/default.json
        """

        // Configure test database.
        sh """
          sed -i'' -e 's/\"username\":.*/\"username\":"root\",/' app/etc/db/test.json
          sed -i'' -e 's/\"password\":.*/\"password\":\"\",/' app/etc/db/test.json
          sed -i'' -e 's/\"db\":.*/\"db\":\"starbug_test\",/' app/etc/db/test.json
          cat app/etc/db/test.json
        """

        // Setup/migrate database
        sh """
          echo \"root\" | docker-compose exec -u ${env.UID}: -T php php sb setup
          docker-compose exec -u ${env.UID}: -T php composer dump-autoload
        """

        // Migrate test database
        sh """
          docker-compose exec -u ${env.UID}: -T php php sb migrate -t -db=test
          docker-compose exec -u ${env.UID}: -T php composer dump-autoload
        """

        // Populate SMTP settings for mailcatcher
        sh """
          docker-compose exec -u ${env.UID}: -T php php sb store settings id:4 value:no-reply@sb.local.com
          docker-compose exec -u ${env.UID}: -T php php sb store settings id:5 value:mailcatcher
          docker-compose exec -u ${env.UID}: -T php php sb store settings id:6 value:1025
        """

        // Configure behat for docker
        sh """
          sed -i'' -e 's/localhost:1080/mailcatcher:1080/' behat.yml
        """
      }
    }

    stage("Run tests") {
      steps {
        sh "mkdir -p build/logs"
        sh "docker-compose exec -u ${env.UID}: -T php vendor/bin/phpcs --extensions=php --standard=vendor/starbug/standard/phpcs.xml --ignore=views,templates,layouts --report=checkstyle --report-file=build/logs/checkstyle.xml core app modules || true"
        sh "docker-compose exec -u ${env.UID}: -T php vendor/bin/phploc --log-csv build/logs/phploc.csv --quiet --count-tests app core modules"
        sh "docker-compose exec -u ${env.UID}: -T php vendor/bin/phpmd . xml vendor/starbug/standard/phpmd.xml --reportfile build/logs/phpmd.xml --exclude libraries,var,node_modules,vendor || true"
        sh "docker-compose exec -u ${env.UID}: -T php vendor/bin/phpcpd --log-pmd build/logs/pmd-cpd.xml app core modules || true"
        sh "docker-compose exec -u ${env.UID}: -T php vendor/bin/phpunit -c etc/phpunit.xml"
        sh "docker-compose exec -u ${env.UID}: -T php vendor/bin/behat || true"
      }

      post {
        always {
          junit "build/logs/phpunit.xml,build/logs/behat/*.xml"
          recordIssues enabledForFailure: true, aggregatingResults: true, tools: [
            checkStyle(pattern: "build/logs/checkstyle.xml"),
            pmdParser(pattern: "build/logs/phpmd.xml"),
            cpd(pattern: "build/logs/pmd-cpd.xml")
          ]
          step([
            $class: "CloverPublisher",
            cloverReportDir: "build/logs",
            cloverReportFileName: "clover.xml"
          ])
          cobertura coberturaReportFile: "cobertura-coverage.xml", enableNewApi: true, failNoReports: false
          plot csvFileName: "phloc-plot.csv",
            csvSeries: [[
              file: "build/logs/phploc.csv",
              inclusionFlag: "OFF"
            ]],
            group: "PHPLOC",
            title: "Project Size",
            style: "line",
            keepRecords: true,
            numBuilds: "100",
            yaxis: ""
        }
      }
    }

  }

  post {
    always {
      sh "docker-compose down || true"
    }
  }
}