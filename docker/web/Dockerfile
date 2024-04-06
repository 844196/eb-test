FROM public.ecr.aws/nginx/nginx-unprivileged:1.25.4-alpine as base

COPY --chown=nginx:nginx ./docker/web/nginx.conf /etc/nginx/nginx.conf
COPY --chown=nginx:nginx ./docker/web/default.conf.template /etc/nginx/templates/default.conf.template

ENV NGINX_WORKER_PROCESSES auto
ENV NGINX_WORKER_RLIMIT_NOFILE 4096
ENV NGINX_WORKER_CONNECTIONS 1024
ENV NGINX_LISTEN 8080

# ------------------------------------------------------------------------------

FROM base as live

COPY --chown=nginx:nginx ./public /opt/app/public
