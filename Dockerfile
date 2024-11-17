FROM php:8-cli

WORKDIR /app

ENV APP_ENV=dev

RUN apt-get update && apt-get install -y --no-install-recommends \
	zip unzip \
	&& rm -rf /var/lib/apt/lists/*

# Install compmoser
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer;
ENV COMPOSER_ALLOW_SUPERUSER=1
