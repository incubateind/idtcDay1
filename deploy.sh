#!/bin/bash
set -e

docker login -u="$QUAY_USERNAME" -p="$QUAY_PASSWORD" quay.io
docker tag ${APP_IMAGE} quay.io/${APP_IMAGE}:${TRAVIS_TAG}
docker tag ${APP_IMAGE} quay.io/${APP_IMAGE}:latest
docker tag ${APP_IMAGE} quay.io/keboola/docker-demo-private:${TRAVIS_TAG}
docker tag ${APP_IMAGE} quay.io/keboola/docker-demo-private:latest
docker images
docker push quay.io/${APP_IMAGE}:${TRAVIS_TAG}
docker push quay.io/${APP_IMAGE}:latest
docker push quay.io/keboola/docker-demo-private:${TRAVIS_TAG}
docker push quay.io/keboola/docker-demo-private:latest

docker login -u="$DOCKERHUB_USERNAME" -p="$DOCKERHUB_PASSWORD" https://index.docker.io/v1/
docker tag ${APP_IMAGE} keboolaprivatetest/docker-demo-docker:${TRAVIS_TAG}
docker tag ${APP_IMAGE} keboolaprivatetest/docker-demo-docker:latest
docker images
docker push keboolaprivatetest/docker-demo-docker:${TRAVIS_TAG}
docker push keboolaprivatetest/docker-demo-docker:latest

# taken from https://gist.github.com/BretFisher/14cd228f0d7e40dae085
# install aws cli w/o sudo
pip install --user awscli
# put aws in the path
export PATH=$PATH:$HOME/.local/bin
# needs AWS_ACCESS_KEY_ID and AWS_SECRET_ACCESS_KEY environment variables
eval $(aws ecr get-login --region us-east-1)
docker tag ${APP_IMAGE}:latest 147946154733.dkr.ecr.us-east-1.amazonaws.com/${APP_IMAGE}:${TRAVIS_TAG}
docker tag ${APP_IMAGE}:latest 147946154733.dkr.ecr.us-east-1.amazonaws.com/${APP_IMAGE}:latest
docker push 147946154733.dkr.ecr.us-east-1.amazonaws.com/${APP_IMAGE}:${TRAVIS_TAG}
docker push 147946154733.dkr.ecr.us-east-1.amazonaws.com/${APP_IMAGE}:latest

# needs KBC_DEVELOPERPORTAL_USERNAME, KBC_DEVELOPERPORTAL_PASSWORD and KBC_DEVELOPERPORTAL_URL
docker pull quay.io/keboola/developer-portal-cli-v2:latest
export REPOSITORY=`docker run --rm  \
    -e KBC_DEVELOPERPORTAL_USERNAME -e KBC_DEVELOPERPORTAL_PASSWORD \
    quay.io/keboola/developer-portal-cli-v2:latest ecr:get-repository ${KBC_DEVELOPERPORTAL_VENDOR} ${KBC_DEVELOPERPORTAL_APP}`
docker tag ${APP_IMAGE}:latest ${REPOSITORY}:${TRAVIS_TAG}
docker tag ${APP_IMAGE}:latest ${REPOSITORY}:latest
eval $(docker run --rm \
    -e KBC_DEVELOPERPORTAL_USERNAME -e KBC_DEVELOPERPORTAL_PASSWORD \
    quay.io/keboola/developer-portal-cli-v2:latest ecr:get-login ${KBC_DEVELOPERPORTAL_VENDOR} ${KBC_DEVELOPERPORTAL_APP})
docker push ${REPOSITORY}:${TRAVIS_TAG}
docker push ${REPOSITORY}:latest

# Deploy to KBC -> update the tag in Keboola Developer Portal (needs $KBC_DEVELOPERPORTAL_VENDOR & $KBC_DEVELOPERPORTAL_APP)
docker run --rm -e KBC_DEVELOPERPORTAL_USERNAME -e KBC_DEVELOPERPORTAL_PASSWORD \
    quay.io/keboola/developer-portal-cli-v2:latest update-app-repository ${KBC_DEVELOPERPORTAL_VENDOR} ${KBC_DEVELOPERPORTAL_APP} ${TRAVIS_TAG} ecr ${REPOSITORY}
