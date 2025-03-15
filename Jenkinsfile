pipeline {
    agent {
        docker {
            image 'shippingdocker/php-composer:7.4'
            args '-u root'
        }
    }
    stages {
        stage('Build') {
            steps {
                sh 'rm -f composer.lock'
                sh 'composer install'
            }
        }
        stage('Testing') {
            agent {
                docker {
                    image 'ubuntu'
                    args '-u root'
                }
            }
            steps {
                sh 'echo "Ini adalah test"'
            }
        }
    }
}
