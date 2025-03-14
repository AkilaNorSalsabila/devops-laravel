pipeline {
    agent any

    stages {
        stage('Checkout') {
            steps {
                git branch: 'main', url: 'https://github.com/AkilaNorSalsabila/devops-laravel.git'
            }
        }

        stage('Install Dependencies') {
            agent {
                docker {
                    image 'php:8.2-cli'
                    args '--user root'
                }
            }
            steps {
                sh 'apt-get update && apt-get install -y unzip git curl'
                sh 'curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer'
                sh 'rm -f composer.lock'
                sh 'composer install --no-interaction --prefer-dist --no-progress'
            }
        }

        stage('Testing') {
            agent {
                docker {
                    image 'php:8.2-cli'
                    args '--user root'
                }
            }
            steps {
                sh 'echo "Menjalankan testing di Debian..."'
            }
        }

        stage('Deploy to Production') {
            steps {
                sh 'rsync -rav --delete ./ ./deploy-directory/'
            }
        }
    }
}
