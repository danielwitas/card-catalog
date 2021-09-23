  # base image
FROM node:12.2.0-alpine

# set working directory
WORKDIR /node

# add `/app/node_modules/.bin` to $PATH
ENV PATH /node/node_modules/.bin:$PATH

# install and cache app dependencies
COPY node/package.json /node/package.json
RUN npm install -g @vue/cli
RUN npm install


# start app
CMD ["npm", "run", "serve"]