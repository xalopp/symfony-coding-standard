pipeline {
    agent {
      docker {
        image 'composer/composer'
        args '-v /etc/passwd:/etc/passwd'
      }
    }
    stages {
        stage('prepare') {
            steps {
                sh 'php --version'
                echo 'Install composer'
                sh 'composer install --dev'
                sh 'mkdir -p vendor/squizlabs/php_codesniffer/CodeSniffer/Standards/Symfony'
                sh 'cp -R Sniffs/ vendor/squizlabs/php_codesniffer/CodeSniffer/Standards/Symfony/Sniffs/'
                sh 'cp -R Tests/ vendor/squizlabs/php_codesniffer/CodeSniffer/Standards/Symfony/Tests/'
                sh 'cp ruleset.xml vendor/squizlabs/php_codesniffer/CodeSniffer/Standards/Symfony'
            }
        }
        stage('Test') {
            steps {
                echo 'Testing..'
                sh 'cd vendor/squizlabs/php_codesniffer ; ../../bin/phpunit --filter Symfony_'
                sh './vendor/squizlabs/php_codesniffer/bin/phpcs Sniffs --standard=PHPCS --report=summary -np'
            }
        }
    }
}

