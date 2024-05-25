ARG BASE_IMAGE
FROM ${BASE_IMAGE}

USER root

ARG UNAME=docker

# [Optional SUDO] Let user execute sudo without password prompt.
# RUN echo ${UNAME} ALL=\(root\) NOPASSWD:ALL > /etc/sudoers.d/${UNAME} &&\
#   chmod 0440 /etc/sudoers.d/${UNAME}

# allow user to use root program without password (default: ALL)
# required for docker-init script to initialize docker group
# other programs still require a password
# RUN echo "${UNAME} ALL=(root) PASSWD:ALL, NOPASSWD:/usr/sbin/groupadd,/usr/sbin/usermod" > "/etc/sudoers.d/${UNAME}" \
#   && chmod 0440 "/etc/sudoers.d/${UNAME}"

USER ${UNAME}

# install nvm (setup .bashrc), Node.js
ARG NVM_VERSION=0.39.7
ARG NODE_VERSION=20.10.0
ENV NVM_DIR="/home/${UNAME}/.nvm"

# not supported for NVM
ENV NPM_CONFIG_PREFIX=
# export home only for the current RUN layer for root because of install script
RUN set -ex && mkdir -p "${NVM_DIR}" \
  && export HOME="/home/${UNAME}" \
  && curl -o- https://raw.githubusercontent.com/nvm-sh/nvm/v${NVM_VERSION}/install.sh | bash - \
  && . ~/.nvm/nvm.sh \
  && nvm install "${NODE_VERSION}" \
  && nvm alias default "${NODE_VERSION}" \
  && nvm use default \
  && npm install -g npm@latest @types/node

ENV NODE_PATH="${NVM_DIR}/v${NODE_VERSION}/lib/node_modules"
ENV PATH="${NVM_DIR}/versions/node/v${NODE_VERSION}/bin:$PATH"

# needs to be root again at the end to run docker-outside-docker commands
# USER root

CMD [ "sleep", "infinity" ]
