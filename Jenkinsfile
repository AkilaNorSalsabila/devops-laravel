pipeline {
    agent any

    stages {
        stage('Checkout') {
            steps {
                script {
                    withCredentials([string(credentialsId: 'github-token', variable: 'GITHUB_TOKEN')]) {
                        sh 'git clone https://${GITHUB_TOKEN}@github.com/AkilaNorSalsabila/devops-laravel.git'
                    }
                }
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
                script {
                    withCredentials([sshUserPrivateKey(credentialsId: 'ssh-prod', keyFileVariable: 'SSH_KEY')]) {
                        sh '''
                        mkdir -p ~/.ssh
                        echo "$SSH_KEY" > ~/.ssh/id_rsa
                        chmod 600 ~/.ssh/id_rsa
                        ssh-keyscan -H "$PROD_HOST" >> ~/.ssh/known_hosts
                        rsync -rav --delete ./laravel/ ubuntu@$PROD_HOST:/home/ubuntu/prod.kelasdevops.xyz/
                        '''
                    }
                }
            }
        }
    }
}
