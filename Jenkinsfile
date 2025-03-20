pipeline {
    agent any

    environment {
        GIT_REPO = 'https://github.com/AkilaNorSalsabila/devops-laravel'
        DEPLOY_DIR = '/var/www/devops-laravel' // Lokasi deploy yang lebih aman
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
                    echo "Updating system and installing required dependencies..."
                    sudo -n apt-get update || echo "Skipping sudo command"

                    echo "Setting correct permissions for Composer..."
                    mkdir -p ${COMPOSER_HOME}

                    echo "Checking Composer installation..."
                    if ! [ -x "$(command -v composer)" ]; then
                        curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
                    fi

                    echo "Updating Composer..."
                    composer self-update --no-cache

                    echo "Installing Composer dependencies..."
                    composer install --optimize-autoloader --ignore-platform-req=ext-curl || exit 1
                '''
            }
        }

        stage('Run Tests') {
            steps {
                sh '''
                    echo "Running Laravel tests..."
                    set -e  # Jika ada error, langsung keluar
                    if [ -f artisan ]; then
                        php artisan test
                    fi

                    echo "Running PHPUnit tests..."
                    if [ -x vendor/bin/phpunit ]; then
                        ./vendor/bin/phpunit
                    else
                        echo "PHPUnit not found, skipping tests."
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
                    echo "Deploying application..."
                    sudo mkdir -p ${DEPLOY_DIR}
                    sudo chown -R jenkins:jenkins ${DEPLOY_DIR}
                    rsync -avz --delete --exclude '.env' --exclude 'storage/' --exclude 'vendor/' --exclude '.git' . ${DEPLOY_DIR} || exit 1

                    cd ${DEPLOY_DIR}
                    composer install --no-dev --optimize-autoloader || exit 1

                    php artisan config:clear
                    php artisan cache:clear || true
                    php artisan config:cache
                    php artisan route:cache
                    php artisan view:cache || true
                    
                    echo "Deployment completed successfully!"
                '''
            }
        }
    }
}
