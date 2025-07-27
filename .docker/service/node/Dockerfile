ARG NODE_VER
FROM node:${NODE_VER} AS base
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

USER ${UNAME}

# shell alias
RUN printf "PS1='\u@\h:\w\$ '" >> "${HOME}/.bashrc" \
  && printf "\nalias ll=\"ls -la\"" >> "${HOME}/.bashrc"

# npm latest 10.9.0 is very slow when installing packages
RUN npm install -g npm@10.9.1 @types/node

# remove default binary from node image to use the custom global npm binary

USER root

RUN rm -rf /usr/local/lib/node_modules/npm \
  && find /usr/local/bin -name "np*" | xargs rm -rf

USER ${UNAME}

WORKDIR /app
RUN mkdir -p node_modules
COPY --chown="${UID}:${GID}" package*.json .
COPY --chown="${UID}:${GID}" yarn.lock* .

FROM base AS develop

ENV NODE_ENV=development

# install modules inside image
WORKDIR /app
RUN if [ -f "package.json" ]; then npm install; fi

# COPY --chown=${UID}:${GID} app .

ENTRYPOINT [ "bash" ]

FROM base AS prod

ENV NODE_ENV=production

# install but do not update package-lock
WORKDIR /app
RUN if [ -f "package-lock.json" ]; then npm clean-install && npm cache clean --force; fi

# /usr/local/bin/docker-entrypoint.sh
ENTRYPOINT docker-entrypoint.sh
CMD [ "node" ]
