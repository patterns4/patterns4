FROM node:14

WORKDIR /customers
COPY package.json .
RUN npm install

COPY . .
# CMD node runbikes.js