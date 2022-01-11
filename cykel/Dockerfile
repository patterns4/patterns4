FROM node:14

WORKDIR /bike-backend
COPY package.json .
RUN npm install

COPY . .
CMD npm start
