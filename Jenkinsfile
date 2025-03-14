pipeline {
    agent { label 'docker' }

    stages {
        stage('Install Dependencies') {
            steps {
                script {
                    docker.image('php:8.2').inside {
                        sh 'apt-get update'
                        sh 'apt-get install -y unzip'
                        sh 'composer install'
                    }
                }
            }
        }
        
        stage('Testing') {
            steps {
                script {
                    docker.image('debian').inside {
                        sh 'echo "Ini adalah test"'
                    }
                }
            }
        }

        stage('Deploy to Production') {
            steps {
                script {
                    docker.image('agung3wi/alpine').inside {
                        sh 'mkdir -p ~/.ssh'
                        sh 'ssh-keyscan -H "$PROD_HOST" > ~/.ssh/known_hosts'
                        sh 'rsync -rav --delete ./laravel/ ubuntu@$PROD_HOST:/home/ubuntu/prod.kelasdevops.xyz/'
                    }
                }
            }
        }
    }
}
