pipeline {
    agent any  // Gunakan agen apa saja yang tersedia

    environment {
        GIT_REPO = 'https://github.com/AkilaNorSalsabila/devops-laravel.git'
        DEPLOY_DIR = '/home/akilanor/deploy-directory'
    }

    stages {
        stage('Checkout') {
            steps {
                script {
                    checkout([
                        $class: 'GitSCM',
                        branches: [[name: '*/main']],
                        userRemoteConfigs: [[
                            url: "${GIT_REPO}",
                            credentialsId: 'github-token'  // Pastikan ID ini sesuai dengan yang ada di Jenkins
                        ]]
                    ])
                }
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
                sh '''
                    apt-get update && apt-get install -y unzip git
                    rm -f composer.lock
                    composer install --no-interaction --prefer-dist --no-progress
                '''
            }
        }

        stage('Run Tests') {
            steps {
                sh 'echo "Menjalankan unit testing..."'
            }
        }

        stage('Deploy to Production') {
            steps {
                withCredentials([string(credentialsId: 'github-token', variable: 'GITHUB_TOKEN')]) {
                    sh '''
                    echo "Deploying application..."
                    mkdir -p $GIT_REPO
                    rsync -rav --delete . ${GIT_REPO}
                    '''
                }
            }
        }
    }
}
