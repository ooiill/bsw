#
# See https://hub.docker.com/_/mysql/
# See https://github.com/docker-library/mysql/blob/master/5.7/Dockerfile
#

FROM mysql:5.7
MAINTAINER LEZ

#
# Copy script
#
COPY bootstrap.sh /usr/local/bin/bootstrap
RUN chmod a+x /usr/local/bin/bootstrap

#
# For writable
#
RUN usermod -u 1000 mysql

ENTRYPOINT ["/usr/local/bin/bootstrap"]