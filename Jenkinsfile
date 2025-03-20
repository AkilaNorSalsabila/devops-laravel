pipeline {
    agent any

    environment {
        GIT_REPO = 'https://github.com/AkilaNorSalsabila/devops-laravel'
        DEPLOY_DIR = '/var/www/devops-laravel'
        COMPOSER_HOME = "${WORKSPACE}/.composer"
    }

    stages {
        stage('Cleanup Workspace') {
            steps {
                script {
                    deleteDir()
                }
            }
        }

        stage('Checkout') {
            steps {
                script {
                    checkout([
                        $class: 'GitSCM',
                        branches: [[name: '*/main']],
                        userRemoteConfigs: [[
                            url: "${GIT_REPO}",
                            credentialsId: 'github-token'
                        ]]
                    ])
                }
            }
        }

        stage('Install Dependencies') {
            steps {
                sh '''
                    echo "Updating system packages..."
                    sudo apt-get update || echo "Skipping sudo command"

                    echo "Ensuring Composer is installed..."
                    if ! [ -x "$(command -v composer)" ]; then
                        curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
                    fi

                    echo "Installing PHP dependencies..."
                    composer install --optimize-autoloader --ignore-platform-req=ext-curl --prefer-dist || exit 1
                '''
            }
        }

        stage('Set Application Key') {
            steps {
                sh '''
                    echo "Configuring Laravel environment..."
                    [ ! -f .env ] && cp .env.example .env
                    if ! grep -q "APP_KEY=" .env || [ -z "$(grep 'APP_KEY=' .env | cut -d '=' -f2)" ]; then
                        php artisan key:generate
                        php artisan config:clear
                    fi
                '''
            }
        }

        stage('Deploy to Production') {
            when {
                expression { currentBuild.result == null || currentBuild.result == 'SUCCESS' }
            }
            steps {
                sh '''
                    echo "Preparing deployment..."
                    sudo mkdir -p ${DEPLOY_DIR}
                    sudo chown -R jenkins:jenkins ${DEPLOY_DIR}

                    echo "Deploying application..."
                    rsync -avz --delete . ${DEPLOY_DIR} || exit 1

                    echo "Running post-deployment setup..."
                    cd ${DEPLOY_DIR}
                    sudo -u jenkins composer install --no-dev --optimize-autoloader || exit 1

                    php artisan config:clear
                    php artisan cache:clear
                    php artisan config:cache
                    php artisan route:cache
                    php artisan view:cache

                    echo "Deployment completed successfully!"
                '''
            }
        }
    }
}
