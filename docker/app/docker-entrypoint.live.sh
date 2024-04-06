#!/bin/bash
set -euo pipefail

aws ssm get-parameter \
  --name /eb-test/${ENVIRONMENT}/secret \
  --query Parameter.Value \
  --output text \
  > /opt/app/secret

# "docker-php-entrypoint" はベースイメージで定義されているエントリーポイント
exec docker-php-entrypoint "$@"
