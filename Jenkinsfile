pipeline {
    agent any 

    environment {
        GIT_REPO = 'git@github.com:AkilaNorSalsabila/devops-laravel.git'
        SSH_USER = 'ubuntu'
        SSH_HOST = 'prod.kelasdevops.xyz'
        SSH_DIR = "/home/${SSH_USER}/prod.kelasdevops.xyz/"
        SSH_OPTS = '-o StrictHostKeyChecking=no'
    }

    stages {
        stage('Checkout') {
            steps {
                sshagent(['ssh-prod']) {
                    sh '''
                        mkdir -p ~/.ssh
                        ssh-keyscan -H github.com >> ~/.ssh/known_hosts
                        ssh-keyscan -H "$SSH_HOST" >> ~/.ssh/known_hosts
                        git clone $GIT_REPO laravel || (cd laravel && git pull)
                    '''
                }
            }
        }

        stage('Install Dependencies') {
            agent {
                docker {
                    image 'php:8.2'
                    args '--user root'
                }
            }
            steps {
                sh '''
                    set -x
                    apt-get update && apt-get install -y unzip
                    composer install --no-interaction --prefer-dist --no-progress
                '''
            }
        }

        stage('Testing') {
            agent {
                docker {
                    image 'ubuntu'
                    args '--user root'
                }
            }
            steps {
                sh 'echo "Running Tests..."'
                sh 'vendor/bin/phpunit --testdox'
            }
        }

        stage('Deploy to Production') {
            agent {
                docker {
                    image 'agung3wi/alpine-rsync:latest'
                    args '--user root'
                }
            }
            steps {
                sshagent (credentials: ['ssh-prod']) {
                    sh '''
                        set -x
                        mkdir -p ~/.ssh
                        echo "$SSH_PRIVATE_KEY" > ~/.ssh/id_rsa
                        chmod 600 ~/.ssh/id_rsa
                        ssh-keyscan -H "$SSH_HOST" >> ~/.ssh/known_hosts
                        rsync -avz -e "ssh $SSH_OPTS -i ~/.ssh/id_rsa" --delete ./laravel/ $SSH_USER@$SSH_HOST:$SSH_DIR
                    '''
                }
            }
        }
    }
}
