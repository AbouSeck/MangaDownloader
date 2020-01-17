#publicly available docker image "php" on docker hub will be pulled

FROM php:7.2-cli

#creating directory helloworld in container (linux machine)

RUN mkdir /dockerdir
RUN mkdir /dockerdir/manga
WORKDIR /dockerdir/manga

ENV v1 arg1
ENV v2 arg2
ENV v3 arg3

#copying Downloadersequence.php from local directory to container's helloworld folder

COPY Downloadersequence.php /dockerdir/Downloadersequence.php

#running Downaloadersequence.php in container

CMD php -f /dockerdir/Downloadersequence.php $v1 $v2 $v3

VOLUME /dockerdir/manga 
