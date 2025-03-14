pipeline {
    agent any

    stages {
        stage('Checkout') {
            steps {
                script {
                    withCredentials([string(credentialsId: 'github-token', variable: 'GITHUB_TOKEN')]) {
                        sh 'git clone --branch main https://$GITHUB_TOKEN@github.com/AkilaNorSalsabila/devops-laravel.git .'
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
                    image 'agung3wi/alpine-rsync:1.1'
                    args '-u root'
                }
            }
            steps {
                sh 'echo "Menjalankan testing di Debian..."'
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
                withCredentials([string(credentialsId: 'github-token', variable: 'GITHUB_TOKEN')]) {
                    sh 'rsync -rav --delete ./laravel/ ubuntu@$PROD_HOST:/home/ubuntu/prod.kelasdevops.xyz/'
                }
            }
        }
    }
}
