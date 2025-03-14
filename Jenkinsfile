pipeline {
    agent any

    stages {
        stage('Checkout') {
            steps {
                script {
                    withCredentials([string(credentialsId: 'github-token', variable: 'GITHUB_TOKEN')]) {
                        sh 'git clone https://$GITHUB_TOKEN@github.com/AkilaNorSalsabila/devops-laravel.git'
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
                    withCredentials([string(credentialsId: 'github-token', variable: 'GITHUB_TOKEN')]) {
                        sh 'curl -X POST -H "Authorization: token $GITHUB_TOKEN" \
                            -H "Accept: application/vnd.github.v3+json" \
                            https://api.github.com/repos/AkilaNorSalsabila/devops-laravel/actions/workflows/deploy.yml/dispatches \
                            -d "{\\"ref\\":\\"main\\"}"'
                    }
                }
            }
        }
    }
}
