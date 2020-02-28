pipeline {
  agent any

  stages {

    stage("Start services") {
      steps {
        sh """
          docker volume ls | grep sb_test_mariadb-data && docker volume rm sb_mariadb-data
          docker volume ls | grep sb_test_webroot && docker volume rm sb_webroot
          docker-compose up -d
          sleep 0.2
        """
      }
    }

    stage("Install application") {
      steps {
        sh "docker-compose exec -T php composer install"

        // Setup default database.
        sh """
          sed -i'' -e 's/\"username\":.*/\"username\":\"root\",/' app/etc/db/default.json
          sed -i'' -e 's/\"password\":.*/\"password\":\"\",/' app/etc/db/default.json
          sed -i'' -e 's/\"db\":.*/\"db\":\"starbug\",/' app/etc/db/default.json
          cat app/etc/db/default.json
        """
        sh "docker-compose exec -T mariadb mysql -e \"DROP DATABASE IF EXISTS starbug; CREATE DATABASE IF NOT EXISTS starbug\""
        sh """
          echo \"root\" | docker-compose exec -T php php sb setup
          docker-compose exec -T php composer dump-autoload
        """

        // Setup test database.
        sh """
          sed -i'' -e 's/\"username\":.*/\"username\":"root\",/' app/etc/db/test.json
          sed -i'' -e 's/\"password\":.*/\"password\":\"\",/' app/etc/db/test.json
          sed -i'' -e 's/\"db\":.*/\"db\":\"starbug_test\",/' app/etc/db/test.json
          cat app/etc/db/test.json
        """
        sh "docker-compose exec -T mariadb mysql -e \"DROP DATABASE IF EXISTS starbug_test; CREATE DATABASE IF NOT EXISTS starbug_test\""
        sh """
          docker-compose exec -T php php sb migrate -t -db=test
          docker-compose exec -T php composer dump-autoload
        """

        // Populate SMTP settings for mailcatcher
        sh """
          docker-compose exec -T php php sb store settings id:4 value:no-reply@sb.local.com
          docker-compose exec -T php php sb store settings id:5 value:mailcatcher
          docker-compose exec -T php php sb store settings id:6 value:1025
        """
      }
    }

    stage("Run tests") {
      steps {
        sh "mkdir -p build/logs"
        sh "docker-compose exec -T php vendor/bin/phpcs --extensions=php --standard=vendor/starbug/standard/phpcs.xml --ignore=views,templates,layouts --report=checkstyle --report-file=build/logs/checkstyle.xml core app modules"
        sh "docker-compose exec -T php vendor/bin/phploc --log-csv build/logs/phploc.csv --quiet --count-tests app core modules"
        sh "docker-compose exec -T php vendor/bin/phpmd . xml vendor/starbug/standard/phpmd.xml --reportfile build/logs/phpmd.xml --exclude libraries,var,node_modules,vendor || true"
        sh "docker-compose exec -T php vendor/bin/phpcpd --log-pmd build/logs/pmd-cpd.xml app core modules || true"
        sh "docker-compose exec -T php vendor/bin/phpunit -c etc/phpunit.xml"
        sh "docker-compose exec -T php vendor/bin/behat"
      }

      post {
        always {
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