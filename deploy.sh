#!/usr/bin/env sh

USERNAME=$(git config user.name)
EMAIL=$(git config user.email)

# abort on errors
set -e

# build
npm run docs:build

# navigate into the build output directory
cd docs/.vuepress/dist

# if you are deploying to a custom domain
# echo 'www.example.com' > CNAME

git init
git config --local user.name "$USERNAME"
git config --local user.email "$EMAIL"
git add -A
git commit -m 'Deploy Changelogger Website'

# if you are deploying to https://<USERNAME>.github.io/<REPO>
git push -f git@github.com:churchtools/changelogger.git main:gh-pages

cd -
