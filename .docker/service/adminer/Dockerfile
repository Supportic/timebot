ARG ADMINER_VER=latest
FROM shyim/adminerevo:$ADMINER_VER
LABEL autodelete="true"

USER root

COPY index.php /var/www/html/index.php

COPY designs/* /var/www/html/designs/
COPY plugins-enabled/* /var/www/html/plugins-enabled/
