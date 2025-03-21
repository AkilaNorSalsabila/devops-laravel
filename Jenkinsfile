pipeline {
    agent any

    environment {
        GIT_REPO = 'https://github.com/AkilaNorSalsabila/devops-laravel'
        DEPLOY_DIR = '/var/www/devops-laravel'
        COMPOSER_HOME = "${WORKSPACE}/.composer"
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
                            credentialsId: 'github-token'
                        ]]
                    ])
                    sh 'git reset --hard'  // Pastikan workspace selalu bersih
                }
            }
        }

        stage('Install Dependencies') {
            steps {
                sh '''
                    echo "🔄 Updating system packages..."
                    sudo apt-get update || echo "Skipping sudo command"

                    echo "🔎 Checking for Composer..."
                    if ! [ -x "$(command -v composer)" ]; then
                        curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
                    fi

                    echo "📂 Checking for composer.json..."
                    if [ ! -f composer.json ]; then
                        echo "❌ ERROR: composer.json not found!"
                        exit 1
                    fi

                    echo "⚡ Installing PHP dependencies..."
                    composer update --no-dev --optimize-autoloader || exit 1
                '''
            }
        }

        stage('Set Application Key') {
            steps {
                sh '''
                    echo "🔑 Configuring Laravel environment..."
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
                    echo "🚀 Checking if rsync is installed..."
                    if ! [ -x "$(command -v rsync)" ]; then
                        echo "❌ ERROR: rsync not found!"
                        exit 1
                    fi

                    echo "📁 Preparing deployment..."
                    sudo mkdir -p ${DEPLOY_DIR}
                    sudo chown -R jenkins:www-data ${DEPLOY_DIR}
                    sudo chmod -R 775 ${DEPLOY_DIR}

                    echo "📦 Deploying application..."
                    rsync -avz --delete . ${DEPLOY_DIR} || exit 1

                    echo "🔧 Fixing permissions after rsync..."
                    sudo chown -R jenkins:www-data ${DEPLOY_DIR}
                    sudo chmod -R 775 ${DEPLOY_DIR}

                    echo "🛠️ Running post-deployment setup..."
                    cd ${DEPLOY_DIR}
                    
                    if [ -x "$(command -v php)" ] && [ -f artisan ]; then
                        sudo -u jenkins composer install --no-dev --optimize-autoloader || exit 1

                        php artisan config:clear
                        php artisan cache:clear
                        php artisan config:cache
                        php artisan route:cache
                        php artisan view:cache

                        echo "✅ Deployment completed successfully!"
                    else
                        echo "❌ ERROR: Laravel is not properly installed!"
                        exit 1
                    fi
                '''
            }
        }
    }
}
