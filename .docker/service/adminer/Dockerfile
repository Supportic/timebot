ARG ADMINER_VER
FROM adminer:$ADMINER_VER
LABEL autodelete="true"

USER root 

COPY index.php /var/www/html/index.php

USER adminer

COPY designs/* /var/www/html/designs/
COPY plugins-enabled/* /var/www/html/plugins-enabled/
