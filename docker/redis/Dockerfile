#
# See https://hub.docker.com/_/redis/
# See https://github.com/docker-library/redis/blob/7900c5d31e0b3a4c463c57a8d69cc497d58fbe70/4.0/Dockerfile
#

FROM redis:latest
MAINTAINER LEZ

COPY redis.conf /usr/local/etc/redis/redis.conf
CMD ["redis-server", "/usr/local/etc/redis/redis.conf"]