FROM php:8.1-cli-alpine

COPY ./builds/changelogger /changelogger/builds/changelogger
COPY ./LICENSE /changelogger/LICENSE
RUN ln -s /changelogger/builds/changelogger /usr/local/bin/changelogger \
    && mkdir /app
WORKDIR /app
VOLUME ["/app"]
CMD ["changelogger", "new"]
