# idtcDay1
1. Clone the repository from here.
cd idtcDay1
2. Go into app directory
cd app
3. Install required dependencies
npm install
4. Create docker network
docker network create mongo-network 
5. Start mongodb container
docker run -d -p 27017:27017 -e MONGO_INITDB_ROOT_USERNAME=admin -e MONGO_INITDB_ROOT_PASSWORD=password --name mongodb --net mongo-network mongo    
6. Start mongo-express
docker run -d -p 8081:8081 -e ME_CONFIG_MONGODB_ADMINUSERNAME=admin -e ME_CONFIG_MONGODB_ADMINPASSWORD=password --net mongo-network --name mongo-express -e ME_CONFIG_MONGODB_SERVER=mongodb mongo-express 
7.  Open mongo-express from browser
http://localhost:8081
8. Create user-account database and users collection in mongo-express
9. Make changes in server.js
• Comment line 29
• Remove comment from line 26
• Replace mongoUrlDocker with mongoUrlLocal on line 41 and 71
10. Start your nodejs application
node server.js
11. Access it from the browser
http://localhost:3000
Till this you have successfully deployed your app locally with mongoDB container. Now you need to build your app so that you can push it to dockerhub.
1. Go to docker-hub create an account.
2. Create a repository my-app on docker hub
3.  Go to idtcDay1 directory on your PC
cd ..
4. Undo the changes done in server.js in step 9.
5. Delete node_modules folder from app directory.
6. Edit build my-app image
docker build -t <yourDockerHUbUsername>/my-app:1.0 .
7. Edit the docker-compose.yml  file
25:   image: <yourDockerHUbUsername>/my-app:1.0
7. Start mongodb, mongo-express an my-app
docker-compose -f docker-compose.yaml up
8. You can access the mongo-express under localhost:8080 from your browser
9. In mongo-express UI
• Create a new database my-db
• Create a new collection users in the database my-db
10. Access the node.js application from browser
http://localhost:3000
11.  Push your image to docker hub
docker push <yourDockerHUbUsername>/my-app:1.0 
