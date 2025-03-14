pipeline {
    agent any

    environment {
        GIT_REPO = 'https://github.com/AkilaNorSalsabila/devops-laravel.git'
        DEPLOY_DIR = '/home/akilanor/deploy-directory'
    }

    options {
        // Clean the workspace before the build starts
        cleanWs()
    }

    stages {
        stage('Checkout') {
            steps {
                script {
                    // Configure Git to recognize the workspace as a safe directory
                    sh 'git config --global --add safe.directory /var/jenkins_home/workspace/devops-laravel'
                    // Perform the checkout
                    checkout([
                        $class: 'GitSCM',
                        branches: [[name: 'main']],
                        userRemoteConfigs: [[
                            url: "${GIT_REPO}",
                            credentialsId: 'github-token'
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
                    apt-get update && apt-get install -y unzip git curl
                    curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
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
                    mkdir -p $DEPLOY_DIR
                    rsync -rav --delete . ${DEPLOY_DIR}
                    '''
                }
            }
        }
    }
}
