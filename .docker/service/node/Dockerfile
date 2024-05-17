ARG NODE_VER=20.10-alpine
FROM node:${NODE_VER} as base
LABEL autodelete="true"

USER root

RUN apk add --no-cache shadow bash git

# setup User
ARG UID=1000
ARG GID=$UID
ARG UNAME=docker
ARG GNAME=${UNAME}
ARG PASS=

ENV USER=${UNAME}

RUN set -ex \
  && deluser --remove-home node \
  && addgroup --gid "${GID}" "${GNAME}" \
  && adduser --gecos "" --uid "${UID}" --ingroup "${GNAME}" --shell /bin/bash -D "${UNAME}" \
  # set user PW
  && echo "${UNAME}:${PASS}" | chpasswd \
  && chown -R ${UID} /usr/local/lib/node_modules

# exec npm as user
# https://github.com/nodejs/docker-node/blob/main/docs/BestPractices.md#global-npm-dependencies
ENV NPM_CONFIG_PREFIX="/home/${UNAME}/.npm-global"
ENV PATH="${PATH}:${NPM_CONFIG_PREFIX}/bin"

# yarn
RUN corepack enable \
  && corepack prepare yarn@stable --activate

USER "${UID}"

# shell alias
RUN printf "PS1='\u@\h:\w\$ '" >> "${HOME}/.bashrc" \
  && printf "\nalias ll=\"ls -la\"" >> "${HOME}/.bashrc"

RUN npm install -g npm@latest @types/node

WORKDIR /var/www/app
RUN mkdir -p node_modules
COPY --chown="${UID}:${GID}" package*.json .
COPY --chown="${UID}:${GID}" yarn.lock* .

FROM base as develop

ENV NODE_ENV=development

# install modules inside image
WORKDIR /var/www/app
RUN if [ -f "package.json" ]; then npm install; fi

# COPY --chown=${UID}:${GID} app .

ENTRYPOINT [ "bash" ]

FROM base as prod

ENV NODE_ENV=production

# install but do not update package-lock
WORKDIR /var/www/app
RUN npm clean-install && npm cache clean --force

# /usr/local/bin/docker-entrypoint.sh
ENTRYPOINT docker-entrypoint.sh
CMD [ "node" ]
