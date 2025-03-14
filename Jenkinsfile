pipeline {
    agent any

    stages {
        stage('Checkout') {
            steps {
                checkout scm
            }
        }

        stage('Install Dependencies') {
            agent {
                docker {
                    image 'php:8.2-cli'
                    args '-u root'
                }
            }
            steps {
                sh 'apt-get update && apt-get install -y unzip git'
                sh 'rm -f composer.lock'
                sh 'composer install --no-interaction --prefer-dist --no-progress'
            }
        }

        stage('Testing') {
            agent {
                docker {
                    image 'debian:latest'
                    args '-u root'
                }
            }
            steps {
                sh 'echo "Menjalankan testing di Debian..."'
                // Tambahkan testing sesuai kebutuhan, contoh PHPUnit atau lainnya
            }
        }

        stage('Deploy to Production') {
            agent {
                docker {
                    image 'agung3wi/alpine-rsync:1.1'
                    args '-u root'
                }
            }
            steps {
                sshagent (credentials: ['ssh-prod']) {
                    sh 'mkdir -p ~/.ssh'
                    sh 'ssh-keyscan -H "$PROD_HOST" > ~/.ssh/known_hosts'
                    sh 'rsync -rav --delete ./laravel/ ubuntu@$PROD_HOST:/home/ubuntu/prod.kelasdevops.xyz/'
                }
            }
        }
    }
}
